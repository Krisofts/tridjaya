<?php

namespace App\Notifications;

use App\CRM\Models\CrmTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskDueReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public CrmTask $task,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'     => 'task_due',
            'title'    => 'Task akan jatuh tempo',
            'body'     => $this->task->title,
            'lead_id'  => $this->task->lead_id,
            'lead_name'=> $this->task->lead?->name,
            'task_id'  => $this->task->id,
            'due_at'   => $this->task->due_at?->toIso8601String(),
            'priority' => $this->task->priority,
            'url'      => route('crm.leads.show', $this->task->lead_id),
        ];
    }
}