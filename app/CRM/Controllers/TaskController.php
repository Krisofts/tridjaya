<?php

namespace App\CRM\Controllers;

use App\Http\Controllers\Controller;
use App\CRM\Models\CrmTask;
use App\CRM\Services\TaskService;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    /**
     * Mark task as completed
     */
    public function complete(CrmTask $task)
    {
        $this->taskService->complete($task);

        return back()->with('success', 'Task marked as completed');
    }
}