<?php

namespace App\Auth\Services;

use App\Auth\Models\AuthGroupUser;
use App\User\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AuthorizationService
{
    private const CONFIG = 'auth_groups';

    /*
    |--------------------------------------------------------------------------
    | CACHE KEY
    |--------------------------------------------------------------------------
    */
    private function aclKey(User $user): string
    {
        return "auth:user:{$user->id}:acl";
    }

    /*
    |--------------------------------------------------------------------------
    | BUILD / GET ACL CACHE (CACHE AGNOSTIC - READY FOR REDIS)
    |--------------------------------------------------------------------------
    | Structure:
    | [
    |   'groups' => [...],
    |   'permissions' => [...],
    | ]
    */
    private function acl(User $user): array
    {
        return Cache::remember(
            $this->aclKey($user),
            now()->addHours(6),
            function () use ($user) {

                $groups = DB::table('auth_groups_users')
                    ->where('user_id', $user->id)
                    ->pluck('group')
                    ->all();

                $permissions = DB::table('auth_permissions_users')
                    ->where('user_id', $user->id)
                    ->pluck('permission')
                    ->all();

                return [
                    'groups'      => $groups,
                    'permissions' => $permissions,
                ];
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SUPER ADMIN CHECK
    |--------------------------------------------------------------------------
    */
    public function isSuperadmin(User $user): bool
    {
        return $this->inGroup(
            $user,
            config(self::CONFIG . '.superadminGroup', 'superadmin')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GROUP CHECK
    |--------------------------------------------------------------------------
    */
    public function inGroup(User $user, string|array $groups): bool
    {
        $acl = $this->acl($user);

        return !empty(array_intersect(
            $acl['groups'],
            (array) $groups
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | PERMISSION CHECK (FAST PATH)
    |--------------------------------------------------------------------------
    */
    public function hasPermission(User $user, string $permission): bool
    {
        if ($this->isSuperadmin($user)) {
            return true;
        }

        $acl = $this->acl($user);

        /*
        |----------------------------------------------------------
        | 1. DIRECT USER PERMISSION
        |----------------------------------------------------------
        */
        if (in_array($permission, $acl['permissions'], true)) {
            return true;
        }

        /*
        |----------------------------------------------------------
        | 2. GROUP-BASED PERMISSION
        |----------------------------------------------------------
        */
        $groupPermissionsMap = config(self::CONFIG . '.groupPermissions', []);
        $groups = $acl['groups'];

        [$module] = explode('.', $permission . '.');

        foreach ($groups as $group) {

            $permissions = $groupPermissionsMap[$group] ?? [];

            if (in_array('*', $permissions, true)) {
                return true;
            }

            if (in_array($permission, $permissions, true)) {
                return true;
            }

            if (in_array($module . '.*', $permissions, true)) {
                return true;
            }
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | ALIAS
    |--------------------------------------------------------------------------
    */
    public function canAccess(User $user, string $permission): bool
    {
        return $this->hasPermission($user, $permission);
    }

    /*
    |--------------------------------------------------------------------------
    | GROUP MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function assignGroup(User $user, string $group): void
    {
        AuthGroupUser::create([
            'user_id' => $user->id,
            'group'   => $group,
        ]);

        $this->clearCache($user);
    }

    public function removeGroup(User $user, string $group): void
    {
        AuthGroupUser::where('user_id', $user->id)
            ->where('group', $group)
            ->delete();

        $this->clearCache($user);
    }

    public function syncGroups(User $user, array $groups): void
    {
        DB::transaction(function () use ($user, $groups) {

            AuthGroupUser::where('user_id', $user->id)->delete();

            foreach ($groups as $group) {
                AuthGroupUser::create([
                    'user_id' => $user->id,
                    'group'   => $group,
                ]);
            }

            $this->clearCache($user);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | CACHE INVALIDATION (SAFE + FUTURE PROOF)
    |--------------------------------------------------------------------------
    */
    public function clearCache(User $user): void
    {
        Cache::forget($this->aclKey($user));
    }
}