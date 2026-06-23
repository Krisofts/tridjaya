<?php

namespace App\CRM\Listeners;

use App\CRM\Events\LeadStageChanged;
use App\CRM\Services\TaskService;

class AutoCreateStageTasks
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    public function handle(LeadStageChanged $event): void
    {
        $lead = $event->lead;

        $stage = $lead->stage()
            ->with('tasks')
            ->first();

        if (! $stage) {
            return;
        }

        foreach ($stage->tasks as $rule) {

            $this->taskService->create([
                'lead_id' => $lead->id,
                'user_id' => $lead->assigned_to,
                'created_by' => $lead->created_by,

                'title' => $rule->title,
                'description' => $rule->description,

                'type' => $rule->type,
                'priority' => $rule->priority,

                'due_at' => now()->addMinutes(
                    $rule->due_after_minutes
                ),

                'reminder_at' => now()->addMinutes(
                    max(
                        $rule->due_after_minutes -
                        $rule->reminder_before_minutes,
                        0
                    )
                ),
            ]);
        }
    }
}