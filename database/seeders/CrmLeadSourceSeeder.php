<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\CRM\Models\CrmLeadSource;

class CrmLeadSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            'Walk In',
            'Facebook',
            'Instagram',
            'TikTok',
            'WhatsApp',
            'Ads',
            'Website',
            'Event',
        ];

        foreach ($sources as $source) {
            CrmLeadSource::firstOrCreate([
                'name' => $source,
            ]);
        }
    }
}