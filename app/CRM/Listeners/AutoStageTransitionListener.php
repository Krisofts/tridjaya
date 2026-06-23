<?php

namespace App\CRM\Listeners;

use App\CRM\Events\TaskCompleted;
use App\CRM\Events\LeadStageChanged;
use App\CRM\Models\CrmResultStageMapping;

class AutoStageTransitionListener
{
    /**
     * Handle TaskCompleted event
     */
    public function handle(TaskCompleted $event): void
    {
        $task = $event->task;
        $lead = $task->lead;

        // result dari event (wajib dikirim dari UI / service)
        $resultId = $event->result_id ?? null;

        if (! $resultId) {
            return;
        }

        // cari mapping di database
        $mapping = CrmResultStageMapping::query()
            ->where('pipeline_id', $lead->pipeline_id)
            ->where('result_id', $resultId)
            ->where('is_active', true)
            ->first();

        if (! $mapping) {
            return;
        }

        // jika stage sama, stop
        if ($lead->pipeline_stage_id === $mapping->target_stage_id) {
            return;
        }

        // update stage lead
        $lead->update([
            'pipeline_stage_id' => $mapping->target_stage_id,
        ]);

        // trigger event stage berubah
        event(new LeadStageChanged($lead));
    }
}