<?php

namespace App\CRM\Models;

use App\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmLeadStageHistory extends Model
{
    use HasFactory;

    protected $table = 'crm_lead_stage_histories';

    protected $fillable = [
        'lead_id',
        'from_stage_id',
        'to_stage_id',
        'changed_by',
        'note',
        'changed_at',
    ];

    protected $casts = [
        'lead_id'       => 'integer',
        'from_stage_id' => 'integer',
        'to_stage_id'   => 'integer',
        'changed_by'    => 'integer',
        'changed_at'    => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function lead()
    {
        return $this->belongsTo(CrmLead::class, 'lead_id');
    }

    public function fromStage()
    {
        return $this->belongsTo(CrmPipelineStage::class, 'from_stage_id');
    }

    public function toStage()
    {
        return $this->belongsTo(CrmPipelineStage::class, 'to_stage_id');
    }

    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeByLead($query, int $leadId)
    {
        return $query->where('lead_id', $leadId);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('changed_by', $userId);
    }
}