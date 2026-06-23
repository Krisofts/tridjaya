<?php

namespace App\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmPipelineStage extends Model
{
    protected $table = 'crm_pipeline_stages';

    /*
    |--------------------------------------------------------------------------
    | TEMPERATURE
    |--------------------------------------------------------------------------
    */

    public const TEMP_COLD = 'cold';
    public const TEMP_WARM = 'warm';
    public const TEMP_HOT = 'hot';
    public const TEMP_CUSTOMER = 'customer';
    public const TEMP_LOST = 'lost';

    protected $fillable = [
        'pipeline_id',
        'name',
        'sort_order',
        'color',
        'temperature',
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

    public function tasks(): HasMany
    {
        return $this->hasMany(
            CrmPipelineStageTask::class,
            'pipeline_stage_id'
        )
        ->where('is_active', true)
        ->orderBy('id');
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

    public function isCold(): bool
    {
        return $this->temperature === self::TEMP_COLD;
    }

    public function isWarm(): bool
    {
        return $this->temperature === self::TEMP_WARM;
    }

    public function isHot(): bool
    {
        return $this->temperature === self::TEMP_HOT;
    }

    public function isCustomer(): bool
    {
        return $this->temperature === self::TEMP_CUSTOMER;
    }

    public function getTemperatureLabelAttribute(): string
    {
        return match ($this->temperature) {
            self::TEMP_COLD => 'Cold',
            self::TEMP_WARM => 'Warm',
            self::TEMP_HOT => 'Hot',
            self::TEMP_CUSTOMER => 'Customer',
            self::TEMP_LOST => 'Lost',
            default => 'Cold',
        };
    }
}