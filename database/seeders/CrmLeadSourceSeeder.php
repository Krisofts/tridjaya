<?php

namespace Database\Seeders;

use App\CRM\Models\CrmLeadSource;
use Illuminate\Database\Seeder;

class CrmLeadSourceSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            'Walk In',
            'Facebook',
            'Instagram',
            'TikTok',
            'WhatsApp',
            'Referral',
            'Ads',
            'Website',
            'Event',
        ];

        foreach ($sources as $index => $name) {
            CrmLeadSource::updateOrCreate(
                ['name' => $name],
                ['is_active' => true, 'sort_order' => $index + 1],
            );
        }

        $this->command->info('✓ Lead sources berhasil di-seed.');
    }
}