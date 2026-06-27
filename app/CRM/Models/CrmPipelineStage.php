<?php

namespace App\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmPipelineStage extends Model
{
    protected $table = 'crm_pipeline_stages';

    // -------------------------------------------------------------------------
    // TEMPERATURE CONSTANTS
    // -------------------------------------------------------------------------

    public const TEMP_COLD     = 'cold';
    public const TEMP_WARM     = 'warm';
    public const TEMP_HOT      = 'hot';
    public const TEMP_CUSTOMER = 'customer';
    public const TEMP_LOST     = 'lost';

    // -------------------------------------------------------------------------
    // FILLABLE & CASTS
    // -------------------------------------------------------------------------

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
        'is_won'     => 'boolean',
        'is_lost'    => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------------------------

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(CrmPipeline::class, 'pipeline_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(CrmLead::class, 'pipeline_stage_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(CrmPipelineStageTask::class, 'pipeline_stage_id')
            ->where('is_active', true)
            ->orderBy('id');
    }

    // -------------------------------------------------------------------------
    // TEMPERATURE HELPERS
    // -------------------------------------------------------------------------

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

    public function isLost(): bool
    {
        return $this->temperature === self::TEMP_LOST;
    }

    // -------------------------------------------------------------------------
    // STAGE HELPERS
    // -------------------------------------------------------------------------

    public function isWon(): bool
    {
        return $this->is_won;
    }

    // -------------------------------------------------------------------------
    // BADGE HELPERS (dipakai di blade)
    // -------------------------------------------------------------------------

    /**
     * Badge type berdasarkan nama stage.
     * Dipakai: <x-ui.badge :type="$lead->stage->badgeType()">
     */
    public function badgeType(): string
    {
        return match (strtolower($this->name ?? '')) {
            'new'  => 'info',
            'hot'  => 'warning',
            'won'  => 'success',
            'lost' => 'error',
            default => 'primary',
        };
    }

    /**
     * Badge type berdasarkan temperature.
     * Dipakai: <x-ui.badge :type="$lead->stage->temperatureBadgeType()">
     */
    public function temperatureBadgeType(): string
    {
        return match ($this->temperature ?? '') {
            self::TEMP_HOT      => 'error',
            self::TEMP_WARM     => 'warning',
            self::TEMP_COLD     => 'info',
            self::TEMP_CUSTOMER => 'success',
            self::TEMP_LOST     => 'dark',
            default             => 'light',
        };
    }

    /**
     * Label temperature untuk ditampilkan di UI.
     * Dipakai: {{ $lead->stage->temperatureLabel() }}
     */
    public function temperatureLabel(): string
    {
        return match ($this->temperature) {
            self::TEMP_COLD     => 'Cold',
            self::TEMP_WARM     => 'Warm',
            self::TEMP_HOT      => 'Hot',
            self::TEMP_CUSTOMER => 'Customer',
            self::TEMP_LOST     => 'Lost',
            default             => '-',
        };
    }

    // -------------------------------------------------------------------------
    // ACCESSOR
    // -------------------------------------------------------------------------

    /**
     * @deprecated Gunakan temperatureLabel() method langsung.
     * Tetap dipertahankan untuk backward compatibility.
     */
    public function getTemperatureLabelAttribute(): string
    {
        return $this->temperatureLabel();
    }
}