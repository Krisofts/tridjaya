<?php

namespace App\CRM\Models;

use App\Models\Branch;
use App\User\Models\User;
use App\Services\RegionService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
        'interest',
        'notes',
        'assigned_to',
        'branch_id',
        'created_by',

        // 🌍 REGION
        'province_code',
        'city_code',
        'district_code',
    ];

    protected $appends = [
        'temperature',
        'temperature_label',
        'region',
    ];

    /*
    |--------------------------------------------------------------------------
    | PHONE MUTATOR (AUTO FORMAT 62)
    |--------------------------------------------------------------------------
    */
    protected function phone(): Attribute
{
    return Attribute::make(
        set: function ($value) {
            if (!$value) return null;

            // remove all non digits
            $value = preg_replace('/[^0-9]/', '', $value);

            // 08xxxx -> 628xxxx
            if (str_starts_with($value, '0')) {
                $value = '62' . substr($value, 1);
            }

            // 8xxxx -> 628xxxx
            if (str_starts_with($value, '8')) {
                $value = '62' . $value;
            }

            return $value;
        }
    );
}

/*
|--------------------------------------------------------------------------
| NAME MUTATOR (TITLE CASE)
|--------------------------------------------------------------------------
*/
protected function name(): Attribute
{
    return Attribute::make(
        set: function ($value) {
            if (!$value) return null;

            // rapikan spasi berlebih
            $value = trim(preg_replace('/\s+/', ' ', $value));

            // ubah ke Title Case
            return ucwords(strtolower($value));
        }
    );
}

    /*
    |--------------------------------------------------------------------------
    | OPTIONAL: DISPLAY FORMAT PHONE
    |--------------------------------------------------------------------------
    */
    protected function phoneDisplay(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->phone) return null;

                // +62 812-3456-7890 style simple format
                return preg_replace('/(\d{2})(\d{3})(\d{4})(\d{4})/', '+$1 $2-$3-$4', $this->phone);
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function source(): BelongsTo
    {
        return $this->belongsTo(CrmLeadSource::class, 'lead_source_id');
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

    /*
    |--------------------------------------------------------------------------
    | TASKS
    |--------------------------------------------------------------------------
    */

    public function tasks(): HasMany
    {
        return $this->hasMany(CrmTask::class, 'lead_id')->latest();
    }

    /*
    |--------------------------------------------------------------------------
    | ACTIVITIES
    |--------------------------------------------------------------------------
    */

    public function activities(): HasMany
    {
        return $this->hasMany(CrmActivity::class, 'lead_id')->latest();
    }

    /*
    |--------------------------------------------------------------------------
    | REGION ACCESSOR
    |--------------------------------------------------------------------------
    */

    public function getRegionAttribute(): array
    {
        return app(RegionService::class)->resolve(
            $this->province_code,
            $this->city_code,
            $this->district_code
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TEMPERATURE ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getTemperatureAttribute(): ?string
    {
        return $this->stage?->temperature;
    }

    public function getTemperatureLabelAttribute(): string
    {
        return $this->stage?->temperature_label ?? 'Cold';
    }

    public function getIsColdAttribute(): bool
    {
        return $this->temperature === CrmPipelineStage::TEMP_COLD;
    }

    public function getIsWarmAttribute(): bool
    {
        return $this->temperature === CrmPipelineStage::TEMP_WARM;
    }

    public function getIsHotAttribute(): bool
    {
        return $this->temperature === CrmPipelineStage::TEMP_HOT;
    }
}