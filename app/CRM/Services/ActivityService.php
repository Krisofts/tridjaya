<?php

namespace App\CRM\Services;

use App\CRM\Models\CrmActivity;
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
            ->with('user')
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

            'meta' => $data['meta'] ?? null,
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
     * Optional: get all activities (for admin report)
     */
    public function getPaginated(
        ?string $type = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        return CrmActivity::query()
            ->with(['lead', 'user'])
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->latest()
            ->paginate($perPage);
    }
}
