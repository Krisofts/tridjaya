<?php

namespace Database\Seeders;

use App\CRM\Models\CrmInterest;
use Illuminate\Database\Seeder;

class CrmInterestSeeder extends Seeder
{
    public function run(): void
    {
        $interests = [
            'Elektronik',
            'Furniture',
            'Gadget',
            'Alat Tani',
        ];

        foreach ($interests as $index => $name) {
            CrmInterest::updateOrCreate(
                ['name' => $name],
                ['is_active' => true, 'sort_order' => $index + 1],
            );
        }

        $this->command->info('✓ Interests berhasil di-seed.');
    }
}