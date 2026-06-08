<?php

namespace App\Auth\Services;

use App\Auth\Models\AuthGroup;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

class AuthGroupService
{
    private const CACHE_PREFIX = 'auth:user:%d:groups';

    /*
    |---------------------------------------------------
    | ADD GROUP
    |---------------------------------------------------
    */
    public function addGroup(int $userId, string ...$groups): bool
    {
        $groups = $this->validateGroups($groups);

        foreach ($groups as $group) {
            AuthGroup::firstOrCreate([
                'user_id' => $userId,
                'group'   => $group,
            ]);
        }

        $this->forgetCache($userId);

        return true;
    }

    /*
    |---------------------------------------------------
    | REMOVE GROUP
    |---------------------------------------------------
    */
    public function removeGroup(int $userId, string ...$groups): bool
    {
        $groups = $this->normalizeGroups($groups);

        AuthGroup::where('user_id', $userId)
            ->whereIn('group', $groups)
            ->delete();

        $this->forgetCache($userId);

        return true;
    }

    /*
    |---------------------------------------------------
    | SYNC GROUPS
    |---------------------------------------------------
    */
    public function syncGroups(int $userId, string ...$groups): bool
    {
        $groups = $this->validateGroups($groups);

        AuthGroup::where('user_id', $userId)->delete();

        if (!empty($groups)) {
            AuthGroup::insert(
                array_map(fn ($group) => [
                    'user_id'    => $userId,
                    'group'      => $group,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $groups)
            );
        }

        $this->forgetCache($userId);

        return true;
    }

    /*
    |---------------------------------------------------
    | GET GROUPS (CACHED)
    |---------------------------------------------------
    */
    public function getGroups(int $userId): array
    {
        return Cache::remember(
            $this->cacheKey($userId),
            now()->addHours(12),
            fn () => AuthGroup::where('user_id', $userId)
                ->pluck('group')
                ->map(fn ($group) => strtolower(trim($group)))
                ->unique()
                ->values()
                ->toArray()
        );
    }

    /*
    |---------------------------------------------------
    | AVAILABLE GROUPS (FROM CONFIG)
    |---------------------------------------------------
    */
    public function getAvailableGroups(): array
    {
        return Cache::remember('auth:available_groups', 3600, function () {
            return array_map('strtolower', config('auth_groups.groups', []));
        });
    }

    /*
    |---------------------------------------------------
    | DEFAULT GROUP
    |---------------------------------------------------
    */
    public function addToDefaultGroup(int $userId): bool
    {
        return $this->addGroup(
            $userId,
            config('auth_groups.defaultGroup')
        );
    }

    /*
    |---------------------------------------------------
    | VALIDATION
    |---------------------------------------------------
    */
    private function validateGroups(array $groups): array
    {
        $groups = $this->normalizeGroups($groups);

        $availableGroups = array_map(
            'strtolower',
            array_keys(config('auth_groups.groups', []))
        );

        foreach ($groups as $group) {
            if (!in_array($group, $availableGroups, true)) {
                throw new InvalidArgumentException("Group '{$group}' not found.");
            }
        }

        return $groups;
    }

    /*
    |---------------------------------------------------
    | NORMALIZER
    |---------------------------------------------------
    */
    private function normalizeGroups(array $groups): array
    {
        return collect($groups)
            ->map(fn ($group) => strtolower(trim($group)))
            ->unique()
            ->values()
            ->toArray();
    }

    /*
    |---------------------------------------------------
    | CACHE HELPERS
    |---------------------------------------------------
    */
    private function cacheKey(int $userId): string
    {
        return sprintf(self::CACHE_PREFIX, $userId);
    }

    private function forgetCache(int $userId): void
    {
        Cache::forget($this->cacheKey($userId));
    }
}