<?php

namespace App\Branch\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\User\Models\User;

class Branch extends Model
{
    /*
    |---------------------------------------------------
    | TABLE
    |---------------------------------------------------
    */
    protected $table = 'branches';

    /*
    |---------------------------------------------------
    | MASS ASSIGNMENT
    |---------------------------------------------------
    */
    protected $fillable = [
        'code',
        'name',
        'address',
        'phone',
        'manager_name',
        'is_active',
    ];

    /*
    |---------------------------------------------------
    | CASTS
    |---------------------------------------------------
    */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |---------------------------------------------------
    | RELATION: USERS
    |---------------------------------------------------
    */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'branch_id');
    }

    /*
    |---------------------------------------------------
    | SCOPES
    |---------------------------------------------------
    */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /*
    |---------------------------------------------------
    | HELPERS (UI / DOMAIN LIGHT)
    |---------------------------------------------------
    */

    public function getIsActiveLabelAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    public function hasUsers(): bool
    {
        return $this->users()->exists();
    }
}