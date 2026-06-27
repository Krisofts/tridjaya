<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WilayahSeeder extends Seeder
{
    private string $baseUrl = 'https://wilayah.id/api';

    public function run(): void
    {
        $this->command->info('Memulai seeding data wilayah Indonesia...');
        $this->command->newLine();

        // ─────────────────────────────────────────────
        // 1. PROVINSI
        // ─────────────────────────────────────────────

        $this->command->info('→ Mengambil data provinsi...');

        $provinces = $this->fetch('provinces.json');

        if (empty($provinces)) {
            $this->command->error('Gagal mengambil data provinsi. Seeding dibatalkan.');
            return;
        }

        DB::table('wilayah_provinsi')->truncate();
        DB::table('wilayah_provinsi')->insert(
            collect($provinces)->map(fn ($p) => [
                'code' => $p['code'],
                'name' => $p['name'],
            ])->toArray()
        );

        $this->command->info("   ✓ {$provinces->count()} provinsi berhasil disimpan.");
        $this->command->newLine();

        // ─────────────────────────────────────────────
        // 2. KOTA / KABUPATEN
        // ─────────────────────────────────────────────

        $this->command->info('→ Mengambil data kota/kabupaten...');

        DB::table('wilayah_kota')->truncate();

        $bar = $this->command->getOutput()->createProgressBar($provinces->count());
        $bar->start();

        $provinces->each(function ($province) use ($bar) {
            $regencies = $this->fetch("regencies/{$province['code']}.json");

            if ($regencies->isNotEmpty()) {
                DB::table('wilayah_kota')->insert(
                    $regencies->map(fn ($r) => [
                        'code'          => $r['code'],
                        'province_code' => $province['code'],
                        'name'          => $r['name'],
                    ])->toArray()
                );
            }

            $bar->advance();
        });

        $bar->finish();
        $this->command->newLine();
        $this->command->info('   ✓ Data kota/kabupaten berhasil disimpan.');
        $this->command->newLine();

        // ─────────────────────────────────────────────
        // 3. KECAMATAN
        // ─────────────────────────────────────────────

        $this->command->info('→ Mengambil data kecamatan (ini membutuhkan waktu lebih lama)...');

        DB::table('wilayah_kecamatan')->truncate();

        $cities = DB::table('wilayah_kota')->get();
        $bar    = $this->command->getOutput()->createProgressBar($cities->count());
        $bar->start();

        $cities->each(function ($city) use ($bar) {
            $districts = $this->fetch("districts/{$city->code}.json");

            if ($districts->isNotEmpty()) {
                DB::table('wilayah_kecamatan')->insert(
                    $districts->map(fn ($d) => [
                        'code'      => $d['code'],
                        'city_code' => $city->code,
                        'name'      => $d['name'],
                    ])->toArray()
                );
            }

            $bar->advance();
        });

        $bar->finish();
        $this->command->newLine();
        $this->command->info('   ✓ Data kecamatan berhasil disimpan.');
        $this->command->newLine();

        $this->command->info('✅ Seeding wilayah selesai!');
    }

    // ─────────────────────────────────────────────────
    // PRIVATE
    // ─────────────────────────────────────────────────

    private function fetch(string $endpoint): \Illuminate\Support\Collection
    {
        try {
            $response = Http::retry(3, 500)
                ->timeout(15)
                ->get("{$this->baseUrl}/{$endpoint}");

            if (! $response->successful()) {
                Log::warning("WilayahSeeder: gagal fetch {$endpoint}", [
                    'status' => $response->status(),
                ]);
                return collect();
            }

            return collect($response->json('data') ?? []);
        } catch (\Throwable $e) {
            Log::error("WilayahSeeder: error fetch {$endpoint}", [
                'message' => $e->getMessage(),
            ]);
            return collect();
        }
    }
}