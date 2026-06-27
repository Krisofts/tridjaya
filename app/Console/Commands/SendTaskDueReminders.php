<?php

namespace App\Console\Commands;

use App\CRM\Models\CrmTask;
use App\Notifications\TaskDueReminder;
use Illuminate\Console\Command;

class SendTaskDueReminders extends Command
{
    protected $signature   = 'crm:send-task-reminders';
    protected $description = 'Kirim notifikasi reminder untuk task yang akan jatuh tempo';

    public function handle(): void
    {
        $tasks = CrmTask::query()
            ->with(['assignee', 'lead:id,name'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('due_at')
            ->whereBetween('due_at', [now(), now()->addHour()])
            ->whereNull('reminder_sent_at')
            ->get();

        if ($tasks->isEmpty()) {
            $this->info('Tidak ada task yang perlu diingatkan.');
            return;
        }

        $sent = 0;

        foreach ($tasks as $task) {
            if (! $task->assignee) continue;

            $task->assignee->notify(new TaskDueReminder($task));

            $task->update(['reminder_sent_at' => now()]);

            $sent++;
        }

        $this->info("✓ {$sent} reminder terkirim.");
    }
}