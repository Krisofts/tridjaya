<?php

namespace App\CRM\Controllers;

use App\Http\Controllers\Controller;
use App\CRM\Models\CrmTask;
use App\CRM\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | START TASK
    |--------------------------------------------------------------------------
    */
    public function start(CrmTask $task)
    {
        $this->taskService->start($task);

        return back()->with('success', 'Task started');
    }

    /*
    |--------------------------------------------------------------------------
    | COMPLETE TASK
    |--------------------------------------------------------------------------
    */
    public function complete(Request $request, CrmTask $task)
    {
        $validated = $request->validate([
            'description' => 'nullable|string',
            'result'      => 'nullable|string|max:1000',
        ]);

        // update description dulu kalau diisi/diubah di modal
        if ($request->has('description')) {
            $this->taskService->update($task, [
                'description' => $validated['description'],
            ]);
        }

        // baru tandai selesai + simpan result
        $this->taskService->complete(
            $task,
            $validated['result'] ?? null
        );

        return back()->with('success', 'Task marked as completed');
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL TASK
    |--------------------------------------------------------------------------
    */
    public function cancel(CrmTask $task)
    {
        $this->taskService->cancel($task);

        return back()->with('success', 'Task cancelled');
    }
}