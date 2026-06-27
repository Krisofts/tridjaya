<?php

namespace App\CRM\Events;

use App\CRM\Models\CrmTask;

class TaskCompleted
{
    public function __construct(
        public CrmTask $task,
        public ?int    $resultId = null,
    ) {}
}