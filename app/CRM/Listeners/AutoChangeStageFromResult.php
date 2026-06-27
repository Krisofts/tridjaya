<?php

namespace App\CRM\Listeners;

use App\CRM\Events\TaskCompleted;
use App\CRM\Models\CrmResultStageMapping;
use App\CRM\Services\LeadService;

class AutoChangeStageFromResult
{
    public function __construct(
        protected LeadService $leads,
    ) {}

    public function handle(TaskCompleted $event): void
    {
        $task     = $event->task;
        $resultId = $event->resultId;

        if (! $resultId) return;

        $lead = $task->lead;

        if (! $lead?->pipeline_id) return;

        $mapping = CrmResultStageMapping::query()
            ->where('pipeline_id', $lead->pipeline_id)
            ->where('result_id', $resultId)
            ->first();

        if (! $mapping?->target_stage_id) return;

        $this->leads->changeStage($lead, $mapping->target_stage_id);
    }
}