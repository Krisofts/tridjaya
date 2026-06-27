<?php

namespace App\CRM\Services;

use App\CRM\Events\LeadCreated;
use App\CRM\Events\LeadStageChanged;
use App\CRM\Filters\LeadFilter;
use App\CRM\Models\CrmLead;
use App\CRM\Models\CrmPipelineStage;
use App\CRM\Services\LeadStageService;
use App\Services\RegionService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class LeadService
{
    public function __construct(
        protected LeadFilter      $filter,
        protected RegionService   $region,
        protected LeadStageService $stageService,
    ) {}

    // -------------------------------------------------------------------------
    // QUERY / PAGINATION
    // -------------------------------------------------------------------------

    public function getPaginated(
        ?string $search = null,
        ?int $sourceId = null,
        ?int $pipelineId = null,
        ?string $temperature = null,
        ?int $assignedTo = null,
        int $perPage = 15,
    ): LengthAwarePaginator {
        $query = CrmLead::query()->with([
            'source',
            'pipeline',
            'stage',
            'interest',
            'assignee',
            'creator',
            'branch',
        ]);

        $query = $this->filter->apply($query, [
            'search'      => $search,
            'source_id'   => $sourceId,
            'pipeline_id' => $pipelineId,
            'temperature' => $temperature,
            'assigned_to' => $assignedTo,
        ]);

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    // -------------------------------------------------------------------------
    // FIND
    // -------------------------------------------------------------------------

    public function find(int $id): CrmLead
    {
        return CrmLead::query()
            ->with([
                'source',
                'pipeline',
                'stage',
                'activities.user',
                'assignee',
                'creator',
                'branch',
            ])
            ->findOrFail($id);
    }

    // -------------------------------------------------------------------------
    // CREATE
    // -------------------------------------------------------------------------

    public function create(array $data): CrmLead
    {
        return DB::transaction(function () use ($data) {
            $data['lead_code'] = $this->generateLeadCode();

            $firstStage = CrmPipelineStage::query()
                ->where('pipeline_id', $data['pipeline_id'])
                ->orderBy('sort_order')
                ->orderBy('id')
                ->first();

            if (! $firstStage) {
                throw new \RuntimeException(
                    "Pipeline #{$data['pipeline_id']} belum memiliki stage."
                );
            }

            $data['pipeline_stage_id'] = $firstStage->id;

            // Otomatis assign ke user yang sedang login
            $data['assigned_to'] = auth()->id();

            // Otomatis simpan creator
            $data['created_by'] = auth()->id();

            $data = array_merge($data, $this->region->resolve(
                $data['province_code'] ?? null,
                $data['city_code'] ?? null,
                $data['district_code'] ?? null,
            ));

            $lead = CrmLead::create($data);

            event(new LeadCreated($lead));

            return $lead;
        });
    }

    // -------------------------------------------------------------------------
    // UPDATE
    // -------------------------------------------------------------------------

    public function update(CrmLead $lead, array $data): CrmLead
    {
        return DB::transaction(function () use ($lead, $data) {
            $oldStageId   = $lead->pipeline_stage_id;
            $stageChanged = false;

            if (
                isset($data['pipeline_id']) &&
                (int) $data['pipeline_id'] !== (int) $lead->pipeline_id
            ) {
                $defaultStage = CrmPipelineStage::query()
                    ->where('pipeline_id', $data['pipeline_id'])
                    ->where('is_default', true)
                    ->first();

                if (! $defaultStage) {
                    throw new \RuntimeException(
                        "Pipeline #{$data['pipeline_id']} tidak memiliki stage default."
                    );
                }

                $data['pipeline_stage_id'] = $defaultStage->id;
                $stageChanged = true;
            }

            // Resolve nama wilayah — pakai nilai baru kalau ada, fallback ke nilai lama
            $data = array_merge($data, $this->region->resolve(
                $data['province_code'] ?? $lead->province_code,
                $data['city_code']     ?? $lead->city_code,
                $data['district_code'] ?? $lead->district_code,
            ));

            $lead->update($data);

            $lead = $lead->fresh([
                'source',
                'pipeline',
                'stage',
                'assignee',
                'creator',
                'branch',
            ]);

            if ($stageChanged) {
                event(new LeadStageChanged(
                    lead: $lead,
                    oldStageId: $oldStageId,
                    newStageId: $lead->pipeline_stage_id,
                ));
            }

            return $lead;
        });
    }

    // -------------------------------------------------------------------------
    // CHANGE STAGE
    // -------------------------------------------------------------------------

    public function changeStage(CrmLead $lead, int $stageId): CrmLead
    {
        return $this->stageService->changeStage($lead, $stageId);
    }

    // -------------------------------------------------------------------------
    // DELETE
    // -------------------------------------------------------------------------

    public function delete(CrmLead $lead): bool
    {
        return $lead->delete() !== false;
    }

    // -------------------------------------------------------------------------
    // PRIVATE
    // -------------------------------------------------------------------------

    /**
     * Generate lead code yang unik per tahun.
     * Format : LD-2026-00001
     * Harus dipanggil di dalam DB::transaction() agar lockForUpdate() efektif.
     */
    private function generateLeadCode(): string
    {
        $year = now()->format('Y');

        $lastCode = CrmLead::query()
            ->where('lead_code', 'like', "LD-{$year}-%")
            ->lockForUpdate()
            ->max('lead_code');

        $nextNumber = $lastCode
            ? (int) substr($lastCode, strrpos($lastCode, '-') + 1) + 1
            : 1;

        return sprintf('LD-%s-%05d', $year, $nextNumber);
    }
}
