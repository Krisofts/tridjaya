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
    | MUTATORS
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

                $phone = preg_replace('/\D/', '', $value);

                if (str_starts_with($phone, '0')) {
                    $phone = '62' . substr($phone, 1);
                }

                return $phone;
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)
            ->latest();
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(LeadReminder::class)
            ->latest();
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(LeadTransaction::class)
            ->latest();
    }

    /**
     * CRM Tasks
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(LeadTask::class)
            ->latest();
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function getWhatsappNumberAttribute(): ?string
    {
        return $this->phone;
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        return $this->phone
            ? "https://wa.me/{$this->phone}"
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | OPTIONS
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