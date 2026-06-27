<?php

namespace App\CRM\Models;

use App\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmTransaction extends Model
{
    protected $table = 'crm_transactions';

    protected $fillable = [
        'lead_id',
        'created_by',
        'type',
        'amount',
        'dp_amount',
        'leasing',
        'tenor',
        'status',
        'notes',
        'transaction_date',
        'paid_at',
    ];

    protected $casts = [
        'amount'           => 'integer',
        'dp_amount'        => 'integer',
        'tenor'            => 'integer',
        'transaction_date' => 'date',
        'paid_at'          => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // SCOPES
    // -------------------------------------------------------------------------

    public function scopePending($query)    { return $query->where('status', 'pending'); }
    public function scopePaid($query)       { return $query->where('status', 'paid'); }
    public function scopeCancelled($query)  { return $query->where('status', 'cancelled'); }

    // -------------------------------------------------------------------------
    // ACCESSORS
    // -------------------------------------------------------------------------

    public function getAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getDpAmountFormattedAttribute(): ?string
    {
        return $this->dp_amount
            ? 'Rp ' . number_format($this->dp_amount, 0, ',', '.')
            : null;
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'cash'   => 'Tunai',
            'credit' => 'Kredit',
            default  => $this->type,
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending'   => 'Menunggu',
            'paid'      => 'Lunas',
            'cancelled' => 'Dibatalkan',
            default     => $this->status,
        };
    }

    public function getStatusColor(): string
    {
        return match ($this->status) {
            'paid'      => 'success',
            'cancelled' => 'dark',
            default     => 'warning',
        };
    }

    // -------------------------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------------------------

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class, 'lead_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}