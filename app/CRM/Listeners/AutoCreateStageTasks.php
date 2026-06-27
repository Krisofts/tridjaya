<?php

namespace App\CRM\Listeners;

use App\CRM\Events\LeadStageChanged;
use App\CRM\Services\TaskService;

class AutoCreateStageTasks
{
    public function __construct(
        protected TaskService $tasks,
    ) {}

    public function handle(LeadStageChanged $event): void
    {
        $lead  = $event->lead;
        $stage = $lead->stage()->with('tasks')->first();

        if (! $stage || $stage->tasks->isEmpty()) {
            return;
        }

        foreach ($stage->tasks as $template) {
            $dueMinutes      = (int) $template->due_after_minutes;
            $reminderMinutes = max($dueMinutes - (int) $template->reminder_before_minutes, 0);

            $this->tasks->create([
                'lead_id'     => $lead->id,
                'user_id'     => $lead->assigned_to,
                'created_by'  => $lead->created_by,
                'title'       => $template->title,
                'description' => $template->description,
                'type'        => $template->type,
                'priority'    => $template->priority,
                'due_at'      => now()->addMinutes($dueMinutes),
                'reminder_at' => now()->addMinutes($reminderMinutes),
            ]);
        }
    }
}