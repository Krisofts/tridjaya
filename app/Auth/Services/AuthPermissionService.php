<?php

namespace App\Auth\Services;

use App\Auth\Models\AuthPermission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AuthPermissionService
{
    private const CACHE_PREFIX = 'auth:user:%d:permissions';

    /*
    |--------------------------------
    | ADD PERMISSION
    |--------------------------------
    */
    public function addPermission(int $userId, string ...$permissions): bool
    {
        $permissions = $this->normalize($permissions);

        $this->validate($permissions);

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
    |--------------------------------
    | REMOVE PERMISSION
    |--------------------------------
    */
    public function removePermission(int $userId, string ...$permissions): bool
    {
        $permissions = $this->normalize($permissions);

        AuthPermission::where('user_id', $userId)
            ->whereIn('permission', $permissions)
            ->delete();

        $this->forgetCache($userId);

        return true;
    }

    /*
    |--------------------------------
    | SYNC PERMISSION (FULL REPLACE)
    |--------------------------------
    */
    public function syncPermissions(int $userId, string ...$permissions): bool
    {
        $permissions = $this->normalize($permissions);
        $this->validate($permissions);

        DB::transaction(function () use ($userId, $permissions) {

            AuthPermission::where('user_id', $userId)->delete();

            if (!empty($permissions)) {
                AuthPermission::insert(
                    array_map(fn ($permission) => [
                        'user_id'    => $userId,
                        'permission' => $permission,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ], $permissions)
                );
            }
        });

        $this->forgetCache($userId);

        return true;
    }

    /*
    |--------------------------------
    | GET PERMISSIONS (CACHED)
    |--------------------------------
    */
    public function getPermissions(int $userId): array
    {
        return Cache::remember(
            $this->cacheKey($userId),
            now()->addHours(12),
            fn () => AuthPermission::where('user_id', $userId)
                ->pluck('permission')
                ->map(fn ($p) => strtolower(trim($p)))
                ->unique()
                ->values()
                ->toArray()
        );
    }

    /*
    |--------------------------------
    | CHECK PERMISSION
    |--------------------------------
    */
    public function hasPermission(int $userId, string $permission): bool
    {
        return in_array(
            strtolower(trim($permission)),
            $this->getPermissions($userId),
            true
        );
    }

    /*
    |--------------------------------
    | VALIDATION
    |--------------------------------
    */
    private function validate(array $permissions): void
    {
        if (empty($permissions)) {
            return;
        }

        $available = array_map(
            'strtolower',
            array_keys(config('auth_groups.permissions', []))
        );

        foreach ($permissions as $permission) {
            if (!in_array($permission, $available, true)) {
                throw new InvalidArgumentException(
                    "Permission '{$permission}' not found in config."
                );
            }
        }
    }

    /*
    |--------------------------------
    | NORMALIZER
    |--------------------------------
    */
    private function normalize(array $permissions): array
    {
        return collect($permissions)
            ->map(fn ($p) => strtolower(trim($p)))
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    /*
    |--------------------------------
    | CACHE KEY
    |--------------------------------
    */
    private function cacheKey(int $userId): string
    {
        return sprintf(self::CACHE_PREFIX, $userId);
    }

    /*
    |--------------------------------
    | CLEAR CACHE
    |--------------------------------
    */
    private function forgetCache(int $userId): void
    {
        Cache::forget($this->cacheKey($userId));
    }
}