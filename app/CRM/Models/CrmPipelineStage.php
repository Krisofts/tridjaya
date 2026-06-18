<?php

namespace App\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmPipelineStage extends Model
{
    protected $table = 'crm_pipeline_stages';

    protected $fillable = [
        'pipeline_id',
        'name',
        'sort_order',
        'color',
        'is_default',
        'is_won',
        'is_lost',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_won' => 'boolean',
        'is_lost' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(
            CrmPipeline::class,
            'pipeline_id'
        );
    }

    public function leads(): HasMany
    {
        return $this->hasMany(
            CrmLead::class,
            'pipeline_stage_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isWon(): bool
    {
        return $this->is_won;
    }

    public function isLost(): bool
    {
        return $this->is_lost;
    }
}
