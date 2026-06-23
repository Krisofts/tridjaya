<?php

namespace App\CRM\Services;

use App\CRM\Models\CrmLead;
use App\CRM\Models\CrmPipelineStage;

class LeadStageService
{
    public function changeStage(CrmLead $lead, int $stageId): CrmLead
    {
        $stage = CrmPipelineStage::query()
            ->findOrFail($stageId);

        $this->validateTransition($lead, $stage);

        $lead->update([
            'pipeline_id' => $stage->pipeline_id,
            'pipeline_stage_id' => $stage->id,
        ]);

        return $lead->fresh([
            'pipeline',
            'stage',
        ]);
    }

    private function validateTransition(CrmLead $lead, CrmPipelineStage $stage): void
    {
        // nanti bisa diisi rule:
        // - urutan stage
        // - permission
        // - automation check
    }
}