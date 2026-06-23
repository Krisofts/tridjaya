<?php

namespace App\CRM\Listeners;

use App\CRM\Events\TaskCompleted;
use App\CRM\Models\CrmResult;
use App\CRM\Models\CrmResultStageMapping;
use App\CRM\Services\LeadService;

class AutoChangeStageFromResult
{
    public function __construct(
        protected LeadService $leadService
    ) {}

    public function handle(TaskCompleted $event): void
    {
        $task = $event->task;
        $resultId = $event->result_id;

        if (! $resultId) {
            return;
        }

        $result = CrmResult::find($resultId);

        if (! $result) {
            return;
        }

        $lead = $task->lead;

        if (! $lead || ! $lead->pipeline_id) {
            return;
        }

        $mapping = CrmResultStageMapping::where('pipeline_id', $lead->pipeline_id)
            ->where('result_id', $resultId)
            ->first();

        if (! $mapping || ! $mapping->target_stage_id) {
            return;
        }

        $this->leadService->changeStage(
            $lead,
            $mapping->target_stage_id
        );
    }
}