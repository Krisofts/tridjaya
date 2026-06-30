<?php

namespace App\CRM\Services;

use App\CRM\Models\CrmActivityResult;
use App\CRM\Models\CrmActivityType;
use App\CRM\Models\CrmLead;
use App\CRM\Models\CrmLostReason;
use App\CRM\Models\CrmPipeline;
use App\CRM\Models\CrmPipelineStage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LeadService
{
    // -------------------------------------------------------------------------
    // READ
    // -------------------------------------------------------------------------

    public function list(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $user = Auth::user();

        return CrmLead::query()
            ->with([
                'pipeline',
                'stage',
                'source',
                'product',
                'assignedUser',
                'province',
                'regency',
                'district',
                'lostReason',
            ])
            ->when(
                $user->branch_id,
                fn ($q) => $q->byBranch($user->branch_id)
            )
            ->when(
                isset($filters['pipeline_id']),
                fn ($q) => $q->byPipeline($filters['pipeline_id'])
            )
            ->when(
                isset($filters['stage_id']),
                fn ($q) => $q->where('stage_id', $filters['stage_id'])
            )
            ->when(
                isset($filters['source_id']),
                fn ($q) => $q->where('source_id', $filters['source_id'])
            )
            ->when(
                isset($filters['product_id']),
                fn ($q) => $q->where('product_id', $filters['product_id'])
            )
            ->when(
                isset($filters['assigned_to']),
                fn ($q) => $q->assignedTo($filters['assigned_to'])
            )
            ->when(
                isset($filters['status']),
                fn ($q) => $q->where('status', $filters['status'])
            )
            ->when(
                ! empty($filters['search']),
                fn ($q) => $q->where(function ($q) use ($filters) {
                    $q->where('name', 'like', "%{$filters['search']}%")
                        ->orWhere('phone', 'like', "%{$filters['search']}%");
                })
            )
            ->latest()
            ->paginate($perPage);
    }

    // -------------------------------------------------------------------------
    // CREATE
    // -------------------------------------------------------------------------

    public function create(array $data): CrmLead
    {
        return DB::transaction(function () use ($data) {
            $pipeline = CrmPipeline::with('defaultStage')
                ->findOrFail($data['pipeline_id']);

            if (! $pipeline->defaultStage) {
                throw ValidationException::withMessages([
                    'pipeline_id' => 'Pipeline belum memiliki default stage.',
                ]);
            }

            $user = Auth::user();

            $lead = CrmLead::create([
                'pipeline_id'        => $pipeline->id,
                'stage_id'           => $pipeline->defaultStage->id,
                'name'               => $data['name'],
                'phone'              => $data['phone'],
                'product_id'         => $data['product_id'] ?? null,
                'source_id'          => $data['source_id'] ?? null,
                'assigned_to'        => $data['assigned_to'] ?? $user->id,
                'created_by'         => $user->id,
                'branch_id'          => $user->branch_id,
                'province_id'        => $data['province_id'] ?? null,
                'regency_id'         => $data['regency_id'] ?? null,
                'district_id'        => $data['district_id'] ?? null,
                'address'            => $data['address'] ?? null,
                'estimated_value'    => $data['estimated_value'] ?? 0,
                'probability'        => $pipeline->defaultStage->probability,
                'next_follow_up_at'  => $data['next_follow_up_at'] ?? null,
                'status'             => CrmLead::STATUS_OPEN,
            ]);

            $this->recordStageHistory(
                lead: $lead,
                fromStageId: null,
                toStageId: $lead->stage_id,
                changedBy: $user->id,
            );

            return $lead;
        });
    }

    // -------------------------------------------------------------------------
    // UPDATE
    // -------------------------------------------------------------------------

    public function update(CrmLead $lead, array $data): CrmLead
    {
        return DB::transaction(function () use ($lead, $data) {
            $fillable = [
                'name', 'phone',
                'product_id', 'source_id',
                'assigned_to',
                'province_id', 'regency_id', 'district_id', 'address',
                'estimated_value',
                'next_follow_up_at',
            ];

            $lead->update(
                collect($data)->only($fillable)->toArray()
            );

            return $lead->fresh();
        });
    }

    // -------------------------------------------------------------------------
    // STAGE MANAGEMENT
    // -------------------------------------------------------------------------

    public function moveToStage(CrmLead $lead, int $stageId): CrmLead
    {
        return DB::transaction(function () use ($lead, $stageId) {
            $stage = CrmPipelineStage::where('pipeline_id', $lead->pipeline_id)
                ->findOrFail($stageId);

            if ($lead->stage_id === $stage->id) {
                return $lead;
            }

            $fromStageId = $lead->stage_id;

            $lead->update([
                'stage_id'    => $stage->id,
                'probability' => $stage->probability,
            ]);

            $this->recordStageHistory(
                lead: $lead,
                fromStageId: $fromStageId,
                toStageId: $stage->id,
                changedBy: Auth::id(),
            );

            return $lead->fresh();
        });
    }

    // -------------------------------------------------------------------------
    // STATUS LIFECYCLE
    // -------------------------------------------------------------------------

    /**
     * Tandai lead sebagai Won + catat closing activity otomatis.
     */
    public function markWon(CrmLead $lead): CrmLead
    {
        $this->ensureLeadIsOpen($lead);

        return DB::transaction(function () use ($lead) {
            $lead->update([
                'status'      => CrmLead::STATUS_WON,
                'probability' => 100,
                'closed_at'   => now(),
            ]);

            $this->recordClosingActivity(
                lead     : $lead,
                resultSlug: 'lead-won',
                title    : 'Lead ditandai Won',
                notes    : null,
            );

            return $lead->fresh();
        });
    }

    /**
     * Tandai lead sebagai Lost + catat closing activity otomatis.
     */
    public function markLost(
        CrmLead $lead,
        ?int    $lostReasonId = null,
        ?string $lostNote = null,
    ): CrmLead {
        $this->ensureLeadIsOpen($lead);

        return DB::transaction(function () use ($lead, $lostReasonId, $lostNote) {
            $lead->update([
                'status'         => CrmLead::STATUS_LOST,
                'probability'    => 0,
                'closed_at'      => now(),
                'lost_reason_id' => $lostReasonId,
                'lost_note'      => $lostNote,
            ]);

            // Susun catatan activity dari reason + note
            $reasonName = $lostReasonId
                ? CrmLostReason::find($lostReasonId)?->name
                : null;

            $notes = collect([$reasonName, $lostNote])->filter()->implode(' — ');

            $this->recordClosingActivity(
                lead     : $lead,
                resultSlug: 'lead-lost',
                title    : 'Lead ditandai Lost' . ($reasonName ? ": {$reasonName}" : ''),
                notes    : $notes ?: null,
            );

            return $lead->fresh();
        });
    }

    /**
     * Buka kembali lead Won / Lost + catat reopen activity otomatis.
     */
    public function reopen(CrmLead $lead): CrmLead
    {
        if ($lead->isOpen()) {
            return $lead;
        }

        return DB::transaction(function () use ($lead) {
            $lead->update([
                'status'         => CrmLead::STATUS_OPEN,
                'closed_at'      => null,
                'lost_reason_id' => null,
                'lost_note'      => null,
            ]);

            $this->recordClosingActivity(
                lead     : $lead,
                resultSlug: 'lead-reopen',
                title    : 'Lead dibuka kembali',
                notes    : null,
            );

            return $lead->fresh();
        });
    }

    /**
     * Catat waktu aktivitas terakhir pada lead.
     */
    public function touchLastActivity(CrmLead $lead): CrmLead
    {
        $lead->update(['last_activity_at' => now()]);

        return $lead->fresh();
    }

    // -------------------------------------------------------------------------
    // DELETE & RESTORE
    // -------------------------------------------------------------------------

    public function delete(CrmLead $lead): void
    {
        $lead->delete();
    }

    public function restore(int $id): CrmLead
    {
        $lead = CrmLead::withTrashed()->findOrFail($id);
        $lead->restore();

        return $lead->fresh();
    }

    public function forceDelete(int $id): void
    {
        CrmLead::withTrashed()->findOrFail($id)->forceDelete();
    }

    // -------------------------------------------------------------------------
    // PRIVATE HELPERS
    // -------------------------------------------------------------------------

    private function recordStageHistory(
        CrmLead $lead,
        ?int    $fromStageId,
        int     $toStageId,
        int     $changedBy,
    ): void {
        $lead->stageHistories()->create([
            'from_stage_id' => $fromStageId,
            'to_stage_id'   => $toStageId,
            'changed_by'    => $changedBy,
            'changed_at'    => now(),
        ]);
    }

    /**
     * Catat closing activity sistem — Won / Lost / Reopen.
     *
     * Memakai activity type slug 'sistem' yang is_active=false
     * sehingga tidak muncul di dropdown form sales.
     * Jika type 'sistem' belum ada di DB, skip secara diam-diam.
     */
    private function recordClosingActivity(
        CrmLead $lead,
        string  $resultSlug,
        string  $title,
        ?string $notes,
    ): void {
        $type = CrmActivityType::where('slug', 'sistem')->first();

        if (! $type) {
            return; // Seeder belum dijalankan — skip, tidak throw error
        }

        $result = CrmActivityResult::where('activity_type_id', $type->id)
            ->where('slug', $resultSlug)
            ->first();

        $lead->activities()->create([
            'activity_type_id'   => $type->id,
            'activity_result_id' => $result?->id,
            'user_id'            => Auth::id(),
            'activity_at'        => now(),
            'title'              => $title,
            'notes'              => $notes,
            'stage_id'           => $lead->stage_id,
            'is_contacted'       => false,
        ]);

        // Update last_activity_at
        $lead->update(['last_activity_at' => now()]);
    }

    private function ensureLeadIsOpen(CrmLead $lead): void
    {
        if (! $lead->isOpen()) {
            throw ValidationException::withMessages([
                'status' => "Lead sudah berstatus '{$lead->status}'. Gunakan reopen() terlebih dahulu.",
            ]);
        }
    }
}