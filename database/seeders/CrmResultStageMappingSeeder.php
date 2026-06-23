<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\CRM\Models\CrmPipeline;
use App\CRM\Models\CrmResult;
use App\CRM\Models\CrmPipelineStage;
use App\CRM\Models\CrmResultStageMapping;

class CrmResultStageMappingSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | CASH PIPELINE MAPPING
        |--------------------------------------------------------------------------
        */
        $cash = CrmPipeline::where('name', 'Cash')->first();

        if ($cash) {

            $this->map($cash, [
                'no_response'    => 'Contacted',
                'interested'     => 'Qualified',
                'follow_up'      => 'Contacted',

                // 👉 TAMBAHAN FLOW PROSPECT
                'qualified'      => 'Prospek',

                'deal'           => 'Deal',
                'success'        => 'Won',
                'not_interested' => 'Lost',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CREDIT PIPELINE MAPPING
        |--------------------------------------------------------------------------
        */
        $credit = CrmPipeline::where('name', 'Credit')->first();

        if ($credit) {

            $this->map($credit, [
                'no_response' => 'Contacted',
                'interested'  => 'Qualified',
                'follow_up'   => 'Contacted',

                // 👉 TAMBAHAN FLOW PROSPECT
                'qualified'   => 'Prospek',

                'submitted'   => 'Poling',
                'survey'      => 'Survey',
                'approved'    => 'Acc',
                'rejected'    => 'Reject',

                'dp'          => 'Deal',
                'success'     => 'Won',
                'not_interested' => 'Lost',
            ]);
        }
    }

    private function map($pipeline, array $maps): void
    {
        foreach ($maps as $resultCode => $stageName) {

            $result = CrmResult::where('code', $resultCode)->first();

            $stage = CrmPipelineStage::where('pipeline_id', $pipeline->id)
                ->where('name', $stageName)
                ->first();

            if (! $result || ! $stage) {
                continue;
            }

            CrmResultStageMapping::updateOrCreate(
                [
                    'pipeline_id' => $pipeline->id,
                    'result_id'   => $result->id,
                ],
                [
                    'target_stage_id' => $stage->id,
                    'is_active' => true,
                    'priority' => 0,
                ]
            );
        }
    }
}