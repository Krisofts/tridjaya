<?php

namespace App\CRM\Listeners;

use App\CRM\Events\LeadStageChanged;
use App\CRM\Services\ActivityService;

class LogLeadStageChanged
{
    public function __construct(
        protected ActivityService $activities,
    ) {}

    public function handle(LeadStageChanged $event): void
    {
        $lead      = $event->lead;
        $oldStage  = \App\CRM\Models\CrmPipelineStage::find($event->oldStageId);
        $newStage  = $lead->stage;

        $from = $oldStage?->name ?? 'Tidak diketahui';
        $to   = $newStage?->name ?? 'Tidak diketahui';

        $this->activities->create([ 
            'lead_id'     => $lead->id,
            'user_id'     => auth()->id(),
            'type'        => 'stage_changed',
            'title'       => "Stage diubah ke: {$to}",
            'description' => "Dari \"{$from}\" → \"{$to}\"",
            'stage_id'    => $newStage?->id,
        ]);
    }
}