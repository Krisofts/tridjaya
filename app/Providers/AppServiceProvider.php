<?php

namespace App\Providers;

use App\CRM\Models\CrmLead;
use App\CRM\Observers\CrmLeadObserver;
use Illuminate\Support\ServiceProvider;

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
        CrmLead::observe(CrmLeadObserver::class);
    }
}
