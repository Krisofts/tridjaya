<?php

namespace App\CRM\Services;

use App\CRM\Models\Task;
use Illuminate\Validation\Rule;

class TaskService
{
    /*
    |--------------------------------------------------------------------------
    | BASE QUERY
    |--------------------------------------------------------------------------
    */
    private function baseQuery()
    {
        return Task::query()
            ->with(['lead', 'assignedTo', 'createdBy']);
    }

    /*
    |--------------------------------------------------------------------------
    | LIST TASK
    |--------------------------------------------------------------------------
    */
    public function paginate(int $perPage = 15)
    {
        return $this->baseQuery()
            ->latest()
            ->paginate($perPage);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE TASK
    |--------------------------------------------------------------------------
    */
    public function create(array $data): Task
    {
        $this->validate($data);

        $data['status'] ??= Task::defaultStatus();

        if (($data['status'] ?? null) === 'done') {
            $data['completed_at'] = now();
        }

        return Task::create($data);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE TASK
    |--------------------------------------------------------------------------
    */
    public function update(Task $task, array $data): Task
    {
        $this->validate($data);

        $task->update($data);

        return $task->fresh();
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE TASK
    |--------------------------------------------------------------------------
    */
    public function delete(Task $task): bool
    {
        return (bool) $task->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS
    |--------------------------------------------------------------------------
    */
    public function updateStatus(Task $task, string $status): Task
    {
        $allowed = array_keys(config('crm.task_status'));

        if (!in_array($status, $allowed)) {
            throw new \InvalidArgumentException("Invalid task status");
        }

        $data = ['status' => $status];

        if ($status === 'done') {
            $data['completed_at'] = now();
        } else {
            $data['completed_at'] = null;
        }

        $task->update($data);

        return $task->fresh();
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */
    private function validate(array $data): void
    {
        validator($data, [
            'status' => [
                'nullable',
                Rule::in(array_keys(config('crm.task_status'))),
            ],

            'priority' => [
                'nullable',
                Rule::in(array_keys(config('crm.task_priority'))),
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],
        ])->validate();
    }
}