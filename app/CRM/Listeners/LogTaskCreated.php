<?php

namespace App\CRM\Listeners;

use App\CRM\Events\TaskCreated;
use App\CRM\Services\ActivityService;

class LogTaskCreated
{
    public function __construct(
        protected ActivityService $activityService
    ) {}

    public function handle(TaskCreated $event): void
    {
        $task = $event->task;

        $this->activityService->create([
            'lead_id'     => $task->lead_id,
            'user_id'     => $task->created_by,
            'type'        => 'task_created',
            'title'       => $task->title,
            'description' => $task->description ?: 'Task created',
        ]);
    }
}