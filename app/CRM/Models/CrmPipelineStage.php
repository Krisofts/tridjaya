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
        'slug',
        'description',
        'sort_order',
        'probability',
        'is_default',
        'is_won',
        'is_lost',
        'is_active',
    ];

    protected $casts = [
        'probability' => 'integer',
        'sort_order' => 'integer',

        'is_default' => 'boolean',
        'is_won' => 'boolean',
        'is_lost' => 'boolean',
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
     * Pipeline
     */
    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(CrmPipeline::class, 'pipeline_id');
    }

    /**
     * Leads pada stage ini
     */
    public function leads(): HasMany
    {
        return $this->hasMany(CrmLead::class, 'stage_id');
    }

    /**
     * History sebagai stage asal
     */
    public function historiesFrom(): HasMany
    {
        return $this->hasMany(CrmLeadStageHistory::class, 'from_stage_id');
    }

    /**
     * History sebagai stage tujuan
     */
    public function historiesTo(): HasMany
    {
        return $this->hasMany(CrmLeadStageHistory::class, 'to_stage_id');
    }
}