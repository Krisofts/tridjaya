<?php

namespace App\Providers;

use App\Auth\Services\AuthorizationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
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
        /*
        |--------------------------------------------------------------------------
        | Group Directive
        |--------------------------------------------------------------------------
        |
        | @group('superadmin')
        | @group(['superadmin', 'owner'])
        |
        */
        Blade::if('group', function (string|array $groups) {

            $user = Auth::user();

            return $user !== null
                && app(AuthorizationService::class)
                    ->inGroup($user, $groups);
        });

        /*
        |--------------------------------------------------------------------------
        | Permission Directive
        |--------------------------------------------------------------------------
        |
        | @permission('inventory.view')
        |
        */
        Blade::if('permission', function (string $permission) {

            $user = Auth::user();

            return $user !== null
                && app(AuthorizationService::class)
                    ->canAccess($user, $permission);
        });

        /*
        |--------------------------------------------------------------------------
        | Superadmin Directive
        |--------------------------------------------------------------------------
        |
        | @superadmin
        |
        */
        Blade::if('superadmin', function () {

            $user = Auth::user();

            return $user !== null
                && app(AuthorizationService::class)
                    ->isSuperadmin($user);
        });
    }
}