<?php

namespace App\CRM\Models;

use App\Models\Branch;
use App\User\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmLead extends Model
{
    protected $table = 'crm_leads';

    protected $fillable = [
        'lead_code',
        'lead_source_id',
        'pipeline_id',
        'pipeline_stage_id',
        'name',
        'phone',
        'email',
        'address',
        'interest_id',
        'notes',
        'assigned_to',
        'branch_id',
        'created_by',
        'province_code',
        'province_name',
        'city_code',
        'city_name',
        'district_code',
        'district_name',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // MUTATORS
    // -------------------------------------------------------------------------

    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value): ?string => $value
                ? ucwords(strtolower(trim(preg_replace('/\s+/', ' ', $value))))
                : null,
        );
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            set: function (?string $value): ?string {
                if (! $value) return null;

                $value = preg_replace('/[^0-9]/', '', $value);

                return match (true) {
                    str_starts_with($value, '0') => '62' . substr($value, 1),
                    str_starts_with($value, '8') => '62' . $value,
                    default                       => $value,
                };
            },
        );
    }

    // -------------------------------------------------------------------------
    // ACCESSORS
    // -------------------------------------------------------------------------

    /**
     * Format: +62 812-3456-7890
     * Panggil eksplisit: $lead->phone_display
     */
    protected function phoneDisplay(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->phone
                ? preg_replace('/(\d{2})(\d{3})(\d{4})(\d{4})/', '+$1 $2-$3-$4', $this->phone)
                : null,
        );
    }

    /**
     * Panggil eksplisit: $lead->temperature
     */
    public function getTemperatureAttribute(): ?string
    {
        return $this->stage?->temperature;
    }

    /**
     * Panggil eksplisit: $lead->temperature_label
     */
    public function getTemperatureLabelAttribute(): string
    {
        return $this->stage?->temperature_label ?? 'Cold';
    }

    // -------------------------------------------------------------------------
    // TEMPERATURE CHECKS
    // -------------------------------------------------------------------------

    public function isCold(): bool     { return $this->temperature === CrmPipelineStage::TEMP_COLD; }
    public function isWarm(): bool     { return $this->temperature === CrmPipelineStage::TEMP_WARM; }
    public function isHot(): bool      { return $this->temperature === CrmPipelineStage::TEMP_HOT; }
    public function isCustomer(): bool { return $this->temperature === CrmPipelineStage::TEMP_CUSTOMER; }
    public function isLost(): bool     { return $this->temperature === CrmPipelineStage::TEMP_LOST; }

    // -------------------------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------------------------

    public function source(): BelongsTo
    {
        return $this->belongsTo(CrmLeadSource::class, 'lead_source_id');
    }

    public function interest(): BelongsTo
    {
        return $this->belongsTo(CrmInterest::class, 'interest_id');
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(CrmPipeline::class, 'pipeline_id');
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(CrmPipelineStage::class, 'pipeline_stage_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(CrmTask::class, 'lead_id')->latest();
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CrmActivity::class, 'lead_id')->latest();
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CrmTransaction::class, 'lead_id')->latest();
    }
}