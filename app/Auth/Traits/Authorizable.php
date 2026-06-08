<?php

namespace App\Auth\Traits;

use App\Auth\Services\AuthService;

trait Authorizable
{
    /*
    |---------------------------------------------------
    | AUTH SERVICE
    |---------------------------------------------------
    */
    protected function auth(): AuthService
    {
        return app(AuthService::class);
    }

    /*
    |---------------------------------------------------
    | PERMISSION CHECK
    |---------------------------------------------------
    */
    public function can(...$permissions): bool
    {
        return $this->auth()->can(...$permissions);
    }

    public function hasPermission(string $permission): bool
    {
        return $this->auth()->hasPermission($permission);
    }

    /*
    |---------------------------------------------------
    | GROUP CHECK
    |---------------------------------------------------
    */
    public function inGroup(...$groups): bool
    {
        return $this->auth()->inGroup(...$groups);
    }

    /*
    |---------------------------------------------------
    | READ ONLY DATA
    |---------------------------------------------------
    */
    public function getGroups(): array
    {
        return $this->groups()
            ->pluck('group')
            ->map(fn ($g) => strtolower(trim($g)))
            ->toArray();
    }

    public function getPermissions(): array
    {
        return $this->permissions()
            ->pluck('permission')
            ->map(fn ($p) => strtolower(trim($p)))
            ->toArray();
    }
}