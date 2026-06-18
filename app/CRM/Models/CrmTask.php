<?php

namespace App\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmTask extends Model
{
    protected $table = 'crm_tasks';

    protected $fillable = [
        'lead_id',
        'user_id',
        'title',
        'description',
        'status',
        'priority',
        'due_at',
        'completed_at',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /*
    |---------------------------------------------
    | RELATIONS
    |---------------------------------------------
    */

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class, 'lead_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User\Models\User::class, 'user_id');
    }

    /*
    |---------------------------------------------
    | SCOPES
    |---------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            'pending',
            'in_progress',
        ]);
    }

    public function scopeOverdue($query)
    {
        return $query
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('due_at')
            ->where('due_at', '<', now());
    }

    public function scopeToday($query)
    {
        return $query->whereDate('due_at', now());
    }

    public function scopeUpcoming($query)
    {
        return $query
            ->where('status', 'pending')
            ->whereNotNull('due_at')
            ->where('due_at', '>', now());
    }

    /*
    |---------------------------------------------
    | HELPERS
    |---------------------------------------------
    */

    public function isOverdue(): bool
    {
        return $this->status !== 'completed'
            && $this->due_at
            && $this->due_at->isPast();
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }
}