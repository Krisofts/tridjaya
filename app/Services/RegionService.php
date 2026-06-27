<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RegionService
{
    // Cache 30 hari — data wilayah sangat jarang berubah
    private const TTL = 60 * 60 * 24 * 30;

    // -------------------------------------------------------------------------
    // PROVINCES
    // -------------------------------------------------------------------------

    public function provinces(): array
    {
        return Cache::remember('wilayah:provinces', self::TTL, fn () =>
            DB::table('wilayah_provinsi')
                ->orderBy('name')
                ->pluck('name', 'code')
                ->toArray()
        );
    }

    // -------------------------------------------------------------------------
    // REGENCIES (KOTA / KABUPATEN)
    // -------------------------------------------------------------------------

    public function regencies(string $provinceCode): array
    {
        return Cache::remember("wilayah:regencies:{$provinceCode}", self::TTL, fn () =>
            DB::table('wilayah_kota')
                ->where('province_code', $provinceCode)
                ->orderBy('name')
                ->pluck('name', 'code')
                ->toArray()
        );
    }

    // -------------------------------------------------------------------------
    // DISTRICTS (KECAMATAN)
    // -------------------------------------------------------------------------

    public function districts(string $cityCode): array
    {
        return Cache::remember("wilayah:districts:{$cityCode}", self::TTL, fn () =>
            DB::table('wilayah_kecamatan')
                ->where('city_code', $cityCode)
                ->orderBy('name')
                ->pluck('name', 'code')
                ->toArray()
        );
    }

    // -------------------------------------------------------------------------
    // RESOLVE — ambil nama dari kode, reuse cache yang sudah ada
    // -------------------------------------------------------------------------

    public function resolve(
        ?string $provinceCode,
        ?string $cityCode,
        ?string $districtCode,
    ): array {
        return [
            'province_name' => $provinceCode
                ? ($this->provinces()[$provinceCode] ?? null)
                : null,

            'city_name' => ($provinceCode && $cityCode)
                ? ($this->regencies($provinceCode)[$cityCode] ?? null)
                : null,

            'district_name' => ($cityCode && $districtCode)
                ? ($this->districts($cityCode)[$districtCode] ?? null)
                : null,
        ];
    }

    // -------------------------------------------------------------------------
    // VILLAGES (opsional)
    // -------------------------------------------------------------------------

    public function villages(string $districtCode): array
    {
        return [];
    }

    // -------------------------------------------------------------------------
    // FLUSH — hapus semua cache wilayah
    // Panggil setelah re-seed: app(RegionService::class)->flush()
    // -------------------------------------------------------------------------

    public function flush(): void
    {
        Cache::forget('wilayah:provinces');

        DB::table('wilayah_provinsi')
            ->pluck('code')
            ->each(fn (string $code) => Cache::forget("wilayah:regencies:{$code}"));

        DB::table('wilayah_kota')
            ->pluck('code')
            ->each(fn (string $code) => Cache::forget("wilayah:districts:{$code}"));
    }
}