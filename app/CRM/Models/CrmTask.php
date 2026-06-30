<?php

namespace App\CRM\Models;

use App\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_tasks';

    public const PRIORITY_LOW    = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH   = 'high';

    public const STATUS_OPEN      = 'open';
    public const STATUS_DONE      = 'done';
    public const STATUS_CANCELLED = 'cancelled';

    public const PRIORITIES = [
        self::PRIORITY_LOW,
        self::PRIORITY_MEDIUM,
        self::PRIORITY_HIGH,
    ];

    public const STATUSES = [
        self::STATUS_OPEN,
        self::STATUS_DONE,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'lead_id',
        'assigned_to',
        'created_by',
        'title',
        'description',
        'priority',
        'status',
        'due_at',
        'done_at',
        'is_reminded',
    ];

    protected $casts = [
        'lead_id'     => 'integer',
        'assigned_to' => 'integer',
        'created_by'  => 'integer',
        'due_at'      => 'datetime',
        'done_at'     => 'datetime',
        'is_reminded' => 'boolean',
        'deleted_at'  => 'datetime',
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

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
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

    public function scopeDone($query)
    {
        return $query->where('status', self::STATUS_DONE);
    }

    public function scopeOverdue($query)
    {
        return $query->open()->where('due_at', '<', now());
    }

    public function scopeDueToday($query)
    {
        return $query->open()->whereDate('due_at', today());
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeAssignedTo($query, int $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeForLead($query, int $leadId)
    {
        return $query->where('lead_id', $leadId);
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

    public function isDone(): bool
    {
        return $this->status === self::STATUS_DONE;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isOverdue(): bool
    {
        return $this->isOpen() && $this->due_at->isPast();
    }

    public function priorityLabel(): string
    {
        return match ($this->priority) {
            self::PRIORITY_HIGH => 'Tinggi',
            self::PRIORITY_LOW  => 'Rendah',
            default             => 'Sedang',
        };
    }

    public function priorityColor(): string
    {
        return match ($this->priority) {
            self::PRIORITY_HIGH => 'red',
            self::PRIORITY_LOW  => 'gray',
            default             => 'yellow',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DONE      => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default                => 'Open',
        };
    }
}