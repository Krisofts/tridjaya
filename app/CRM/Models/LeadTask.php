<?php

namespace App\CRM\Models;

use App\CRM\Models\Lead;
use App\User\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadTask extends Model
{
    protected $table = 'tasks';

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    public const STATUS_OPEN = 'open';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_DONE = 'done';
    public const STATUS_CANCELLED = 'cancelled';

    /*
    |--------------------------------------------------------------------------
    | PRIORITY
    |--------------------------------------------------------------------------
    */

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';

    /*
    |--------------------------------------------------------------------------
    | TYPE
    |--------------------------------------------------------------------------
    */

    public const TYPE_CALL = 'call';
    public const TYPE_WHATSAPP = 'whatsapp';
    public const TYPE_VISIT = 'visit';
    public const TYPE_OTHER = 'other';

    /*
    |--------------------------------------------------------------------------
    | OUTCOME
    |--------------------------------------------------------------------------
    */

    public const OUTCOME_INTERESTED = 'interested';
    public const OUTCOME_NOT_INTERESTED = 'not_interested';
    public const OUTCOME_CALLBACK = 'callback';
    public const OUTCOME_NO_ANSWER = 'no_answer';
    public const OUTCOME_BUSY = 'busy';
    public const OUTCOME_WRONG_NUMBER = 'wrong_number';
    public const OUTCOME_SUCCESS = 'success';
    public const OUTCOME_FAILED = 'failed';

    /*
    |--------------------------------------------------------------------------
    | FILLABLE
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'lead_id',
        'title',
        'description',
        'notes',
        'type',
        'priority',
        'status',
        'outcome',
        'assigned_to',
        'created_by',
        'completed_by',
        'due_date',
        'reminder_at',
        'completed_at',
        'parent_task_id',
        'metadata',
    ];

    protected $attributes = [
        'status' => self::STATUS_OPEN,
        'priority' => self::PRIORITY_MEDIUM,
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'reminder_at' => 'datetime',
        'completed_at' => 'datetime',
        'metadata' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_task_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DONE);
    }

    public function scopeAssignedTo(Builder $query, int $userId): Builder
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
                self::STATUS_DONE,
                self::STATUS_CANCELLED,
            ])
            ->whereNotNull('due_date')
            ->where('due_date', '<', now());
    }

    public function scopeDueToday(Builder $query): Builder
    {
        return $query->whereDate('due_date', today());
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public static function priorities(): array
    {
        return [
            self::PRIORITY_LOW,
            self::PRIORITY_MEDIUM,
            self::PRIORITY_HIGH,
        ];
    }

    public static function types(): array
    {
        return [
            self::TYPE_CALL,
            self::TYPE_WHATSAPP,
            self::TYPE_VISIT,
            self::TYPE_OTHER,
        ];
    }

    public static function outcomes(): array
    {
        return [
            self::OUTCOME_INTERESTED,
            self::OUTCOME_NOT_INTERESTED,
            self::OUTCOME_CALLBACK,
            self::OUTCOME_NO_ANSWER,
            self::OUTCOME_BUSY,
            self::OUTCOME_WRONG_NUMBER,
            self::OUTCOME_SUCCESS,
            self::OUTCOME_FAILED,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | STATE CHECKERS
    |--------------------------------------------------------------------------
    */

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_DONE;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isOverdue(): bool
    {
        return $this->due_date
            && !$this->isCompleted()
            && !$this->isCancelled()
            && $this->due_date->isPast();
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_CALL => 'Call',
            self::TYPE_WHATSAPP => 'WhatsApp',
            self::TYPE_VISIT => 'Visit',
            self::TYPE_OTHER => 'Other',
            default => ucfirst((string) $this->type),
        };
    }

    public function getOutcomeLabelAttribute(): ?string
    {
        if (!$this->outcome) return null;

        return match ($this->outcome) {
            self::OUTCOME_INTERESTED => 'Interested',
            self::OUTCOME_NOT_INTERESTED => 'Not Interested',
            self::OUTCOME_CALLBACK => 'Callback',
            self::OUTCOME_NO_ANSWER => 'No Answer',
            self::OUTCOME_BUSY => 'Busy',
            self::OUTCOME_WRONG_NUMBER => 'Wrong Number',
            self::OUTCOME_SUCCESS => 'Success',
            self::OUTCOME_FAILED => 'Failed',
            default => ucfirst((string) $this->outcome),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'Open',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_DONE => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            default => ucfirst((string) $this->status),
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
            default => ucfirst((string) $this->priority),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'blue',
            self::STATUS_IN_PROGRESS => 'yellow',
            self::STATUS_DONE => 'green',
            self::STATUS_CANCELLED => 'red',
            default => 'gray',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION HELPERS (OPTIONAL BUT USEFUL)
    |--------------------------------------------------------------------------
    */

    public static function isValidPriority(string $value): bool
    {
        return in_array($value, self::priorities(), true);
    }

    public static function isValidStatus(string $value): bool
    {
        return in_array($value, [
            self::STATUS_OPEN,
            self::STATUS_IN_PROGRESS,
            self::STATUS_DONE,
            self::STATUS_CANCELLED,
        ], true);
    }
}