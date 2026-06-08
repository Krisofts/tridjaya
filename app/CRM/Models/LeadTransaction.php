<?php

namespace App\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\CRM\Models\Lead;
use App\User\Models\User;

class LeadTransaction extends Model
{
    protected $table = 'lead_transactions';

    protected $fillable = [
        'lead_id',
        'type',
        'amount',
        'down_payment',
        'tenor_months',
        'monthly_payment',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'float',
        'down_payment' => 'float',
        'monthly_payment' => 'float',
        'tenor_months' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | CONFIG LABELS (NO HARDCODE)
    |--------------------------------------------------------------------------
    */

    public function typeLabel(): string
    {
        return config("crm.transaction_type.{$this->type}")
            ?? ucfirst(str_replace('_', ' ', $this->type));
    }

    public function statusLabel(): string
    {
        return config("crm.transaction_status.{$this->status}")
            ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    /*
    |--------------------------------------------------------------------------
    | BUSINESS HELPERS
    |--------------------------------------------------------------------------
    */

    public function isCredit(): bool
    {
        return $this->type === 'credit';
    }

    public function isCash(): bool
    {
        return $this->type === 'cash';
    }

    public function isInstallment(): bool
    {
        return $this->type === 'installment';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /*
    |--------------------------------------------------------------------------
    | FINANCIAL CALCULATION
    |--------------------------------------------------------------------------
    */

    public function remainingBalance(): float
    {
        return max(0, $this->amount - ($this->down_payment ?? 0));
    }

    public function isFullyPaid(): bool
    {
        return $this->remainingBalance() <= 0;
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT HELPERS (OPTIONAL UI)
    |--------------------------------------------------------------------------
    */

    public function formattedAmount(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function formattedDownPayment(): string
    {
        return 'Rp ' . number_format($this->down_payment ?? 0, 0, ',', '.');
    }

    public function formattedMonthlyPayment(): string
    {
        return 'Rp ' . number_format($this->monthly_payment ?? 0, 0, ',', '.');
    }
}