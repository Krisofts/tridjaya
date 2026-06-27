<?php

namespace App\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmResult extends Model
{
    protected $table = 'crm_results';

    protected $fillable = [
        'pipeline_id',
        'name',
        'code',
        'color',
        'is_active',
        'sort_order',
        'is_terminal',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_terminal' => 'boolean',
        'sort_order'  => 'integer',
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

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(CrmPipeline::class, 'pipeline_id');
    }

    public function stageMappings(): HasMany
    {
        return $this->hasMany(CrmResultStageMapping::class, 'result_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(CrmTask::class, 'result_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CrmActivity::class, 'result_id');
    }
}