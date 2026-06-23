<?php

namespace App\CRM\Listeners;

use App\CRM\Events\LeadCreated;
use App\CRM\Events\TaskCompleted;
use App\CRM\Services\TaskService;

class AutoTaskListener
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    /**
     * MAIN ENTRY POINT (WAJIB)
     */
    public function handle(object $event): void
    {
        match (get_class($event)) {

            LeadCreated::class => $this->handleLeadCreated($event),

            TaskCompleted::class => $this->handleTaskCompleted($event),

            default => null,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | LEAD CREATED → AUTO TASK
    |--------------------------------------------------------------------------
    */
    private function handleLeadCreated(LeadCreated $event): void
    {
        $lead = $event->lead;

        $this->taskService->create([
            'lead_id' => $lead->id,
            'user_id' => $lead->assigned_to,

            'title'   => 'Follow Up Lead Baru: ' . $lead->name,
            'description' => 'WA / Call dalam 15 menit setelah lead masuk 🔥',

            'type'    => 'follow_up',
            'priority'=> 'urgent',

            'due_at'  => now()->addMinutes(15),
            'reminder_at' => now()->addMinutes(5),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TASK COMPLETED → NEXT FLOW
    |--------------------------------------------------------------------------
    */
    private function handleTaskCompleted(TaskCompleted $event): void
    {
        $task = $event->task;
        $result = $event->result;

        if (!$result) return;

        $leadId = $task->lead_id;

        match ($result) {

            'Tidak Merespon' => $this->taskService->create([
                'lead_id' => $leadId,
                'title'   => 'Follow Up Ulang (H+1)',
                'type'    => 'follow_up',
                'priority'=> 'high',
                'due_at'  => now()->addDay(),
            ]),

            'Minta Info' => $this->taskService->create([
                'lead_id' => $leadId,
                'title'   => 'Kirim Detail Produk / Harga',
                'type'    => 'follow_up',
                'due_at'  => now()->addHours(2),
            ]),

            'Pengajuan Masuk' => $this->taskService->create([
                'lead_id' => $leadId,
                'title'   => 'Verifikasi Data Customer',
                'type'    => 'process',
                'due_at'  => now()->addHour(),
            ]),

            'Sedang Survey' => $this->taskService->create([
                'lead_id' => $leadId,
                'title'   => 'Follow Up Hasil Survey',
                'type'    => 'process',
                'due_at'  => now()->addDay(),
            ]),

            'DP Masuk' => $this->taskService->create([
                'lead_id' => $leadId,
                'title'   => 'Siapkan Delivery / Instalasi',
                'type'    => 'delivery',
                'due_at'  => now(),
            ]),

            'Deal Berjalan' => $this->taskService->create([
                'lead_id' => $leadId,
                'title'   => 'Follow Up Closing Admin',
                'type'    => 'closing',
                'due_at'  => now()->addHours(3),
            ]),

            'Berhasil Closing' => $this->taskService->create([
                'lead_id' => $leadId,
                'title'   => 'After Sales Follow Up (H+7)',
                'type'    => 'after_sales',
                'due_at'  => now()->addDays(7),
            ]),

            default => null,
        };
    }
}