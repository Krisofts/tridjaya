<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\CRM\Models\CrmPipeline;

class CrmPipelineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pipelines = [
            [
                'name' => 'Cash',
                'description' => 'Pipeline for cash transactions',
                'color' => '#10B981',
                'is_active' => true,
            ],
            [
                'name' => 'Credit',
                'description' => 'Pipeline for credit transactions',
                'color' => '#F59E0B',
                'is_active' => true,
            ],
        ];

        foreach ($pipelines as $pipeline) {

            CrmPipeline::updateOrCreate(
                ['name' => $pipeline['name']],
                $pipeline
            );
        }
    }
}