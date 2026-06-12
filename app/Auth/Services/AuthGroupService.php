<?php

namespace App\Auth\Services;

use App\Auth\Models\AuthGroup;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

class AuthGroupService
{
    private const CACHE_PREFIX = 'auth:user:%d:groups';

    /*
    |--------------------------------
    | ADD GROUP
    |--------------------------------
    */
    public function addGroup(int $userId, string ...$groups): bool
    {
        $groups = $this->normalize($groups);
        $this->validate($groups);

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
    |--------------------------------
    | REMOVE GROUP
    |--------------------------------
    */
    public function removeGroup(int $userId, string ...$groups): bool
    {
        $groups = $this->normalize($groups);

        AuthGroup::where('user_id', $userId)
            ->whereIn('group', $groups)
            ->delete();

        $this->forgetCache($userId);

        return true;
    }

    /*
    |--------------------------------
    | SYNC GROUPS (REPLACE ALL)
    |--------------------------------
    */
    public function syncGroups(int $userId, string ...$groups): bool
    {
        $groups = $this->normalize($groups);
        $this->validate($groups);

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
    |--------------------------------
    | GET GROUPS (CACHED)
    |--------------------------------
    */
    public function getGroups(int $userId): array
    {
        return Cache::remember(
            $this->cacheKey($userId),
            now()->addHours(12),
            fn () => AuthGroup::where('user_id', $userId)
                ->pluck('group')
                ->map(fn ($g) => strtolower(trim($g)))
                ->unique()
                ->values()
                ->toArray()
        );
    }

    /*
    |--------------------------------
    | AVAILABLE GROUPS (CONFIG)
    |--------------------------------
    */
    public function getAvailableGroups(): array
    {
        return Cache::remember('auth:available_groups', 3600, function () {
            return config('auth_groups.groups', []);
        });
    }

    /*
    |--------------------------------
    | ADD DEFAULT GROUP
    |--------------------------------
    */
    public function addToDefaultGroup(int $userId): bool
    {
        $default = config('auth_groups.defaultGroup');

        if (!$default) {
            throw new InvalidArgumentException('Default group is not configured.');
        }

        return $this->addGroup($userId, $default);
    }

    /*
    |--------------------------------
    | VALIDATION
    |--------------------------------
    */
    private function validate(array $groups): void
    {
        if (empty($groups)) {
            return;
        }

        $available = array_map(
            'strtolower',
            array_keys(config('auth_groups.groups', []))
        );

        foreach ($groups as $group) {
            if (!in_array($group, $available, true)) {
                throw new InvalidArgumentException(
                    "Group '{$group}' not found in config."
                );
            }
        }
    }

    /*
    |--------------------------------
    | NORMALIZER
    |--------------------------------
    */
    private function normalize(array $groups): array
    {
        return collect($groups)
            ->map(fn ($g) => strtolower(trim($g)))
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