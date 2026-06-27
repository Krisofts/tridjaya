<?php

namespace App\CRM\Services;

use App\CRM\Events\LeadStageChanged;
use App\CRM\Models\CrmLead;
use App\CRM\Models\CrmPipelineStage;

class LeadStageService
{
    // -------------------------------------------------------------------------
    // CHANGE STAGE
    // -------------------------------------------------------------------------

    public function changeStage(CrmLead $lead, int $stageId): CrmLead
    {
        $stage = CrmPipelineStage::query()->findOrFail($stageId);

        $oldStageId = $lead->pipeline_stage_id;

        if ($oldStageId === $stage->id) {
            return $lead;
        }

        $this->validateTransition($lead, $stage);

        $lead->update([
            'pipeline_id'       => $stage->pipeline_id,
            'pipeline_stage_id' => $stage->id,
        ]);

        $lead = $lead->fresh(['pipeline', 'stage']);

        event(new LeadStageChanged(
            lead:       $lead,
            oldStageId: $oldStageId,
            newStageId: $stage->id,
        ));

        return $lead;
    }

    // -------------------------------------------------------------------------
    // PRIVATE
    // -------------------------------------------------------------------------

    /**
     * Validasi transisi stage.
     * Bisa diisi rule: urutan stage, permission, automation check, dll.
     */
    private function validateTransition(CrmLead $lead, CrmPipelineStage $stage): void
    {
        // placeholder — tambahkan rule di sini
    }
}