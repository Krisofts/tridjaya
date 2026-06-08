<?php

namespace App\CRM\Models;

use App\CRM\Models\Lead;
use App\CRM\Models\LeadTransaction;
use App\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Customer extends Model
{
    protected $table = 'customers';

    protected $fillable = [
        'lead_id',
        'name',
        'phone',
        'address',
        'type', // active | vip | inactive
        'converted_at',
        'converted_by',
        'created_by',
    ];

    protected $casts = [
        'converted_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION: LEAD (SOURCE ORIGIN)
    |--------------------------------------------------------------------------
    */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /*
    |--------------------------------------------------------------------------
    | RELATION: TRANSACTIONS (BASED ON LEAD ID - SAFE LEGACY LINK)
    |--------------------------------------------------------------------------
    | NOTE: masih pakai lead_id agar kompatibel dengan CRM flow sekarang
    */
    public function transactions(): HasMany
    {
        return $this->hasMany(
            LeadTransaction::class,
            'lead_id',
            'lead_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELATION: CONVERTED BY (USER WHO CONVERT LEAD → CUSTOMER)
    |--------------------------------------------------------------------------
    */
    public function convertedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'converted_by');
    }

    /*
    |--------------------------------------------------------------------------
    | RELATION: CREATED BY
    |--------------------------------------------------------------------------
    */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getNameAttribute($value): string
    {
        return Str::title($value);
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS HELPERS
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->type === 'active';
    }

    public function isVip(): bool
    {
        return $this->type === 'vip';
    }

    public function isInactive(): bool
    {
        return $this->type === 'inactive';
    }

    /*
    |--------------------------------------------------------------------------
    | BUSINESS LOGIC HELPERS
    |--------------------------------------------------------------------------
    */

    public function fullName(): string
    {
        return Str::title($this->name);
    }

    public function totalTransactionAmount(): float
    {
        return (float) $this->transactions->sum('amount');
    }

    public function totalCashRevenue(): float
    {
        return (float) $this->transactions
            ->where('type', 'cash')
            ->sum('amount');
    }

    public function totalCreditRevenue(): float
    {
        return (float) $this->transactions
            ->where('type', 'credit')
            ->sum('amount');
    }

    public function hasLead(): bool
    {
        return !is_null($this->lead_id);
    }

    public function isConverted(): bool
    {
        return !is_null($this->converted_at);
    }

    /*
    |--------------------------------------------------------------------------
    | FUTURE SAFE HOOK (READY FOR EVENT SYSTEM)
    |--------------------------------------------------------------------------
    | nanti bisa dipakai untuk observer / event
    */
    public function markAsConverted(int $userId): void
    {
        $this->update([
            'converted_at' => now(),
            'converted_by' => $userId,
            'type' => 'active',
        ]);
    }
}