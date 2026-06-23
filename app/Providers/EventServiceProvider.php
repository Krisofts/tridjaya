<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

// EVENTS
use App\CRM\Events\LeadCreated;
use App\CRM\Events\LeadStageChanged;
use App\CRM\Events\TaskCreated;
use App\CRM\Events\TaskCompleted;

// LISTENERS
use App\CRM\Listeners\AutoCreateTask;
use App\CRM\Listeners\AutoTaskListener;
use App\CRM\Listeners\AutoCreateStageTasks;
use App\CRM\Listeners\LogTaskCreated;
use App\CRM\Listeners\LogTaskCompleted;
use App\CRM\Listeners\AutoChangeStageFromResult; // 🔥 TAMBAHAN BARU

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [

        LeadCreated::class => [
            AutoTaskListener::class,
        ],

        LeadStageChanged::class => [
            AutoCreateStageTasks::class,
        ],

        TaskCreated::class => [
            LogTaskCreated::class,
        ],

        TaskCompleted::class => [
            LogTaskCompleted::class,
            AutoChangeStageFromResult::class, // 🔥 TAMBAHAN DI SINI
        ],
    ];
}