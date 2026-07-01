<?php

namespace App\CRM\Models;

use App\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class CrmNotification extends Model
{
    protected $table = 'crm_notifications';

    // -------------------------------------------------------------------------
    // TYPES
    // -------------------------------------------------------------------------
    public const TYPE_TASK_OVERDUE     = 'task_overdue';
    public const TYPE_FOLLOWUP_OVERDUE = 'followup_overdue';
    public const TYPE_TASK_REMINDER    = 'task_reminder';
    public const TYPE_LEAD_WON         = 'lead_won';
    public const TYPE_LEAD_LOST        = 'lead_lost';
    public const TYPE_LEAD_ASSIGNED    = 'lead_assigned';
    public const TYPE_MANUAL           = 'manual';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'lead_id',
        'task_id',
        'action_url',
        'is_read',
        'read_at',
        'remind_at',
    ];

    protected $casts = [
        'user_id'   => 'integer',
        'lead_id'   => 'integer',
        'task_id'   => 'integer',
        'is_read'   => 'boolean',
        'read_at'   => 'datetime',
        'remind_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lead()
    {
        return $this->belongsTo(CrmLead::class);
    }

    public function task()
    {
        return $this->belongsTo(CrmTask::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDue($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('remind_at')
              ->orWhere('remind_at', '<=', now());
        });
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function markAsRead(): void
    {
        $this->update(['is_read' => true, 'read_at' => now()]);
    }

    public function iconColor(): string
    {
        return match ($this->type) {
            self::TYPE_TASK_OVERDUE     => 'red',
            self::TYPE_FOLLOWUP_OVERDUE => 'orange',
            self::TYPE_LEAD_WON         => 'green',
            self::TYPE_LEAD_LOST        => 'red',
            self::TYPE_LEAD_ASSIGNED    => 'blue',
            self::TYPE_TASK_REMINDER    => 'yellow',
            default                     => 'blue',
        };
    }

    public function icon(): string
    {
        return match ($this->type) {
            self::TYPE_TASK_OVERDUE,
            self::TYPE_TASK_REMINDER    => 'task',
            self::TYPE_FOLLOWUP_OVERDUE => 'followup',
            self::TYPE_LEAD_WON         => 'won',
            self::TYPE_LEAD_LOST        => 'lost',
            self::TYPE_LEAD_ASSIGNED    => 'assigned',
            default                     => 'bell',
        };
    }
}