<?php

namespace App\Providers;

use App\Auth\Services\AuthorizationService;
use App\CRM\Models\CrmActivityResult;
use App\CRM\Models\CrmActivityType;
use App\CRM\Models\CrmInterest;
use App\CRM\Models\CrmPipeline;
use App\CRM\Models\CrmSource;
use App\CRM\Observers\CacheInvalidationObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Blade Directives
        |--------------------------------------------------------------------------
        */

        Blade::if('group', function (string|array $groups) {
            $user = Auth::user();
            return $user !== null
                && app(AuthorizationService::class)->inGroup($user, $groups);
        });

        Blade::if('permission', function (string $permission) {
            $user = Auth::user();
            return $user !== null
                && app(AuthorizationService::class)->canAccess($user, $permission);
        });

        Blade::if('superadmin', function () {
            $user = Auth::user();
            return $user !== null
                && app(AuthorizationService::class)->isSuperadmin($user);
        });

        /*
        |--------------------------------------------------------------------------
        | CRM Cache Observers
        | Auto-invalidate cache master data saat ada perubahan
        |--------------------------------------------------------------------------
        */

        CrmPipeline::observe(CacheInvalidationObserver::class);
        CrmSource::observe(CacheInvalidationObserver::class);
        CrmInterest::observe(CacheInvalidationObserver::class);
        CrmActivityType::observe(CacheInvalidationObserver::class);
        CrmActivityResult::observe(CacheInvalidationObserver::class);
    }
}