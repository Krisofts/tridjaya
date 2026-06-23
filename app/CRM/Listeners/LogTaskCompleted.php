<?php

namespace App\CRM\Listeners;

use App\CRM\Events\TaskCompleted;
use App\CRM\Services\ActivityService;
use App\CRM\Models\CrmResult;

class LogTaskCompleted
{
    public function __construct(
        protected ActivityService $activityService
    ) {}

    public function handle(TaskCompleted $event): void
    {
        $task = $event->task;
        $resultId = $event->result_id;

        // default fallback
        $resultName = 'Task completed';

        if ($resultId) {
            $result = CrmResult::find($resultId);

            if ($result) {
                $resultName = $result->name;
            }
        }

        $this->activityService->create([
            'lead_id'     => $task->lead_id,
            'user_id'     => $task->completed_by ?? $task->created_by,
            'type'        => 'task_completed',
            'title'       => $task->title,
            'description' => $resultName,
        ]);
    }
}