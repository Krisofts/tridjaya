<?php

namespace App\CRM\Services;

use App\CRM\Models\CrmTask;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    /**
     * Create new task manually
     */
    public function create(array $data): CrmTask
    {
        return CrmTask::create([
            'lead_id' => $data['lead_id'],
            'user_id' => $data['user_id'] ?? Auth::id(),

            'title' => $data['title'],
            'description' => $data['description'] ?? null,

            'type' => $data['type'] ?? null, // 👈 penting untuk automation

            'status' => $data['status'] ?? 'pending',
            'priority' => $data['priority'] ?? 'medium',

            'due_at' => $data['due_at'] ?? null,
        ]);
    }

    /**
     * Auto create initial follow up
     */
    public function createInitialFollowUp(int $leadId, ?int $userId = null): CrmTask
    {
        return CrmTask::create([
            'lead_id' => $leadId,
            'user_id' => $userId ?? Auth::id(),

            'type' => 'initial_follow_up',

            'title' => 'Initial Follow Up',
            'description' => 'Hubungi customer untuk follow up awal lead baru.',

            'status' => 'pending',
            'priority' => 'high',

            'due_at' => now()->addDay(),
        ]);
    }

    /**
     * Mark task as completed + trigger automation
     */
    public function complete(CrmTask $task): CrmTask
    {
        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // 👇 trigger automation stage change
        $this->handleAutoStageChange($task);

        return $task;
    }

    /**
     * Start task
     */
    public function start(CrmTask $task): CrmTask
    {
        $task->update([
            'status' => 'in_progress',
        ]);

        return $task;
    }

    /**
     * Cancel task
     */
    public function cancel(CrmTask $task): CrmTask
    {
        $task->update([
            'status' => 'cancelled',
        ]);

        return $task;
    }

    /**
     * Auto stage change logic
     */
    protected function handleAutoStageChange(CrmTask $task): void
    {
        $lead = $task->lead;

        if (!$lead) return;

        // 🚨 hanya trigger dari initial follow up
        if ($task->type !== 'initial_follow_up') {
            return;
        }

        // 🚨 kalau sudah bukan stage pertama, skip
        if ($lead->pipeline_stage_id != 1) {
            return;
        }

        $nextStage = $lead->pipeline->stages()
            ->orderBy('sort_order')
            ->skip(1)
            ->first();

        if ($nextStage) {
            $lead->update([
                'pipeline_stage_id' => $nextStage->id,
            ]);
        }
    }

    /**
     * Get tasks by lead
     */
    public function getByLead(int $leadId)
    {
        return CrmTask::query()
            ->where('lead_id', $leadId)
            ->latest()
            ->get();
    }

    /**
     * Get active tasks
     */
    public function getActive(?int $userId = null)
    {
        return CrmTask::query()
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('due_at')
            ->get();
    }

    /**
     * Get overdue tasks
     */
    public function getOverdue(?int $userId = null)
    {
        return CrmTask::query()
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->orderBy('due_at')
            ->get();
    }

    /**
     * Get today tasks
     */
    public function getToday(?int $userId = null)
    {
        return CrmTask::query()
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->whereDate('due_at', now())
            ->orderBy('due_at')
            ->get();
    }
}