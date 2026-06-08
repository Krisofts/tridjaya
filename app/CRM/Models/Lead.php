<?php

namespace App\CRM\Models;

use App\User\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Lead extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'address',
        'source',
        'status',
        'interest',
        'notes',
        'assigned_to',
        'created_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | MUTATORS (AUTO CLEAN INPUT)
    |--------------------------------------------------------------------------
    */

    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value
                ? Str::title(
                    preg_replace('/\s+/', ' ', trim($value))
                )
                : null
        );
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            set: function (?string $value) {

                if (blank($value)) {
                    return null;
                }

                // ambil angka saja
                $phone = preg_replace('/\D/', '', $value);

                // normalisasi Indonesia (0 → 62)
                if (str_starts_with($phone, '0')) {
                    $phone = '62' . substr($phone, 1);
                }

                // kalau sudah 62 tapi belum +, tetap simpan tanpa +
                // biar konsisten database
                return $phone;
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function getWhatsappNumberAttribute(): ?string
    {
        if (!$this->phone) {
            return null;
        }

        return $this->phone; // sudah format 62xxxxxxxx
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        if (!$this->phone) {
            return null;
        }

        return 'https://wa.me/' . $this->phone;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function reminders(): HasMany
    {
        return $this->hasMany(LeadReminder::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)
            ->latest();
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
    public function transactions(): HasMany
{
    return $this->hasMany(\App\CRM\Models\LeadTransaction::class)
        ->latest();
}

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* 
    |--------------------------------------------------------------------------
    | OPTIONS (CONFIG DRIVEN)
    |--------------------------------------------------------------------------
    */

    public static function statuses(): array
    {
        return config('crm.lead_status', []);
    }

    public static function sources(): array
    {
        return config('crm.lead_source', []);
    }

    public static function interests(): array
    {
        return config('crm.lead_interest', []);
    }

    public static function defaultStatus(): string
    {
        return array_key_first(self::statuses()) ?? 'new';
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status]
            ?? $this->status;
    }

    public function getSourceLabelAttribute(): ?string
    {
        return self::sources()[$this->source]
            ?? null;
    }

    public function getInterestLabelAttribute(): ?string
    {
        return self::interests()[$this->interest]
            ?? null;
    }
}