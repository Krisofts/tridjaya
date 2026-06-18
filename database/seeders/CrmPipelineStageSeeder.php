<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\CRM\Models\CrmPipeline;
use App\CRM\Models\CrmPipelineStage;

class CrmPipelineStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cashPipeline = CrmPipeline::query()
            ->where('name', 'cash')
            ->first();

        if ($cashPipeline) {

            $cashStages = [
                [
                    'name' => 'New Lead',
                    'sort_order' => 1,
                    'is_default' => true,
                ],
                [
                    'name' => 'Contacted',
                    'sort_order' => 2,
                ],
                [
                    'name' => 'Negotiation',
                    'sort_order' => 3,
                ],
                [
                    'name' => 'Won',
                    'sort_order' => 4,
                    'is_won' => true,
                ],
                [
                    'name' => 'Lost',
                    'sort_order' => 5,
                    'is_lost' => true,
                ],
            ];

            foreach ($cashStages as $stage) {

                CrmPipelineStage::updateOrCreate(
                    [
                        'pipeline_id' => $cashPipeline->id,
                        'name' => $stage['name'],
                    ],
                    [
                        'sort_order' => $stage['sort_order'],
                        'color' => $stage['color'] ?? null,
                        'is_default' => $stage['is_default'] ?? false,
                        'is_won' => $stage['is_won'] ?? false,
                        'is_lost' => $stage['is_lost'] ?? false,
                    ]
                );
            }
        }

        $creditPipeline = CrmPipeline::query()
            ->where('name', 'credit')
            ->first();

        if ($creditPipeline) {

            $creditStages = [
                [
                    'name' => 'New Lead',
                    'sort_order' => 1,
                    'is_default' => true,
                ],
                [
                    'name' => 'Survey',
                    'sort_order' => 2,
                ],
                [
                    'name' => 'Approval',
                    'sort_order' => 3,
                ],
                [
                    'name' => 'Contract',
                    'sort_order' => 4,
                ],
                [
                    'name' => 'Won',
                    'sort_order' => 5,
                    'is_won' => true,
                ],
                [
                    'name' => 'Rejected',
                    'sort_order' => 6,
                    'is_lost' => true,
                ],
            ];

            foreach ($creditStages as $stage) {

                CrmPipelineStage::updateOrCreate(
                    [
                        'pipeline_id' => $creditPipeline->id,
                        'name' => $stage['name'],
                    ],
                    [
                        'sort_order' => $stage['sort_order'],
                        'color' => $stage['color'] ?? null,
                        'is_default' => $stage['is_default'] ?? false,
                        'is_won' => $stage['is_won'] ?? false,
                        'is_lost' => $stage['is_lost'] ?? false,
                    ]
                );
            }
        }
    }
}