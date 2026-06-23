<?php

namespace App\CRM\Services;

use App\CRM\Enums\CrmTaskResult;
use App\CRM\Models\CrmTask;

class AutoTaskService
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    /**
     * MAIN ENTRY POINT
     */
    public function handle(string $event, $lead, ?string $result = null, ?string $stage = null): void
    {
        match ($event) {

            'lead_created' => $this->leadCreated($lead),

            'task_completed' => $this->taskCompleted($lead, $result),

            'stage_changed' => $this->stageChanged($lead, $stage),

            default => null,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | LEAD CREATED
    |--------------------------------------------------------------------------
    */
    private function leadCreated($lead): void
    {
        $this->taskService->create([
            'lead_id' => $lead->id,
            'user_id' => $lead->assigned_to,

            'title'   => 'Follow Up Lead Baru',
            'type'    => 'follow_up',

            'due_at'  => now()->addMinutes(15),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TASK COMPLETED FLOW
    |--------------------------------------------------------------------------
    */
    private function taskCompleted($task, ?string $result): void
    {
        if (!$result) return;

        $leadId = $task->lead_id;

        match ($result) {

            CrmTaskResult::NO_RESPONSE->value => $this->taskService->create([
                'lead_id' => $leadId,
                'title'   => 'Follow Up Ulang (H+1)',
                'due_at'  => now()->addDay(),
            ]),

            CrmTaskResult::INTERESTED->value => $this->taskService->create([
                'lead_id' => $leadId,
                'title'   => 'Kirim Detail Produk',
                'due_at'  => now()->addHours(2),
            ]),

            CrmTaskResult::SUBMITTED->value => $this->taskService->create([
                'lead_id' => $leadId,
                'title'   => 'Verifikasi Data Customer',
                'due_at'  => now()->addHour(),
            ]),

            CrmTaskResult::SURVEY->value => $this->taskService->create([
                'lead_id' => $leadId,
                'title'   => 'Follow Up Hasil Survey',
                'due_at'  => now()->addDay(),
            ]),

            CrmTaskResult::DP->value => $this->taskService->create([
                'lead_id' => $leadId,
                'title'   => 'Siapkan Delivery / Instalasi',
                'due_at'  => now(),
            ]),

            CrmTaskResult::DEAL->value => $this->taskService->create([
                'lead_id' => $leadId,
                'title'   => 'Follow Up Closing Admin',
                'due_at'  => now()->addHours(3),
            ]),

            CrmTaskResult::SUCCESS->value => $this->taskService->create([
                'lead_id' => $leadId,
                'title'   => 'After Sales Follow Up',
                'due_at'  => now()->addDays(7),
            ]),

            default => null,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | STAGE CHANGED FLOW
    |--------------------------------------------------------------------------
    */
    private function stageChanged($lead, ?string $stage): void
    {
        match ($stage) {

            'Contacted' => $this->taskService->create([
                'lead_id' => $lead->id,
                'title'   => 'Follow Up Kontak Pertama',
                'due_at'  => now()->addHours(2),
            ]),

            'Prospek' => $this->taskService->create([
                'lead_id' => $lead->id,
                'title'   => 'Maintain Prospek Aktif',
                'due_at'  => now()->addDay(),
            ]),

            'Won' => $this->taskService->create([
                'lead_id' => $lead->id,
                'title'   => 'Serah Terima Customer',
                'due_at'  => now(),
            ]),

            default => null,
        };
    }
}