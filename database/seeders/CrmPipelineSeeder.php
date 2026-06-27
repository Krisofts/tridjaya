<?php

namespace Database\Seeders;

use App\CRM\Models\CrmPipeline;
use Illuminate\Database\Seeder;

class CrmPipelineSeeder extends Seeder
{
    public function run(): void
    {
        $pipelines = [
            [
                'name'        => 'Cash',
                'description' => 'Pipeline untuk transaksi tunai',
                'color'       => '#10B981',
                'is_active'   => true,
            ],
            [
                'name'        => 'Kredit',
                'description' => 'Pipeline untuk transaksi kredit / leasing',
                'color'       => '#F59E0B',
                'is_active'   => true,
            ],
        ];

        foreach ($pipelines as $pipeline) {
            CrmPipeline::updateOrCreate(
                ['name' => $pipeline['name']],
                $pipeline,
            );
        }

        $this->command->info('✓ Pipelines berhasil di-seed.');
    }
}