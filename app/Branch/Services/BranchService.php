<?php

namespace App\Branch\Services;

use App\Branch\Models\Branch;
use Illuminate\Support\Facades\Cache;

class BranchService
{
    /**
     * Get branch list for UI (id, name)
     */
    public function getList(): array
    {
        return Cache::remember($this->cacheKey('list'), 3600, function () {
            return Branch::query()
                ->active()
                ->orderBy('name')
                ->get(['id', 'name'])
                ->toArray();
        }); 
    }

    /**
     * Get branch options for select dropdown (key => value)
     */
    public function getOptions(): array
    {
        return Cache::remember($this->cacheKey('options'), 3600, function () {
            return Branch::query()
                ->active()
                ->orderBy('name')
                ->pluck('name', 'id')
                ->toArray();
        });
    }

    /**
     * Get single branch by ID (cached)
     */
    public function getById(int $id): ?array
    {
        return Cache::remember($this->cacheKey("detail.$id"), 3600, function () use ($id) {
            return Branch::query()
                ->active()
                ->where('id', $id)
                ->first(['id', 'name'])
                ?->toArray();
        });
    }

    /**
     * Clear all branch caches
     */
    public function clearCache(): void
    {
        Cache::forget($this->cacheKey('list'));
        Cache::forget($this->cacheKey('options'));

        // optional: kalau banyak detail cache
        // bisa pakai cache tagging kalau redis/memcached
    }

    /**
     * Centralized cache key
     */
    private function cacheKey(string $key): string
    {
        return "branches.$key";
    }
}