<?php

namespace App\CRM\Services;

use App\CRM\Models\CrmActivity;
use App\CRM\Models\CrmLead;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ActivityService
{
    // -------------------------------------------------------------------------
    // QUERIES
    // -------------------------------------------------------------------------

    public function getByLead(int $leadId, int $perPage = 15): LengthAwarePaginator
    {
        return CrmActivity::query()
            ->with(['user', 'stage'])
            ->where('lead_id', $leadId)
            ->latest()
            ->paginate($perPage);
    }

    public function getPaginated(?string $type = null, int $perPage = 10): LengthAwarePaginator
    {
        return CrmActivity::query()
            ->with(['lead', 'user', 'stage'])
            ->when($type, fn ($q) => $q->where('type', $type))
            ->latest()
            ->paginate($perPage);
    }

    // -------------------------------------------------------------------------
    // CREATE
    // -------------------------------------------------------------------------

    public function create(array $data): CrmActivity
    {
        return CrmActivity::create([
            'lead_id'          => $data['lead_id'],
            'user_id'          => $data['user_id']          ?? Auth::id(),
            'type'             => $data['type'],
            'title'            => $data['title']            ?? null,
            'description'      => $data['description']      ?? null,
            'result_id'        => $data['result_id']        ?? null,
            'next_follow_up_at'=> $data['next_follow_up_at']?? null,
            'stage_id'         => $data['stage_id']         ?? null,
        ]);
    }

    // -------------------------------------------------------------------------
    // DELETE
    // -------------------------------------------------------------------------

    public function delete(CrmActivity $activity): bool
    {
        return (bool) $activity->delete();
    }

    // -------------------------------------------------------------------------
    // SHORTCUTS
    // -------------------------------------------------------------------------

    public function logWhatsapp(
        CrmLead $lead,
        string  $notes,
        ?int    $resultId     = null,
        ?string $nextFollowUp = null,
        ?int    $stageId      = null,
    ): CrmActivity {
        return $this->create([
            'lead_id'          => $lead->id,
            'type'             => CrmActivity::TYPE_WHATSAPP,
            'title'            => 'WhatsApp Activity',
            'description'      => $notes,
            'result_id'        => $resultId,
            'next_follow_up_at'=> $nextFollowUp,
            'stage_id'         => $stageId,
        ]);
    }
}