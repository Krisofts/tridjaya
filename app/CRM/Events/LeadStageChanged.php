<?php

namespace App\CRM\Events;

use App\CRM\Models\CrmLead;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadStageChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public CrmLead $lead,
        public ?int $oldStageId,
        public ?int $newStageId,
    ) {}
}