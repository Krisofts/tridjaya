<?php

namespace App\CRM\Services;

use App\CRM\Models\CrmActivity;
use App\CRM\Models\CrmLead;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ActivityService
{
    /**
     * Get activities by lead (timeline)
     */
    public function getByLead(
        int $leadId,
        int $perPage = 15
    ): LengthAwarePaginator {
        return CrmActivity::query()
            ->with(['user', 'stage'])
            ->where('lead_id', $leadId)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create new activity
     */
    public function create(array $data): CrmActivity
    {
        return CrmActivity::create([
            'lead_id' => $data['lead_id'],
            'user_id' => $data['user_id'] ?? auth()->id(),

            'type' => $data['type'],
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,

            // ⭐ NEW FIELDS
            'result' => $data['result'] ?? null,
            'next_follow_up_at' => $data['next_follow_up_at'] ?? null,
            'stage_id' => $data['stage_id'] ?? null,
        ]);
    }

    /**
     * Delete activity
     */
    public function delete(CrmActivity $activity): bool
    {
        return $activity->delete();
    }

    /**
     * Optional: get all activities (admin/report)
     */
    public function getPaginated(
        ?string $type = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        return CrmActivity::query()
            ->with(['lead', 'user', 'stage'])
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * WhatsApp activity shortcut
     */
    public function logWhatsapp(
        CrmLead $lead,
        string $notes,
        ?string $result = null,
        ?string $nextFollowUp = null,
        ?int $stageId = null
    ): CrmActivity {
        return CrmActivity::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),

            'type' => CrmActivity::TYPE_WHATSAPP,
            'title' => 'WhatsApp Activity',
            'description' => $notes,

            'result' => $result,
            'next_follow_up_at' => $nextFollowUp,
            'stage_id' => $stageId,
        ]);
    }
}