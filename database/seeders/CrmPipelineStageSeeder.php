<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\CRM\Models\CrmPipeline;
use App\CRM\Models\CrmPipelineStage;

class CrmPipelineStageSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | CASH PIPELINE
        |--------------------------------------------------------------------------
        */
        $cashPipeline = CrmPipeline::where('name', 'Cash')->first();

        if ($cashPipeline) {

            $cashStages = [
                [
                    'name' => 'New',
                    'sort_order' => 1,
                    'temperature' => CrmPipelineStage::TEMP_COLD,
                ],
                [
                    'name' => 'Contacted',
                    'sort_order' => 2,
                    'temperature' => CrmPipelineStage::TEMP_COLD,
                ],
                [
                    'name' => 'Qualified',
                    'sort_order' => 3,
                    'temperature' => CrmPipelineStage::TEMP_WARM,
                ],
                [
                    'name' => 'Prospek',
                    'sort_order' => 4,
                    'temperature' => CrmPipelineStage::TEMP_WARM,
                ],
                [
                    'name' => 'Deal',
                    'sort_order' => 5,
                    'temperature' => CrmPipelineStage::TEMP_HOT,
                ],

                [
                    'name' => 'Won',
                    'sort_order' => 6,
                    'temperature' => CrmPipelineStage::TEMP_CUSTOMER,
                    'is_won' => true,
                ],
                [
                    'name' => 'Lost',
                    'sort_order' => 7,
                    'temperature' => CrmPipelineStage::TEMP_LOST,
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
                        'temperature' => $stage['temperature'],
                        'is_won' => $stage['is_won'] ?? false,
                        'is_lost' => $stage['is_lost'] ?? false,
                    ]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CREDIT PIPELINE
        |--------------------------------------------------------------------------
        */
        $creditPipeline = CrmPipeline::where('name', 'Credit')->first();

        if ($creditPipeline) {

            $creditStages = [
                [
                    'name' => 'New',
                    'sort_order' => 1,
                    'temperature' => CrmPipelineStage::TEMP_COLD,
                ],
                [
                    'name' => 'Contacted',
                    'sort_order' => 2,
                    'temperature' => CrmPipelineStage::TEMP_COLD,
                ],
                [
                    'name' => 'Qualified',
                    'sort_order' => 3,
                    'temperature' => CrmPipelineStage::TEMP_WARM,
                ],
                [
                    'name' => 'Prospek',
                    'sort_order' => 4,
                    'temperature' => CrmPipelineStage::TEMP_WARM,
                ],
                [
                    'name' => 'Poling',
                    'sort_order' => 5,
                    'temperature' => CrmPipelineStage::TEMP_HOT,
                ],
                [
                    'name' => 'Survey',
                    'sort_order' => 6,
                    'temperature' => CrmPipelineStage::TEMP_HOT,
                ],
                [
                    'name' => 'Acc',
                    'sort_order' => 7,
                    'temperature' => CrmPipelineStage::TEMP_HOT,
                ],
                [
                    'name' => 'Reject',
                    'sort_order' => 8,
                    'temperature' => CrmPipelineStage::TEMP_LOST,
                    'is_lost' => true,
                ],
                [
                    'name' => 'Won',
                    'sort_order' => 9,
                    'temperature' => CrmPipelineStage::TEMP_CUSTOMER,
                    'is_won' => true,
                ],
                [
                    'name' => 'Lost',
                    'sort_order' => 10,
                    'temperature' => CrmPipelineStage::TEMP_LOST,
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
                        'temperature' => $stage['temperature'],
                        'is_won' => $stage['is_won'] ?? false,
                        'is_lost' => $stage['is_lost'] ?? false,
                    ]
                );
            }
        }
    }
}