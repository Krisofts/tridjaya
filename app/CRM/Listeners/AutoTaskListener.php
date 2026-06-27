<?php

namespace App\CRM\Listeners;

use App\CRM\Events\LeadCreated;
use App\CRM\Events\TaskCompleted;
use App\CRM\Services\AutoTaskService;

class AutoTaskListener
{
    public function __construct(
        protected AutoTaskService $autoTasks,
    ) {}

    // -------------------------------------------------------------------------
    // DISPATCH
    // -------------------------------------------------------------------------

    public function handle(object $event): void
    {
        match (true) {
            $event instanceof LeadCreated   => $this->autoTasks->onLeadCreated($event->lead),
            $event instanceof TaskCompleted => $this->autoTasks->onTaskCompleted($event->task, $event->resultId),
            default                         => null,
        };
    }
}