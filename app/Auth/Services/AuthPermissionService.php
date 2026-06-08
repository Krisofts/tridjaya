<?php

namespace App\Auth\Services;

use App\Auth\Models\AuthPermission;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

class AuthPermissionService
{
    private const CACHE_PREFIX = 'user:%d:permissions';

    /*
    |---------------------------------------------------
    | ADD PERMISSION
    |---------------------------------------------------
    */
    public function addPermission(
        int $userId,
        string ...$permissions
    ): bool {
        $permissions = $this->validatePermissions(
            $permissions
        );

        foreach ($permissions as $permission) {

            AuthPermission::firstOrCreate([
                'user_id'    => $userId,
                'permission' => $permission,
            ]);
        }

        $this->forgetCache($userId);

        return true;
    }

    /*
    |---------------------------------------------------
    | REMOVE PERMISSION
    |---------------------------------------------------
    */
    public function removePermission(
        int $userId,
        string ...$permissions
    ): bool {
        $permissions = $this->normalizePermissions(
            $permissions
        );

        AuthPermission::query()
            ->where('user_id', $userId)
            ->whereIn('permission', $permissions)
            ->delete();

        $this->forgetCache($userId);

        return true;
    }

    /*
    |---------------------------------------------------
    | SYNC PERMISSIONS
    |---------------------------------------------------
    */
    public function syncPermissions(
        int $userId,
        string ...$permissions
    ): bool {
        $permissions = $this->validatePermissions(
            $permissions
        );

        AuthPermission::query()
            ->where('user_id', $userId)
            ->delete();

        if (! empty($permissions)) {

            AuthPermission::insert(
                collect($permissions)
                    ->map(fn ($permission) => [
                        'user_id'    => $userId,
                        'permission' => $permission,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])
                    ->all()
            );
        }

        $this->forgetCache($userId);

        return true;
    }

    /*
    |---------------------------------------------------
    | GET USER PERMISSIONS (CACHED)
    |---------------------------------------------------
    */
    public function getPermissions(
        int $userId
    ): array {
        return Cache::remember(
            $this->cacheKey($userId),
            now()->addHours(12),
            fn () => AuthPermission::query()
                ->where('user_id', $userId)
                ->pluck('permission')
                ->map(
                    fn ($permission) =>
                    strtolower(trim($permission))
                )
                ->toArray()
        );
    }

    /*
    |---------------------------------------------------
    | CHECK DIRECT PERMISSION
    |---------------------------------------------------
    */
    public function hasPermission(
        int $userId,
        string $permission
    ): bool {
        return in_array(
            strtolower(trim($permission)),
            $this->getPermissions($userId),
            true
        );
    }

    /*
    |---------------------------------------------------
    | VALIDATION
    |---------------------------------------------------
    */
    private function validatePermissions(
        array $permissions
    ): array {
        $permissions = $this->normalizePermissions(
            $permissions
        );

        $availablePermissions = array_map(
            'strtolower',
            array_keys(
                config('auth_groups.permissions', [])
            )
        );

        foreach ($permissions as $permission) {

            if (
                ! in_array(
                    $permission,
                    $availablePermissions,
                    true
                )
            ) {
                throw new InvalidArgumentException(
                    "Permission '{$permission}' not found."
                );
            }
        }

        return $permissions;
    }

    /*
    |---------------------------------------------------
    | NORMALIZER
    |---------------------------------------------------
    */
    private function normalizePermissions(
        array $permissions
    ): array {
        return collect($permissions)
            ->map(
                fn ($permission) =>
                strtolower(trim($permission))
            )
            ->unique()
            ->values()
            ->all();
    }

    /*
    |---------------------------------------------------
    | CACHE
    |---------------------------------------------------
    */
    private function cacheKey(
        int $userId
    ): string {
        return sprintf(
            self::CACHE_PREFIX,
            $userId
        );
    }

    private function forgetCache(
        int $userId
    ): void {
        Cache::forget(
            $this->cacheKey($userId)
        );
    }
}