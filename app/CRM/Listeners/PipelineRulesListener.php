<?php

namespace App\CRM\Listeners;

use App\CRM\Events\TaskCompleted;
use App\CRM\Services\LeadService;
use App\CRM\Models\CrmLead;

class PipelineRulesListener
{
    public function __construct(
        protected LeadService $leadService
    ) {}

    /**
     * MAIN EVENT HANDLER
     */
    public function handle(TaskCompleted $event): void
    {
        $task = $event->task;
        $result = $event->result;

        if (!$result) return;

        $lead = CrmLead::query()
            ->with('pipeline', 'pipeline.stages')
            ->find($task->lead_id);

        if (!$lead || !$lead->pipeline) return;

        $stage = $this->resolveStage($lead, $result);

        if (!$stage) return;

        $this->leadService->changeStage($lead, $stage->id);
    }

    /**
     * RULE ENGINE (PIPELINE MAPPING)
     */
    private function resolveStage(CrmLead $lead, string $result)
    {
        $stages = $lead->pipeline->stages;

        return match ($result) {

            /*
            |--------------------------------------------------------------------------
            | ❌ LOST FLOW
            |--------------------------------------------------------------------------
            */
            'Tidak Merespon',
            'Tidak Tertarik',
            'Ditolak'
                => $stages->firstWhere('is_lost', true),

            /*
            |--------------------------------------------------------------------------
            | 🟡 WARM FLOW
            |--------------------------------------------------------------------------
            */
            'Minta Info',
            'Follow Up Kembali'
                => $stages->firstWhere('name', 'Contacted'),

            /*
            |--------------------------------------------------------------------------
            | 🟠 PROCESS FLOW
            |--------------------------------------------------------------------------
            */
            'Pengajuan Masuk',
            'Sedang Survey'
                => $stages
                    ->whereIn('name', ['Prospek', 'Survey'])
                    ->sortBy('sort_order')
                    ->first(),

            /*
            |--------------------------------------------------------------------------
            | 🔵 DEAL FLOW
            |--------------------------------------------------------------------------
            */
            'Deal Berjalan',
            'DP Masuk'
                => $stages->firstWhere('name', 'Deal'),

            /*
            |--------------------------------------------------------------------------
            | 🏆 SUCCESS FLOW
            |--------------------------------------------------------------------------
            */
            'Berhasil Closing'
                => $stages->firstWhere('is_won', true),

            default => null,
        };
    }
}