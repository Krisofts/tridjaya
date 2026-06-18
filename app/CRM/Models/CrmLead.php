<?php

namespace App\CRM\Models;

use App\Models\Branch;
use App\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function source(): BelongsTo
    {
        return $this->belongsTo(
            CrmLeadSource::class,
            'lead_source_id'
        );
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(
            CrmPipeline::class,
            'pipeline_id'
        );
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(
            CrmPipelineStage::class,
            'pipeline_stage_id'
        );
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'assigned_to'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(
            Branch::class,
            'branch_id'
        );
    }

    public function activities(): HasMany
    {
        return $this->hasMany(
            CrmActivity::class,
            'lead_id'
        )->latest();
    }
}