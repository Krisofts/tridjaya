<?php

namespace App\CRM\Jobs;

use App\CRM\Models\CrmLead;
use App\CRM\Models\CrmNotification;
use App\CRM\Models\CrmTask;
use App\CRM\Services\NotificationService;
use Illuminate\Console\Command;

/**
 * Command ini dijalankan via scheduler setiap 15 menit.
 * Tugasnya: scan task overdue & follow-up terlewat, lalu buat notifikasi.
 *
 * Daftarkan di bootstrap/app.php:
 *   ->withSchedule(function (Schedule $schedule) {
 *       $schedule->command('crm:send-reminders')->everyFifteenMinutes();
 *   })
 */
class SendCrmReminders extends Command
{
    protected $signature   = 'crm:send-reminders';
    protected $description = 'Kirim reminder otomatis: task overdue & follow-up terlewat';

    public function __construct(
        private readonly NotificationService $service,
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->checkTasksOverdue();
        $this->checkFollowUpsOverdue();

        $this->info('✓ CRM reminders selesai dikirim: ' . now()->format('d M Y H:i'));
    }

    // -------------------------------------------------------------------------

    private function checkTasksOverdue(): void
    {
        // Ambil task yang overdue dan belum dapat notifikasi hari ini
        $tasks = CrmTask::query()
            ->open()
            ->overdue()
            ->whereNotNull('assigned_to')
            ->whereDoesntHave('notifications', function ($q) {
                $q->where('type', CrmNotification::TYPE_TASK_OVERDUE)
                  ->whereDate('created_at', today());
            })
            ->with('lead')
            ->get();

        foreach ($tasks as $task) {
            $this->service->notifyTaskOverdue($task);
        }

        $this->line("  Task overdue: {$tasks->count()} notifikasi dibuat.");
    }

    private function checkFollowUpsOverdue(): void
    {
        // Lead open yang follow-up sudah lewat dan belum dapat notifikasi hari ini
        $leads = CrmLead::query()
            ->open()
            ->whereNotNull('assigned_to')
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<', now())
            ->whereDoesntHave('notifications', function ($q) {
                $q->where('type', CrmNotification::TYPE_FOLLOWUP_OVERDUE)
                  ->whereDate('created_at', today());
            })
            ->get();

        foreach ($leads as $lead) {
            $this->service->notifyFollowUpOverdue($lead);
        }

        $this->line("  Follow-up overdue: {$leads->count()} notifikasi dibuat.");
    }
}