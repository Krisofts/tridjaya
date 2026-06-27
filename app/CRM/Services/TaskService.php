<?php

namespace App\CRM\Services;

use App\CRM\Events\TaskCompleted;
use App\CRM\Events\TaskCreated;
use App\CRM\Models\CrmTask;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    // -------------------------------------------------------------------------
    // CREATE
    // -------------------------------------------------------------------------

    public function create(array $data): CrmTask
    {
        $task = CrmTask::create([
            'lead_id'     => $data['lead_id'],
            'user_id'     => $data['user_id']     ?? null,
            'created_by'  => Auth::id(),
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'type'        => $data['type']         ?? 'follow_up',
            'priority'    => $data['priority']     ?? 'medium',
            'due_at'      => $data['due_at']       ?? Carbon::now()->addMinutes(15),
            'reminder_at' => $data['reminder_at']  ?? Carbon::now()->addMinutes(5),
            'status'      => 'pending',
        ]);

        event(new TaskCreated($task));

        return $task;
    }

    // -------------------------------------------------------------------------
    // UPDATE
    // -------------------------------------------------------------------------

    public function update(CrmTask $task, array $data): CrmTask
    {
        $task->update([
            'title'       => $data['title']       ?? $task->title,
            'description' => $data['description'] ?? $task->description,
            'type'        => $data['type']         ?? $task->type,
            'priority'    => $data['priority']     ?? $task->priority,
            'due_at'      => $data['due_at']       ?? $task->due_at,
            'reminder_at' => $data['reminder_at']  ?? $task->reminder_at,
            'user_id'     => $data['user_id']      ?? $task->user_id,
        ]);

        return $task->refresh();
    }

    // -------------------------------------------------------------------------
    // START
    // -------------------------------------------------------------------------

    public function start(CrmTask $task): CrmTask
    {
        if ($task->status === 'completed') {
            return $task;
        }

        $task->update(['status' => 'in_progress']);

        return $task->refresh();
    }

    // -------------------------------------------------------------------------
    // COMPLETE
    // -------------------------------------------------------------------------

    public function complete(CrmTask $task, ?int $resultId = null): CrmTask
    {
        if ($task->status === 'completed') {
            return $task;
        }

        $task->update([
            'status'       => 'completed',
            'completed_at' => now(),
            'result_id'    => $resultId,
        ]);

        event(new TaskCompleted($task, $resultId));

        return $task->refresh();
    }

    // -------------------------------------------------------------------------
    // CANCEL
    // -------------------------------------------------------------------------

    public function cancel(CrmTask $task): CrmTask
    {
        if ($task->status === 'completed') {
            return $task;
        }

        $task->update(['status' => 'cancelled']);

        return $task->refresh();
    }

    // -------------------------------------------------------------------------
    // DELETE
    // -------------------------------------------------------------------------

    public function delete(CrmTask $task): bool
    {
        return (bool) $task->delete();
    }

    // -------------------------------------------------------------------------
    // QUERIES
    // -------------------------------------------------------------------------

    public function getActiveByLead(int $leadId): Collection
    {
        return CrmTask::query()
            ->where('lead_id', $leadId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->latest()
            ->get();
    }

    public function getCompletedByLead(int $leadId): Collection
    {
        return CrmTask::query()
            ->where('lead_id', $leadId)
            ->where('status', 'completed')
            ->latest()
            ->get();
    }

    public function getMyTasks(int $userId): Collection
    {
        return CrmTask::query()
            ->where('user_id', $userId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('due_at')
            ->get();
    }

    public function getOverdueTasks(): Collection
    {
        return CrmTask::query()
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->orderBy('due_at')
            ->get();
    }

    // -------------------------------------------------------------------------
    // SHORTCUTS
    // -------------------------------------------------------------------------

    public function createFollowUp(int $leadId, string $title): CrmTask
    {
        return $this->create([
            'lead_id' => $leadId,
            'title'   => $title,
            'type'    => 'follow_up',
        ]);
    }
}