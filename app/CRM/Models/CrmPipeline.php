<?php

namespace App\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CrmPipeline extends Model
{
    protected $table = 'crm_pipelines';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // SCOPES
    // -------------------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // -------------------------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------------------------

    /**
     * Semua stage pada pipeline.
     */
    public function stages(): HasMany
    {
        return $this->hasMany(CrmPipelineStage::class, 'pipeline_id')
            ->orderBy('sort_order');
    }

    /**
     * Stage default saat lead dibuat.
     */
    public function defaultStage(): HasOne
    {
        return $this->hasOne(CrmPipelineStage::class, 'pipeline_id')
            ->where('is_default', true);
    }

    /**
     * Semua lead pada pipeline.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(CrmLead::class, 'pipeline_id');
    }
}