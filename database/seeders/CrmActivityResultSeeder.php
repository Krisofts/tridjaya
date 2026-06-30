<?php

namespace Database\Seeders;

use App\CRM\Models\CrmActivityResult;
use App\CRM\Models\CrmActivityType;
use Illuminate\Database\Seeder;

class CrmActivityResultSeeder extends Seeder
{
    public function run(): void
    {
        $data = [

            // ----------------------------------------------------------------
            // TELEPON (slug: call)
            // ----------------------------------------------------------------
            'call' => [
                ['name' => 'Terhubung',        'slug' => 'terhubung',        'is_success' => true,  'is_default' => true,  'sort_order' => 1],
                ['name' => 'Tidak Angkat',      'slug' => 'tidak-angkat',     'is_success' => false, 'is_default' => false, 'sort_order' => 2],
                ['name' => 'Sibuk',             'slug' => 'sibuk',            'is_success' => false, 'is_default' => false, 'sort_order' => 3],
                ['name' => 'Salah Nomor',       'slug' => 'salah-nomor',      'is_success' => false, 'is_default' => false, 'sort_order' => 4],
                ['name' => 'Nomor Tidak Aktif', 'slug' => 'nomor-tidak-aktif','is_success' => false, 'is_default' => false, 'sort_order' => 5],
            ],

            // ----------------------------------------------------------------
            // WHATSAPP (slug: whatsapp)
            // ----------------------------------------------------------------
            'whatsapp' => [
                ['name' => 'Dibalas',      'slug' => 'dibalas',      'is_success' => true,  'is_default' => true,  'sort_order' => 1],
                ['name' => 'Dibaca',       'slug' => 'dibaca',       'is_success' => false, 'is_default' => false, 'sort_order' => 2],
                ['name' => 'Tidak Dibaca', 'slug' => 'tidak-dibaca', 'is_success' => false, 'is_default' => false, 'sort_order' => 3],
                ['name' => 'Dikirim',      'slug' => 'dikirim',      'is_success' => false, 'is_default' => false, 'sort_order' => 4],
            ],

            // ----------------------------------------------------------------
            // KUNJUNGAN (slug: visit)
            // ----------------------------------------------------------------
            'visit' => [
                ['name' => 'Bertemu',      'slug' => 'bertemu',      'is_success' => true,  'is_default' => true,  'sort_order' => 1],
                ['name' => 'Tidak Hadir',  'slug' => 'tidak-hadir',  'is_success' => false, 'is_default' => false, 'sort_order' => 2],
                ['name' => 'Reschedule',   'slug' => 'reschedule',   'is_success' => false, 'is_default' => false, 'sort_order' => 3],
            ],

            // ----------------------------------------------------------------
            // SISTEM (slug: sistem) — dipakai closing activity otomatis
            // ----------------------------------------------------------------
            'sistem' => [
                ['name' => 'Lead Won',    'slug' => 'lead-won',    'is_success' => true,  'is_default' => false, 'sort_order' => 1],
                ['name' => 'Lead Lost',   'slug' => 'lead-lost',   'is_success' => false, 'is_default' => false, 'sort_order' => 2],
                ['name' => 'Lead Reopen', 'slug' => 'lead-reopen', 'is_success' => false, 'is_default' => false, 'sort_order' => 3],
            ],

        ];

        // Load semua type sekaligus — hindari N+1 query
        $types = CrmActivityType::whereIn('slug', array_keys($data))
            ->pluck('id', 'slug');

        foreach ($data as $typeSlug => $results) {

            if (! $types->has($typeSlug)) {
                $this->command->warn("⚠  Activity type [{$typeSlug}] tidak ditemukan. Jalankan CrmActivityTypeSeeder lebih dulu.");
                continue;
            }

            $typeId = $types->get($typeSlug);

            foreach ($results as $result) {
                CrmActivityResult::updateOrCreate(
                    [
                        'activity_type_id' => $typeId,
                        'slug'             => $result['slug'],
                    ],
                    array_merge($result, [
                        'activity_type_id' => $typeId,
                        'is_active'        => true,
                    ])
                );
            }

            $this->command->info("✓ Results [{$typeSlug}] berhasil di-seed.");
        }
    }
}