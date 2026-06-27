<?php

namespace App\Providers;

use App\CRM\Events\LeadCreated;
use App\CRM\Events\LeadStageChanged;
use App\CRM\Events\TaskCompleted;
use App\CRM\Events\TaskCreated;
use App\CRM\Listeners\AutoChangeStageFromResult;
use App\CRM\Listeners\AutoCreateStageTasks;
use App\CRM\Listeners\AutoTaskListener;
use App\CRM\Listeners\LogLeadStageChanged;
use App\CRM\Listeners\LogTaskCompleted;
use App\CRM\Listeners\LogTaskCreated;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [

        LeadCreated::class => [
            AutoTaskListener::class,        // buat task follow up otomatis
        ],

        LeadStageChanged::class => [
            AutoCreateStageTasks::class,    // buat tasks dari template stage
            LogLeadStageChanged::class,     // log perubahan stage ke timeline
        ],

        TaskCreated::class => [
            LogTaskCreated::class,          // log activity: task dibuat
        ],

        TaskCompleted::class => [
            LogTaskCompleted::class,        // log activity: task selesai
            AutoTaskListener::class,        // buat task berikutnya berdasarkan result
            AutoChangeStageFromResult::class, // pindah stage otomatis berdasarkan result
        ],

    ];
}