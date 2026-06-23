<?php

namespace App\CRM\Services;

use App\CRM\Events\TaskCompleted;
use App\CRM\Events\TaskCreated;
use App\CRM\Models\CrmTask;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    /*
    |--------------------------------------------------------------------------
    | CREATE TASK
    |--------------------------------------------------------------------------
    */
    public function create(array $data): CrmTask
    {
        $task = CrmTask::create([
            'lead_id' => $data['lead_id'],
            'user_id' => $data['user_id'] ?? null,
            'created_by' => Auth::id(),

            'title' => $data['title'],
            'description' => $data['description'] ?? null,

            'type' => $data['type'] ?? 'follow_up',
            'priority' => $data['priority'] ?? 'medium',

            'due_at' => $data['due_at'] ?? Carbon::now()->addMinutes(15),
            'reminder_at' => $data['reminder_at'] ?? Carbon::now()->addMinutes(5),

            'status' => 'pending',
        ]);

        event(new TaskCreated($task));

        return $task;
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(CrmTask $task, array $data): CrmTask
    {
        $task->update([
            'title' => $data['title'] ?? $task->title,
            'description' => $data['description'] ?? $task->description,
            'type' => $data['type'] ?? $task->type,
            'priority' => $data['priority'] ?? $task->priority,
            'due_at' => $data['due_at'] ?? $task->due_at,
            'reminder_at' => $data['reminder_at'] ?? $task->reminder_at,
            'user_id' => $data['user_id'] ?? $task->user_id,
        ]);

        return $task->refresh();
    }

    /*
    |--------------------------------------------------------------------------
    | START
    |--------------------------------------------------------------------------
    */
    public function start(CrmTask $task): CrmTask
    {
        if ($task->status === 'completed') {
            return $task;
        }

        $task->update([
            'status' => 'in_progress',
        ]);

        return $task->refresh();
    }

    /*
    |--------------------------------------------------------------------------
    | COMPLETE (CRM ENGINE ENTRY POINT)
    |--------------------------------------------------------------------------
    */
    public function complete(
        CrmTask $task,
        ?int $resultId = null
    ): CrmTask {
        if ($task->status === 'completed') {
            return $task;
        }

        $task->update([
            'status' => 'completed',
            'completed_at' => now(),

            // ✅ UPDATED: result_id (bukan string)
            'result_id' => $resultId,
        ]);

        // 🚀 trigger CRM workflow engine
        event(new TaskCompleted($task, $resultId));

        return $task->refresh();
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL
    |--------------------------------------------------------------------------
    */
    public function cancel(CrmTask $task): CrmTask
    {
        if ($task->status === 'completed') {
            return $task;
        }

        $task->update([
            'status' => 'cancelled',
        ]);

        return $task->refresh();
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function delete(CrmTask $task): bool
    {
        return $task->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | GET BY LEAD
    |--------------------------------------------------------------------------
    */
    public function getActiveByLead(int $leadId)
    {
        return CrmTask::where('lead_id', $leadId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->latest()
            ->get();
    }

    public function getCompletedByLead(int $leadId)
    {
        return CrmTask::where('lead_id', $leadId)
            ->where('status', 'completed')
            ->latest()
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | MY TASKS
    |--------------------------------------------------------------------------
    */
    public function getMyTasks(int $userId)
    {
        return CrmTask::where('user_id', $userId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('due_at')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | OVERDUE TASKS
    |--------------------------------------------------------------------------
    */
    public function getOverdueTasks()
    {
        return CrmTask::whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->orderBy('due_at')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | QUICK FOLLOW UP
    |--------------------------------------------------------------------------
    */
    public function createFollowUp(int $leadId, string $title): CrmTask
    {
        return $this->create([
            'lead_id' => $leadId,
            'title' => $title,
            'type' => 'follow_up',
        ]);
    }
}