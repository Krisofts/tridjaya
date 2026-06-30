<?php

namespace App\CRM\Models;

use App\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmLeadActivity extends Model
{
    use HasFactory;

    protected $table = 'crm_lead_activities';

    protected $fillable = [
        'lead_id',
        'activity_type_id',
        'activity_result_id',
        'user_id',
        'activity_at',
        'title',
        'notes',
        'location',
        'stage_id',
        'is_contacted',
    ];

    protected $casts = [
        'lead_id'            => 'integer',
        'activity_type_id'   => 'integer',
        'activity_result_id' => 'integer',
        'user_id'            => 'integer',
        'stage_id'           => 'integer',
        'activity_at'        => 'datetime',
        'is_contacted'       => 'boolean',
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

    public function type()
    {
        return $this->belongsTo(CrmActivityType::class, 'activity_type_id');
    }

    public function result()
    {
        return $this->belongsTo(CrmActivityResult::class, 'activity_result_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function stage()
    {
        return $this->belongsTo(CrmPipelineStage::class, 'stage_id');
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
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, int $typeId)
    {
        return $query->where('activity_type_id', $typeId);
    }

    public function scopeContacted($query)
    {
        return $query->where('is_contacted', true);
    }

    public function scopeSuccessful($query)
    {
        return $query->whereHas('result', fn ($q) => $q->where('is_success', true));
    }

    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('activity_at');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isSuccessful(): bool
    {
        return $this->result?->is_success ?? false;
    }
}