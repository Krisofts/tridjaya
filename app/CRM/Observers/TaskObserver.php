<?php

namespace App\CRM\Observers;

use App\CRM\Enums\LeadActivityType;
use App\CRM\Models\Task;
use App\CRM\Services\LeadActivityService;

class TaskObserver
{
    public function __construct(
        protected LeadActivityService $activityService,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | CREATED
    |--------------------------------------------------------------------------
    */
    public function created(Task $task): void
    {
        if (!$task->lead_id) {
            return;
        }

        $task->loadMissing('lead');

        $this->activityService->create(
            $task->lead,
            LeadActivityType::TASK_CREATED,
            "Task dibuat: {$task->title}"
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATED
    |--------------------------------------------------------------------------
    */
    public function updated(Task $task): void
    {
        if (!$task->lead_id) {
            return;
        }

        $changes = $task->getChanges();
        $task->loadMissing(['lead', 'assignedTo']);

        /*
        |--------------------------------------------------------------------------
        | STATUS CHANGED
        |--------------------------------------------------------------------------
        */
        if (array_key_exists('status', $changes)) {

            $this->activityService->create(
                $task->lead,
                LeadActivityType::TASK_STATUS_CHANGED,
                "Status task '{$task->title}' berubah dari '{$task->getOriginal('status')}' menjadi '{$task->status}'"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ASSIGNED CHANGED
        |--------------------------------------------------------------------------
        */
        if (array_key_exists('assigned_to', $changes)) {

            $assignedName = $task->assignedTo?->name ?? 'Unknown';

            $this->activityService->create(
                $task->lead,
                LeadActivityType::TASK_ASSIGNED,
                "Task '{$task->title}' ditugaskan ke {$assignedName}"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | DUE DATE CHANGED
        |--------------------------------------------------------------------------
        */
        if (array_key_exists('due_date', $changes)) {

            $this->activityService->create(
                $task->lead,
                LeadActivityType::TASK_DUE_DATE_CHANGED,
                "Deadline task '{$task->title}' diperbarui"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | COMPLETED
        |--------------------------------------------------------------------------
        */
        if (
            array_key_exists('status', $changes)
            && $task->status === 'done'
        ) {
            $this->activityService->create(
                $task->lead,
                LeadActivityType::TASK_COMPLETED,
                "Task '{$task->title}' telah diselesaikan"
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETED
    |--------------------------------------------------------------------------
    */
    public function deleted(Task $task): void
    {
        if (!$task->lead_id) {
            return;
        }

        $task->loadMissing('lead');

        $this->activityService->create(
            $task->lead,
            LeadActivityType::TASK_DELETED,
            "Task dihapus: {$task->title}"
        );
    }
}