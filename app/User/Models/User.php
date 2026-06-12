<?php

namespace App\User\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNMENT
    |-------------------------------------------------------------------------- 
    */
    protected $fillable = [
        'name',
        'email',
        'password',
        'branch_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | HIDDEN
    |--------------------------------------------------------------------------
    */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | APPENDS (UI HELPERS)
    |--------------------------------------------------------------------------
    */
    protected $appends = [
        'initials',
        'branch_name',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', $this->name))
            ->filter()
            ->take(2)
            ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
            ->implode('');
    }

    public function getBranchNameAttribute(): ?string
    {
        return $this->branch?->name;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS - RBAC
    |--------------------------------------------------------------------------
    */

    public function groups(): HasMany
    {
        return $this->hasMany(
            \App\Auth\Models\AuthGroupUser::class,
            'user_id'
        );
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(
            \App\Auth\Models\AuthPermissionUser::class,
            'user_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELATION - BRANCH
    |--------------------------------------------------------------------------
    */

    public function branch(): BelongsTo
    {
        return $this->belongsTo(
            \App\Branch\Models\Branch::class,
            'branch_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isInBranch(int $branchId): bool
    {
        return (int) $this->branch_id === $branchId;
    }

    public function hasBranch(): bool
    {
        return !is_null($this->branch_id);
    }

    public function hasGroup(string $group): bool
    {
        return $this->relationLoaded('groups')
            ? $this->groups->contains('group', $group)
            : $this->groups()->where('group', $group)->exists();
    }
}