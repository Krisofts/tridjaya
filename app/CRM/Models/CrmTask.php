<?php

namespace App\CRM\Models;

use App\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmTask extends Model
{
    use HasFactory;

    protected $table = 'crm_tasks';

    protected $fillable = [
        'lead_id',
        'user_id',
        'created_by',
        'title',
        'description',
        'type',
        'status',
        'priority',
        'due_at',
        'reminder_at',
        'reminder_sent_at',
        'completed_at',
        'result_id',
    ];

    protected $casts = [
        'due_at'           => 'datetime',
        'reminder_at'      => 'datetime',
        'reminder_sent_at' => 'datetime',
        'completed_at'     => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------------------------

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class, 'lead_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function result(): BelongsTo
    {
        return $this->belongsTo(CrmResult::class, 'result_id');
    }

    // -------------------------------------------------------------------------
    // SCOPES
    // -------------------------------------------------------------------------

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('due_at')
            ->where('due_at', '<', now());
    }

    // -------------------------------------------------------------------------
    // ACCESSORS
    // -------------------------------------------------------------------------

    public function getIsOverdueAttribute(): bool
    {
        return in_array($this->status, ['pending', 'in_progress'])
            && $this->due_at
            && $this->due_at->isPast();
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }
}