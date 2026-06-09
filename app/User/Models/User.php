<?php

namespace App\User\Models;

use App\Auth\Traits\Authorizable;
use App\Auth\Models\AuthGroup;
use App\Auth\Models\AuthPermission;
use App\Branch\Models\Branch;
use App\CRM\Models\Lead;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Authorizable, SoftDeletes;

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
        'force_password_change',
        'password_changed_at',
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
    | CASTS
    |--------------------------------------------------------------------------
    */
    protected function casts(): array
    {
        return [
            'email_verified_at'     => 'datetime',
            'password'              => 'hashed',
            'password_changed_at'   => 'datetime',
            'force_password_change' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

     
    public function group(): HasOne
    {
        return $this->hasOne(AuthGroup::class, 'user_id');
    }

   
    public function groups(): HasMany
    {
        return $this->hasMany(AuthGroup::class, 'user_id');
    }




    public function permissions()
    {
        return $this->hasMany(AuthPermission::class, 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'created_by');
    }

    public function wonLeads()
    {
        return $this->leads()->where('status', 'won');
    }

    public function todayLeads()
    {
        return $this->leads()->whereDate('created_at', today());
    }

    /*
    |--------------------------------------------------------------------------
    | PASSWORD HELPERS
    |--------------------------------------------------------------------------
    */
    public function mustChangePassword(): bool
    {
        return (bool) $this->force_password_change
            && is_null($this->password_changed_at);
    }

    public function markPasswordAsChanged(): void
    {
        $this->force_password_change = false;
        $this->password_changed_at = now();
        $this->save();
    }

    /*
    |--------------------------------------------------------------------------
    | GROUP SCOPES (SINGLE ONLY)
    |--------------------------------------------------------------------------
    */

    public function scopeInGroup(Builder $query, string $group): Builder
    {
        return $query->whereHas('group', function ($q) use ($group) {
            $q->whereRaw('LOWER(`group`) = ?', [strtolower($group)]);
        });
    }
}