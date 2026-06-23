<?php

namespace App\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use App\CRM\Models\CrmResult;
use App\CRM\Models\CrmPipeline;
use App\CRM\Models\CrmPipelineStage;

class CrmResultStageMapping extends Model
{
    protected $table = 'crm_result_stage_mappings';

    protected $fillable = [
        'pipeline_id',
        'result_id',
        'target_stage_id',
        'priority',
        'is_active',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function pipeline()
    {
        return $this->belongsTo(CrmPipeline::class, 'pipeline_id');
    }

    public function result()
    {
        return $this->belongsTo(CrmResult::class, 'result_id');
    }

    public function targetStage()
    {
        return $this->belongsTo(CrmPipelineStage::class, 'target_stage_id');
    }
}