<?php

namespace App\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmPipelineStageTask extends Model
{
    protected $table = 'crm_pipeline_stage_tasks';

    protected $fillable = [
        'pipeline_stage_id',
        'title',
        'description',
        'type',
        'priority',
        'due_after_minutes',
        'reminder_before_minutes',
        'is_active',
    ];

    protected $casts = [
        'is_active'               => 'boolean',
        'due_after_minutes'       => 'integer',
        'reminder_before_minutes' => 'integer',
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

    public function stage(): BelongsTo
    {
        return $this->belongsTo(CrmPipelineStage::class, 'pipeline_stage_id');
    }
}