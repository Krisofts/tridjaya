<?php

namespace App\CRM\Services;

use App\CRM\Models\CrmActivityResult;
use App\CRM\Models\CrmActivityType;
use App\CRM\Models\CrmLeadActivity;
use App\CRM\Models\CrmLead;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ActivityService
{
    // -------------------------------------------------------------------------
    // READ
    // -------------------------------------------------------------------------

    /**
     * Ambil daftar aktivitas satu lead, urut terbaru.
     */
    public function listByLead(CrmLead $lead, int $perPage = 15): LengthAwarePaginator
    {
        return CrmLeadActivity::query()
            ->with(['type', 'result', 'user', 'stage'])
            ->byLead($lead->id)
            ->latestFirst()
            ->paginate($perPage);
    }

    /**
     * Ambil semua jenis aktivitas yang aktif untuk dropdown form.
     */
    public function getActiveTypes()
    {
        return CrmActivityType::active()->ordered()->with([
            'results' => fn ($q) => $q->active()->ordered(),
        ])->get();
    }

    /**
     * Ambil hasil aktivitas berdasarkan type — untuk cascade dropdown.
     */
    public function getResultsByType(int $activityTypeId)
    {
        return CrmActivityResult::active()
            ->byType($activityTypeId)
            ->ordered()
            ->get(['id', 'name', 'slug', 'is_success', 'is_default']);
    }

    // -------------------------------------------------------------------------
    // CREATE
    // -------------------------------------------------------------------------

    /**
     * Catat aktivitas baru pada lead.
     *
     * Setelah aktivitas disimpan:
     * - last_activity_at lead diupdate
     * - next_follow_up_at lead diupdate jika dikirim
     */
    public function create(CrmLead $lead, array $data): CrmLeadActivity
    {
        return DB::transaction(function () use ($lead, $data) {
            $user = Auth::user();

            // Validasi result harus milik type yang sama
            if (isset($data['activity_result_id']) && $data['activity_result_id']) {
                $result = CrmActivityResult::find($data['activity_result_id']);
                if ($result && $result->activity_type_id !== (int) $data['activity_type_id']) {
                    throw ValidationException::withMessages([
                        'activity_result_id' => 'Hasil aktivitas tidak sesuai dengan jenis aktivitas.',
                    ]);
                }
            }

            $activity = CrmLeadActivity::create([
                'lead_id'            => $lead->id,
                'activity_type_id'   => $data['activity_type_id'],
                'activity_result_id' => $data['activity_result_id'] ?? null,
                'user_id'            => $data['user_id'] ?? $user->id,
                'activity_at'        => $data['activity_at'] ?? now(),
                'title'              => $data['title'],
                'notes'              => $data['notes'] ?? null,
                'location'           => $data['location'] ?? null,
                'stage_id'           => $lead->stage_id,
                'is_contacted'       => $data['is_contacted'] ?? false,
            ]);

            // Update last_activity_at di lead
            $leadUpdates = ['last_activity_at' => now()];

            // Update next_follow_up_at jika dikirim
            if (array_key_exists('next_follow_up_at', $data)) {
                $leadUpdates['next_follow_up_at'] = $data['next_follow_up_at'];
            }

            $lead->update($leadUpdates);

            return $activity->load(['type', 'result', 'user', 'stage']);
        });
    }

    // -------------------------------------------------------------------------
    // UPDATE
    // -------------------------------------------------------------------------

    /**
     * Update aktivitas yang sudah ada.
     */
    public function update(CrmLeadActivity $activity, array $data): CrmLeadActivity
    {
        return DB::transaction(function () use ($activity, $data) {
            $fillable = [
                'activity_type_id',
                'activity_result_id',
                'user_id',
                'activity_at',
                'title',
                'notes',
                'location',
                'is_contacted',
            ];

            // Validasi result konsisten dengan type
            $typeId = $data['activity_type_id'] ?? $activity->activity_type_id;
            if (isset($data['activity_result_id']) && $data['activity_result_id']) {
                $result = CrmActivityResult::find($data['activity_result_id']);
                if ($result && $result->activity_type_id !== (int) $typeId) {
                    throw ValidationException::withMessages([
                        'activity_result_id' => 'Hasil aktivitas tidak sesuai dengan jenis aktivitas.',
                    ]);
                }
            }

            $activity->update(
                collect($data)->only($fillable)->toArray()
            );

            return $activity->fresh(['type', 'result', 'user', 'stage']);
        });
    }

    // -------------------------------------------------------------------------
    // DELETE
    // -------------------------------------------------------------------------

    /**
     * Hapus aktivitas.
     */
    public function delete(CrmLeadActivity $activity): void
    {
        $activity->delete();
    }
}