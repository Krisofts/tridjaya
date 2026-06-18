<?php

namespace App\CRM\Observers;

use App\CRM\Models\CrmLead;
use App\CRM\Services\TaskService;
use App\CRM\Services\ActivityService;

class CrmLeadObserver
{
    /**
     * Handle the CrmLead "created" event.
     */
    public function created(CrmLead $lead): void
    {
        // 1. Activity: Lead Created
        app(ActivityService::class)->create([
            'lead_id' => $lead->id,
            'user_id' => $lead->created_by,
            'type' => 'lead_created',
            'title' => 'Lead Created',
            'description' => 'Lead berhasil dibuat di sistem.',
        ]);

        // 2. Auto Task: Initial Follow Up
        app(TaskService::class)->createInitialFollowUp(
            $lead->id,
            $lead->assigned_to ?? null
        );
    }
}