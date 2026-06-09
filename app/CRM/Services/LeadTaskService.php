<?php

namespace App\CRM\Services;

use App\CRM\Filters\LeadTaskFilter;
use App\CRM\Models\LeadTask;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class LeadTaskService
{
    public function __construct(
        protected LeadTaskFilter $filter
    ) {}

    /*
    |--------------------------------------------------------------------------
    | GET TASKS
    |--------------------------------------------------------------------------
    */

    public function getTasks(array $filters = [])
    {
        $query = LeadTask::query()
            ->with(['lead', 'assignedTo', 'createdBy']);

        $query = $this->filter->apply($query, $filters);

        return $query
            ->latest()
            ->paginate(20)
            ->withQueryString();
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE TASK
    |--------------------------------------------------------------------------
    */

    public function create(array $data): LeadTask
    {
        return DB::transaction(function () use ($data) {

            $this->validateCoreFields($data);

            return LeadTask::create([
                'lead_id' => $data['lead_id'],

                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'notes' => $data['notes'] ?? null,

                'type' => $data['type'],
                'priority' => $data['priority'] ?? LeadTask::PRIORITY_MEDIUM,
                'status' => LeadTask::STATUS_OPEN,

                'assigned_to' => $data['assigned_to'] ?? null,
                'created_by' => Auth::id(),

                'due_date' => $data['due_date'] ?? null,
                'reminder_at' => $data['reminder_at'] ?? null,

                'metadata' => $data['metadata'] ?? null,
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE TASK
    |--------------------------------------------------------------------------
    */

    public function update(LeadTask $task, array $data): LeadTask
    {
        $this->validateOptionalFields($data);

        $task->update([
            'title' => $data['title'] ?? $task->title,
            'description' => $data['description'] ?? $task->description,
            'notes' => $data['notes'] ?? $task->notes,

            'type' => $data['type'] ?? $task->type,
            'priority' => $data['priority'] ?? $task->priority,

            'due_date' => $data['due_date'] ?? $task->due_date,
            'reminder_at' => $data['reminder_at'] ?? $task->reminder_at,

            'metadata' => $data['metadata'] ?? $task->metadata,
        ]);

        return $task->fresh();
    }

    /*
    |--------------------------------------------------------------------------
    | ASSIGN TASK
    |--------------------------------------------------------------------------
    */

    public function assign(LeadTask $task, int $userId): LeadTask
    {
        $task->update([
            'assigned_to' => $userId,
        ]);

        return $task->fresh();
    }

    /*
    |--------------------------------------------------------------------------
    | START TASK
    |--------------------------------------------------------------------------
    */

    public function start(LeadTask $task): LeadTask
    {
        $this->ensureNotCancelled($task);

        $task->update([
            'status' => LeadTask::STATUS_IN_PROGRESS,
        ]);

        return $task->fresh();
    }

    /*
    |--------------------------------------------------------------------------
    | COMPLETE TASK
    |--------------------------------------------------------------------------
    */

    public function complete(
        LeadTask $task,
        ?string $outcome = null,
        ?string $notes = null
    ): LeadTask {
        $this->ensureNotCancelled($task);

        $task->update([
            'status' => LeadTask::STATUS_DONE,
            'completed_at' => now(),
            'completed_by' => Auth::id(),

            'outcome' => $outcome ?? $task->outcome,
            'notes' => $notes ?? $task->notes,
        ]);

        return $task->fresh();
    }

    /*
    |--------------------------------------------------------------------------
    | REOPEN TASK
    |--------------------------------------------------------------------------
    */

    public function reopen(LeadTask $task): LeadTask
    {
        $task->update([
            'status' => LeadTask::STATUS_OPEN,
            'completed_at' => null,
            'completed_by' => null,
        ]);

        return $task->fresh();
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL TASK
    |--------------------------------------------------------------------------
    */

    public function cancel(LeadTask $task): LeadTask
    {
        $task->update([
            'status' => LeadTask::STATUS_CANCELLED,
        ]);

        return $task->fresh();
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE TASK
    |--------------------------------------------------------------------------
    */

    public function delete(LeadTask $task): bool
    {
        return $task->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION HELPERS
    |--------------------------------------------------------------------------
    */

    private function validateCoreFields(array $data): void
    {
        if (!LeadTask::isValidPriority($data['priority'] ?? LeadTask::PRIORITY_MEDIUM)) {
            throw new InvalidArgumentException('Invalid priority value');
        }

        if (!in_array($data['type'], LeadTask::types(), true)) {
            throw new InvalidArgumentException('Invalid task type');
        }
    }

    private function validateOptionalFields(array $data): void
    {
        if (isset($data['priority']) && !LeadTask::isValidPriority($data['priority'])) {
            throw new InvalidArgumentException('Invalid priority value');
        }

        if (isset($data['type']) && !in_array($data['type'], LeadTask::types(), true)) {
            throw new InvalidArgumentException('Invalid task type');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | BUSINESS RULE GUARDS
    |--------------------------------------------------------------------------
    */

    private function ensureNotCancelled(LeadTask $task): void
    {
        if ($task->isCancelled()) {
            throw new \RuntimeException('Task is cancelled and cannot be modified.');
        }
    }
}