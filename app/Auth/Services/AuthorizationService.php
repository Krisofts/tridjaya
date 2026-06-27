<?php

namespace App\Auth\Services;

use App\Auth\Models\AuthGroupUser;
use App\Auth\Models\AuthPermissionUser;
use App\User\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
    | USER ACL
    |--------------------------------------------------------------------------
    */
    private function acl(User $user): array
    {
        return Cache::remember(
            $this->aclKey($user),
            now()->addHours(6),
            function () use ($user) {

                return [
                    'groups' => AuthGroupUser::query()
                        ->where('user_id', $user->id)
                        ->pluck('group')
                        ->all(),

                    'permissions' => AuthPermissionUser::query()
                        ->where('user_id', $user->id)
                        ->pluck('permission')
                        ->all(),
                ];
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CACHE CLEAR
    |--------------------------------------------------------------------------
    */
    public function clearCache(User $user): void
    {
        Cache::forget($this->aclKey($user));
    }

    /*
    |--------------------------------------------------------------------------
    | GROUP CHECK
    |--------------------------------------------------------------------------
    */
    public function inGroup(User $user, string|array $groups): bool
    {
        return !empty(array_intersect(
            $this->acl($user)['groups'],
            (array) $groups
        ));
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
    | USER-LEVEL PERMISSION ONLY (Shield behavior)
    |--------------------------------------------------------------------------
    */
    public function hasPermission(User $user, string $permission): bool
    {
        return in_array(
            $permission,
            $this->acl($user)['permissions'],
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FINAL ACCESS CHECK (CORE ENGINE)
    |--------------------------------------------------------------------------
    */
    public function can(User $user, string $permission): bool
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
        $groupPermissions = config(self::CONFIG . '.groupPermissions', []);

        [$module] = explode('.', $permission . '.');

        foreach ($acl['groups'] as $group) {

            $permissions = $groupPermissions[$group] ?? [];

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
        return $this->can($user, $permission);
    }

    /*
    |--------------------------------------------------------------------------
    | GROUP MANAGEMENT 
    |--------------------------------------------------------------------------
    */
    public function addGroup(User $user, string|array $groups): void
    {
        foreach ((array) $groups as $group) {

            AuthGroupUser::firstOrCreate([
                'user_id' => $user->id,
                'group'   => $group,
            ]);
        }

        $this->clearCache($user);
    }

    public function removeGroup(User $user, string|array $groups): void
    {
        AuthGroupUser::query()
            ->where('user_id', $user->id)
            ->whereIn('group', (array) $groups)
            ->delete();

        $this->clearCache($user);
    }

    public function syncGroups(User $user, array $groups): void
    {
        DB::transaction(function () use ($user, $groups) {

            AuthGroupUser::query()
                ->where('user_id', $user->id)
                ->delete();

            foreach (array_unique($groups) as $group) {

                AuthGroupUser::create([
                    'user_id' => $user->id,
                    'group'   => $group,
                ]);
            }
        });

        $this->clearCache($user);
    }

    public function getGroups(User $user): array
    {
        return $this->acl($user)['groups'];
    }

    /*
    |--------------------------------------------------------------------------
    | PERMISSION MANAGEMENT (SHIELD STYLE)
    |--------------------------------------------------------------------------
    */
    public function addPermission(User $user, string|array $permissions): void
    {
        foreach ((array) $permissions as $permission) {

            AuthPermissionUser::firstOrCreate([
                'user_id'    => $user->id,
                'permission' => $permission,
            ]);
        }

        $this->clearCache($user);
    }

    public function removePermission(User $user, string|array $permissions): void
    {
        AuthPermissionUser::query()
            ->where('user_id', $user->id)
            ->whereIn('permission', (array) $permissions)
            ->delete();

        $this->clearCache($user);
    }

    public function syncPermissions(User $user, array $permissions): void
    {
        DB::transaction(function () use ($user, $permissions) {

            AuthPermissionUser::query()
                ->where('user_id', $user->id)
                ->delete();

            foreach (array_unique($permissions) as $permission) {

                AuthPermissionUser::create([
                    'user_id'    => $user->id,
                    'permission' => $permission,
                ]);
            }
        });

        $this->clearCache($user);
    }

    public function getPermissions(User $user): array
    {
        return $this->acl($user)['permissions'];
    }

    /*
|--------------------------------------------------------------------------
| LOGIN REDIRECT
|--------------------------------------------------------------------------
*/
public function redirectRoute(User $user): string
{
    $routes = config('auth_redirect.groups', []);

    foreach ($this->getGroups($user) as $group) {
        if (isset($routes[$group])) {
            return $routes[$group];
        }
    }

    return config('auth_redirect.default', 'dashboard');
}
}