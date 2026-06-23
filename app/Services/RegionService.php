<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class RegionService
{
    private string $baseUrl;
    private int $ttl; // cache lifetime

    public function __construct()
    {
        $this->baseUrl = config('services.region.base_url');
        $this->ttl = 60 * 60 * 24 * 30; // 30 hari (jarang berubah)
    }

    /*
    |--------------------------------------------------------------------------
    | MAP RESPONSE
    |--------------------------------------------------------------------------
    */
    private function map(array $data): array
    {
        return collect($data)
            ->mapWithKeys(fn ($item) => [
                $item['code'] => $item['name'],
            ])
            ->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | CACHE KEY
    |--------------------------------------------------------------------------
    */
    private function cacheKey(string $endpoint): string
    {
        return "region_api:" . md5($endpoint);
    }

    /*
    |--------------------------------------------------------------------------
    | HTTP REQUEST (CACHE FIRST + REVALIDATE READY)
    |--------------------------------------------------------------------------
    */
    private function get(string $endpoint): array
    {
        $key = $this->cacheKey($endpoint);

        return Cache::remember($key, $this->ttl, function () use ($endpoint) {
            return Http::retry(3, 500)
                ->timeout(10)
                ->get("{$this->baseUrl}/{$endpoint}")
                ->throw()
                ->json('data') ?? [];
        });
    }

    /*
    |--------------------------------------------------------------------------
    | FORCE REFRESH (REVALIDATE MANUAL)
    |--------------------------------------------------------------------------
    */
    public function refresh(string $endpoint): void
    {
        $key = $this->cacheKey($endpoint);

        Cache::forget($key);

        Cache::remember($key, $this->ttl, function () use ($endpoint) {
            return Http::retry(3, 500)
                ->timeout(10)
                ->get("{$this->baseUrl}/{$endpoint}")
                ->throw()
                ->json('data') ?? [];
        });
    }

    /*
    |--------------------------------------------------------------------------
    | PROVINCES
    |--------------------------------------------------------------------------
    */
    public function provinces(): array
    {
        return $this->map(
            $this->get('provinces.json')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | REGENCIES (CITIES)
    |--------------------------------------------------------------------------
    */
    public function regencies(string $provinceCode): array
    {
        return $this->map(
            $this->get("regencies/{$provinceCode}.json")
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DISTRICTS
    |--------------------------------------------------------------------------
    */
    public function districts(string $regencyCode): array
    {
        return $this->map(
            $this->get("districts/{$regencyCode}.json")
        );
    }

    /*
    |--------------------------------------------------------------------------
    | VILLAGES
    |--------------------------------------------------------------------------
    */
    public function villages(string $districtCode): array
    {
        return $this->map(
            $this->get("villages/{$districtCode}.json")
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RESOLVE REGION
    |--------------------------------------------------------------------------
    */
    public function resolve(?string $provinceCode, ?string $cityCode, ?string $districtCode): array
    {
        $provinceName = null;
        $cityName = null;
        $districtName = null;

        if ($provinceCode) {
            $provinceName = $this->provinces()[$provinceCode] ?? null;
        }

        if ($provinceCode && $cityCode) {
            $cityName = $this->regencies($provinceCode)[$cityCode] ?? null;
        }

        if ($cityCode && $districtCode) {
            $districtName = $this->districts($cityCode)[$districtCode] ?? null;
        }

        return [
            'province' => $provinceName,
            'city' => $cityName,
            'district' => $districtName,
        ];
    }
}