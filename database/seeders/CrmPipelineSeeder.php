<?php

namespace Database\Seeders;

use App\CRM\Models\CrmPipeline;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CrmPipelineSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'Cash',
                'sort_order' => 1,
            ],
            [
                'name' => 'Kredit',
                'sort_order' => 2,
            ],
        ];

        foreach ($data as $item) {
            CrmPipeline::updateOrCreate(
                [
                    'slug' => Str::slug($item['name']),
                ],
                [
                    'name' => $item['name'],
                    'slug' => Str::slug($item['name']),
                    'sort_order' => $item['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('✓ CRM Pipelines seeded successfully');
    }
}