<?php

namespace Database\Seeders;

use App\CRM\Models\CrmActivityType;
use Illuminate\Database\Seeder;

class CrmActivityTypeSeeder extends Seeder
{
    public function run(): void
    {
        $activities = [

            [
                'name'        => 'Telepon',
                'slug'        => 'call',
                'sort_order'  => 1,
                'is_default'  => true,
            ],

            [
                'name'        => 'WhatsApp',
                'slug'        => 'whatsapp',
                'sort_order'  => 2,
            ],

            [
                'name'        => 'Kunjungan',
                'slug'        => 'visit',
                'sort_order'  => 3,
            ],

            // ----------------------------------------------------------------
            // SISTEM — untuk closing activity otomatis (Won / Lost / Reopen)
            // is_active = false agar tidak muncul di dropdown form sales
            // ----------------------------------------------------------------
            [
                'name'        => 'Sistem',
                'slug'        => 'sistem',
                'sort_order'  => 99,
                'is_active'   => false,
                'description' => 'Aktivitas otomatis dari sistem (tidak tampil di form)',
            ],

        ];

        foreach ($activities as $activity) {
            CrmActivityType::updateOrCreate(
                ['slug' => $activity['slug']],
                array_merge([
                    'is_default'  => false,
                    'is_active'   => true,
                    'description' => null,
                ], $activity)
            );
        }

        $this->command->info('✓ Activity Types berhasil di-seed.');
    }
}