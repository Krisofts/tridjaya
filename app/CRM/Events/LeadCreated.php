<?php

namespace App\CRM\Events;

use App\CRM\Models\CrmLead;

class LeadCreated
{
    public function __construct(
        public CrmLead $lead
    ) {}
}