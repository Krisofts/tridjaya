<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/*
|--------------------------------------------------------------------------
| CRM MODELS
|--------------------------------------------------------------------------
*/
use App\CRM\Models\Lead;
use App\CRM\Models\LeadReminder;
use App\CRM\Models\Task;
use App\CRM\Models\LeadTransaction;

/*
|--------------------------------------------------------------------------
| CRM OBSERVERS
|--------------------------------------------------------------------------
*/
use App\CRM\Observers\LeadObserver;
use App\CRM\Observers\LeadReminderObserver;
use App\CRM\Observers\TaskObserver;
use App\CRM\Observers\LeadTransactionObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | CRM OBSERVERS
        |--------------------------------------------------------------------------
        */

        Lead::observe(LeadObserver::class);

        LeadReminder::observe(LeadReminderObserver::class);

        Task::observe(TaskObserver::class);

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION OBSERVER (NEW)
        |--------------------------------------------------------------------------
        */
        LeadTransaction::observe(LeadTransactionObserver::class);
    }
}