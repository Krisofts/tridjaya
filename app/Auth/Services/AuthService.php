<?php

namespace App\Auth\Services;

use App\User\Models\User;
use App\Auth\Services\AuthGroupService;
use App\Auth\Services\AuthPermissionService;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function __construct(
        protected AuthGroupService $groupService,
        protected AuthPermissionService $permissionService,
    ) {}

    /*
    |---------------------------------------------------
    | AUTH CORE
    |---------------------------------------------------
    */

    public function user(): ?User
    {
        return auth()->user();
    }

    public function check(): bool
    {
        return auth()->check();
    }

    public function logout(): void
    {
        Auth::logout();
    }

    /*
    |---------------------------------------------------
    | GROUPS
    |---------------------------------------------------
    */

    protected function groups(?User $user = null): array
    {
        $user ??= $this->user();

        if (! $user) {
            return [];
        }

        return array_map(
            fn ($g) => strtolower(trim($g)),
            $this->groupService->getGroups($user->id)
        );
    }

    /*
    |---------------------------------------------------
    | PERMISSIONS
    |---------------------------------------------------
    */

    protected function permissions(?User $user = null): array
    {
        $user ??= $this->user();

        if (! $user) {
            return [];
        }

        return array_map(
            fn ($p) => strtolower(trim($p)),
            $this->permissionService->getPermissions($user->id)
        );
    }

    /*
    |---------------------------------------------------
    | GROUP CHECK
    |---------------------------------------------------
    */

    public function inGroup(string ...$groups): bool
    {
        $groups = array_map(
            fn ($g) => strtolower(trim($g)),
            $groups
        );

        return ! empty(array_intersect($groups, $this->groups()));
    }

    /*
    |---------------------------------------------------
    | PERMISSION CHECK (SINGLE)
    |---------------------------------------------------
    */

    public function hasPermission(string $permission): bool
    {
        return in_array(
            strtolower(trim($permission)),
            $this->permissions(),
            true
        );
    }

    /*
    |---------------------------------------------------
    | RBAC CORE CHECK
    |---------------------------------------------------
    */

    public function can(string ...$permissions): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        $permissions = array_map(
            fn ($p) => strtolower(trim($p)),
            $permissions
        );

        $userGroups = $this->groups($user);
        $userPermissions = $this->permissions($user);

        /*
        | SUPERADMIN BYPASS
        */
        if (in_array('superadmin', $userGroups, true)) {
            return true;
        }

        /*
        | DIRECT PERMISSION CHECK
        */
        if (! empty(array_intersect($permissions, $userPermissions))) {
            return true;
        }

        /*
        | MATRIX RULE CHECK
        */
        $matrix = config('auth_groups.matrix', []);

        foreach ($userGroups as $group) {
            foreach ($matrix[$group] ?? [] as $rule) {
                foreach ($permissions as $permission) {
                    if ($this->matches($permission, $rule)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /*
    |---------------------------------------------------
    | WILDCARD MATCHER
    |---------------------------------------------------
    */

    protected function matches(string $permission, string $rule): bool
    {
        $permission = strtolower(trim($permission));
        $rule = strtolower(trim($rule));

        if ($rule === '*') {
            return true;
        }

        if (str_contains($rule, '*')) {
            $pattern = str_replace('\*', '.*', preg_quote($rule, '/'));
            return (bool) preg_match("/^{$pattern}$/", $permission);
        }

        return $permission === $rule;
    }

    /*
    |---------------------------------------------------
    | LOGIN REDIRECT FLOW
    |---------------------------------------------------
    */

    public function redirectAfterLogin(): string
    {
        if (! $this->check()) {
            return route('login');
        }

        return match (true) {
            $this->inGroup('superadmin') => route('users.index'),
            default => route('dashboard'),
        };
    }
}