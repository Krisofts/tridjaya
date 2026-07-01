<?php

namespace App\CRM\Models;

use App\Models\District;
use App\Models\Product;
use App\Models\Province;
use App\Models\Regency;
use App\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmLead extends Model
{
    use HasFactory, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | CONSTANTS
    |--------------------------------------------------------------------------
    */

    public const STATUS_OPEN = 'open';
    public const STATUS_WON  = 'won';
    public const STATUS_LOST = 'lost';

    public const STATUSES = [
        self::STATUS_OPEN,
        self::STATUS_WON,
        self::STATUS_LOST,
    ];

    /*
    |--------------------------------------------------------------------------
    | TABLE & FILLABLE
    |--------------------------------------------------------------------------
    */

    protected $table = 'crm_leads';

    protected $fillable = [
        // Pipeline
        'pipeline_id',
        'stage_id',

        // Customer
        'name',
        'phone',

        // Relasi opsional
        'source_id',
        'product_id',
        'interest_id',

        // Assignment
        'assigned_to',
        'created_by',
        'branch_id',

        // Lokasi
        'province_id',
        'regency_id',
        'district_id',
        'address',

        // Bisnis
        'estimated_value',
        'probability',

        // Status & lifecycle
        'status',
        'lost_reason_id',
        'lost_note',
        'closed_at',

        // Timeline
        'last_activity_at',
        'next_follow_up_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        // Integer FK
        'pipeline_id'      => 'integer',
        'stage_id'         => 'integer',
        'assigned_to'      => 'integer',
        'created_by'       => 'integer',
        'branch_id'        => 'integer',
        'source_id'        => 'integer',
        'product_id'       => 'integer',
        'interest_id'      => 'integer',
        'lost_reason_id'   => 'integer',
        'province_id'      => 'integer',
        'regency_id'       => 'integer',
        'district_id'      => 'integer',

        // Numeric
        'estimated_value'  => 'decimal:2',
        'probability'      => 'integer',

        // Datetime
        'last_activity_at'   => 'datetime',
        'next_follow_up_at'  => 'datetime',
        'closed_at'          => 'datetime',
        'deleted_at'         => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function pipeline()
    {
        return $this->belongsTo(CrmPipeline::class);
    }

    public function stage()
    {
        return $this->belongsTo(CrmPipelineStage::class, 'stage_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function source()
    {
        return $this->belongsTo(CrmSource::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function interest()
    {
        return $this->belongsTo(CrmInterest::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function regency()
    {
        return $this->belongsTo(Regency::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function lostReason()
    {
        return $this->belongsTo(CrmLostReason::class);
    }

    public function notifications()
    {
        return $this->hasMany(CrmNotification::class, 'lead_id');
    }

    public function tasks()
    {
        return $this->hasMany(\App\CRM\Models\CrmTask::class, 'lead_id')
            ->orderByRaw("FIELD(status,'open','done','cancelled')")
            ->orderBy('due_at');
    }

    public function activities()
    {
        return $this->hasMany(CrmLeadActivity::class, 'lead_id')
            ->orderByDesc('activity_at');
    }

    public function stageHistories()
    {
        return $this->hasMany(CrmLeadStageHistory::class, 'lead_id', 'id')
            ->orderByDesc('created_at');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeWon($query)
    {
        return $query->where('status', self::STATUS_WON);
    }

    public function scopeLost($query)
    {
        return $query->where('status', self::STATUS_LOST);
    }

    public function scopeByPipeline($query, ?int $pipelineId)
    {
        return $query->when($pipelineId, fn ($q) => $q->where('pipeline_id', $pipelineId));
    }

    public function scopeAssignedTo($query, ?int $userId)
    {
        return $query->when($userId, fn ($q) => $q->where('assigned_to', $userId));
    }

    public function scopeByBranch($query, ?int $branchId)
    {
        return $query->when($branchId, fn ($q) => $q->where('branch_id', $branchId));
    }

    public function scopeOverdueFollowUp($query)
    {
        return $query->open()
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<', now());
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function isWon(): bool
    {
        return $this->status === self::STATUS_WON;
    }

    public function isLost(): bool
    {
        return $this->status === self::STATUS_LOST;
    }

    public function isClosed(): bool
    {
        return ! $this->isOpen();
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_WON  => 'Won',
            self::STATUS_LOST => 'Lost',
            default           => 'Open',
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_WON  => 'success',
            self::STATUS_LOST => 'danger',
            default           => 'primary',
        };
    }
}

// Tambahkan relasi ini di CrmLead model dalam section RELATIONSHIPS:
// public function tasks()
// {
//     return $this->hasMany(CrmTask::class, 'lead_id')->orderByRaw("FIELD(status,'open','done','cancelled')")->orderBy('due_at');
// }