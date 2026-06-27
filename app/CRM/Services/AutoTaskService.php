<?php

namespace App\CRM\Services;

use App\CRM\Enums\CrmTaskResult;
use App\CRM\Models\CrmLead;
use App\CRM\Models\CrmTask;

class AutoTaskService
{
    public function __construct(
        protected TaskService $tasks,
    ) {}

    // -------------------------------------------------------------------------
    // ENTRY POINTS (dipanggil dari Event Listeners)
    // -------------------------------------------------------------------------

    public function onLeadCreated(CrmLead $lead): void
    {
        $this->createTask($lead->id, [
            'user_id' => $lead->assigned_to,
            'title'   => 'Follow Up Lead Baru',
            'type'    => 'follow_up',
            'due_at'  => now()->addMinutes(15),
        ]);
    }

    public function onTaskCompleted(CrmTask $task, ?int $resultId): void
    {
        if (! $resultId) return;

        $result = \App\CRM\Models\CrmResult::find($resultId);

        if (! $result) return;

        $this->handleTaskResult($task->lead_id, $result->slug ?? $result->name);
    }

    public function onStageChanged(CrmLead $lead, string $stageName): void
    {
        $this->handleStageFlow($lead, $stageName);
    }

    // -------------------------------------------------------------------------
    // TASK RESULT FLOW
    // -------------------------------------------------------------------------

    private function handleTaskResult(int $leadId, string $result): void
    {
        $config = match ($result) {
            CrmTaskResult::NO_RESPONSE->value => [
                'title'  => 'Follow Up Ulang (H+1)',
                'due_at' => now()->addDay(),
            ],
            CrmTaskResult::INTERESTED->value => [
                'title'  => 'Kirim Detail Produk',
                'due_at' => now()->addHours(2),
            ],
            CrmTaskResult::SUBMITTED->value => [
                'title'  => 'Verifikasi Data Customer',
                'due_at' => now()->addHour(),
            ],
            CrmTaskResult::SURVEY->value => [
                'title'  => 'Follow Up Hasil Survey',
                'due_at' => now()->addDay(),
            ],
            CrmTaskResult::DP->value => [
                'title'  => 'Siapkan Delivery / Instalasi',
                'due_at' => now(),
            ],
            CrmTaskResult::DEAL->value => [
                'title'  => 'Follow Up Closing Admin',
                'due_at' => now()->addHours(3),
            ],
            CrmTaskResult::SUCCESS->value => [
                'title'  => 'After Sales Follow Up',
                'due_at' => now()->addDays(7),
            ],
            default => null,
        };

        if ($config) {
            $this->createTask($leadId, $config);
        }
    }

    // -------------------------------------------------------------------------
    // STAGE CHANGED FLOW
    // -------------------------------------------------------------------------

    private function handleStageFlow(CrmLead $lead, string $stageName): void
    {
        $config = match ($stageName) {
            'Contacted' => [
                'title'  => 'Follow Up Kontak Pertama',
                'due_at' => now()->addHours(2),
            ],
            'Prospek' => [
                'title'  => 'Maintain Prospek Aktif',
                'due_at' => now()->addDay(),
            ],
            'Won' => [
                'title'  => 'Serah Terima Customer',
                'due_at' => now(),
            ],
            default => null,
        };

        if ($config) {
            $this->createTask($lead->id, $config);
        }
    }

    // -------------------------------------------------------------------------
    // PRIVATE
    // -------------------------------------------------------------------------

    private function createTask(int $leadId, array $data): CrmTask
    {
        return $this->tasks->create([
            'lead_id' => $leadId,
            ...$data,
        ]);
    }
}