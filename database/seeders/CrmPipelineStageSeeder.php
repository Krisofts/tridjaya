<?php

namespace Database\Seeders;

use App\CRM\Models\CrmPipeline;
use App\CRM\Models\CrmPipelineStage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CrmPipelineStageSeeder extends Seeder
{
    public function run(): void
    {
        $data = [

            // ================================================================
            // CASH
            // ================================================================
            'Cash' => [
                [
                    'name' => 'Lead Baru',
                    'slug' => 'lead-baru',
                    'sort_order' => 1,
                    'probability' => 10,
                    'is_default' => true,
                    'is_won' => false,
                    'is_lost' => false,
                ],
                [
                    'name' => 'Dihubungi',
                    'slug' => 'dihubungi',
                    'sort_order' => 2,
                    'probability' => 20,
                    'is_default' => false,
                    'is_won' => false,
                    'is_lost' => false,
                ],
                [
                    'name' => 'Tertarik',
                    'slug' => 'tertarik',
                    'sort_order' => 3,
                    'probability' => 40,
                    'is_default' => false,
                    'is_won' => false,
                    'is_lost' => false,
                ],
                [
                    'name' => 'Negosiasi',
                    'slug' => 'negosiasi',
                    'sort_order' => 4,
                    'probability' => 60,
                    'is_default' => false,
                    'is_won' => false,
                    'is_lost' => false,
                ],
                [
                    'name' => 'Deal / Sepakat',
                    'slug' => 'deal',
                    'sort_order' => 5,
                    'probability' => 80,
                    'is_default' => false,
                    'is_won' => false,
                    'is_lost' => false,
                ],
                [
                    'name' => 'Pembayaran',
                    'slug' => 'pembayaran',
                    'sort_order' => 6,
                    'probability' => 95,
                    'is_default' => false,
                    'is_won' => false,
                    'is_lost' => false,
                ],
                [
                    'name' => 'Closing',
                    'slug' => 'closing',
                    'sort_order' => 7,
                    'probability' => 100,
                    'is_default' => false,
                    'is_won' => true,
                    'is_lost' => false,
                ],
                [
                    'name' => 'Lost',
                    'slug' => 'lost',
                    'sort_order' => 8,
                    'probability' => 0,
                    'is_default' => false,
                    'is_won' => false,
                    'is_lost' => true,
                ],
            ],

            // ================================================================
            // KREDIT
            // ================================================================
            'Kredit' => [
                [
                    'name' => 'Lead Baru',
                    'slug' => 'lead-baru',
                    'sort_order' => 1,
                    'probability' => 10,
                    'is_default' => true,
                    'is_won' => false,
                    'is_lost' => false,
                ],
                [
                    'name' => 'Dihubungi',
                    'slug' => 'dihubungi',
                    'sort_order' => 2,
                    'probability' => 20,
                    'is_default' => false,
                    'is_won' => false,
                    'is_lost' => false,
                ],
                [
                    'name' => 'Tertarik',
                    'slug' => 'tertarik',
                    'sort_order' => 3,
                    'probability' => 40,
                    'is_default' => false,
                    'is_won' => false,
                    'is_lost' => false,
                ],
                [
                    'name' => 'Negosiasi',
                    'slug' => 'negosiasi',
                    'sort_order' => 4,
                    'probability' => 60,
                    'is_default' => false,
                    'is_won' => false,
                    'is_lost' => false,
                ],
                [
                    'name' => 'Proses Leasing',
                    'slug' => 'proses-leasing',
                    'sort_order' => 5,
                    'probability' => 70,
                    'is_default' => false,
                    'is_won' => false,
                    'is_lost' => false,
                ],
                [
                    'name' => 'Approval Leasing',
                    'slug' => 'approval-leasing',
                    'sort_order' => 6,
                    'probability' => 90,
                    'is_default' => false,
                    'is_won' => false,
                    'is_lost' => false,
                ],
                [
                    'name' => 'Tanda Tangan',
                    'slug' => 'tanda-tangan',
                    'sort_order' => 7,
                    'probability' => 95,
                    'is_default' => false,
                    'is_won' => false,
                    'is_lost' => false,
                ],
                [
                    'name' => 'Closing',
                    'slug' => 'closing',
                    'sort_order' => 8,
                    'probability' => 100,
                    'is_default' => false,
                    'is_won' => true,
                    'is_lost' => false,
                ],
                [
                    'name' => 'Lost',
                    'slug' => 'lost',
                    'sort_order' => 9,
                    'probability' => 0,
                    'is_default' => false,
                    'is_won' => false,
                    'is_lost' => true,
                ],
            ],

        ];

        foreach ($data as $pipelineName => $stages) {
            $pipeline = CrmPipeline::where('name', $pipelineName)->first();

            if (! $pipeline) {
                $this->command->warn("Pipeline '{$pipelineName}' tidak ditemukan, skip.");
                continue;
            }

            foreach ($stages as $stage) {
                CrmPipelineStage::updateOrCreate(
                    [
                        'pipeline_id' => $pipeline->id,
                        'slug' => $stage['slug'],
                    ],
                    array_merge($stage, [
                        'pipeline_id' => $pipeline->id,
                    ])
                );
            }

            $this->command->info("✓ {$pipelineName}: " . count($stages) . " stages");
        }

        $this->command->info('✓ Pipeline stages berhasil di-seed.');
    }
}