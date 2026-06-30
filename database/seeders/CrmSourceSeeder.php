<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\CRM\Models\CrmSource;
use Illuminate\Support\Str;

class CrmSourceSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'WhatsApp', 'sort_order' => 1],
            ['name' => 'Walk In', 'sort_order' => 2],

            // ADS & SOCIAL
            ['name' => 'Meta Ads', 'sort_order' => 3],
            ['name' => 'Instagram', 'sort_order' => 4],
            ['name' => 'TikTok', 'sort_order' => 5],
            ['name' => 'Facebook', 'sort_order' => 6],

            // MARKETPLACE & DIGITAL
            ['name' => 'Marketplace', 'sort_order' => 7],
            ['name' => 'Website', 'sort_order' => 8],

            // OFFLINE / RELATION
            ['name' => 'Mediator', 'sort_order' => 9],
            ['name' => 'Event', 'sort_order' => 10],
        ];

        foreach ($data as $item) {
            CrmSource::updateOrCreate(
                [
                    'slug' => Str::slug($item['name']),
                ],
                [
                    'name' => $item['name'],
                    'slug' => Str::slug($item['name']),
                    'sort_order' => $item['sort_order'],
                    'is_active' => true,
                    'is_default' => $item['name'] === 'WhatsApp',
                ]
            );
        }

        $this->command->info('✓ CRM Sources updated successfully');
    }
}