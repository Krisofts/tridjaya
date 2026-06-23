<?php

namespace App\CRM\Listeners;

use App\CRM\Events\LeadCreated;
use App\CRM\Services\TaskService;

class AutoCreateTask
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    /**
     * Handle Lead Created Event
     */
    public function handle(LeadCreated $event): void
    {
        $lead = $event->lead;

        // hindari error kalau assigned kosong
        $assignedTo = $lead->assigned_to ?? null;

        $this->taskService->create([
            'lead_id' => $lead->id,
            'user_id' => $assignedTo,
            'created_by' => $lead->created_by,

            'title' => 'Follow Up Lead Baru: ' . $lead->name,
            'description' => 'Segera hubungi calon konsumen lewat whatsapp/telephone! 🔥',

            'type' => 'follow_up',
            'priority' => 'urgent',

            'due_at' => now()->addMinutes(1),
            'reminder_at' => now()->addMinutes(15),
        ]);
    }
}