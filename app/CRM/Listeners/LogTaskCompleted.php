<?php

namespace App\CRM\Listeners;

use App\CRM\Events\TaskCompleted;
use App\CRM\Models\CrmResult;
use App\CRM\Services\ActivityService;

class LogTaskCompleted
{
    public function __construct(
        protected ActivityService $activities,
    ) {}

    public function handle(TaskCompleted $event): void
    {
        $task     = $event->task;
        $resultId = $event->resultId;

        $resultName = $resultId
            ? (CrmResult::find($resultId)?->name ?? 'Task completed')
            : 'Task completed';

        $this->activities->create([
            'lead_id'     => $task->lead_id,
            'user_id'     => $task->completed_by ?? $task->created_by,
            'type'        => 'task_completed',
            'title'       => $task->title,
            'description' => $resultName,
            'result_id'   => $resultId,
        ]);
    }
}