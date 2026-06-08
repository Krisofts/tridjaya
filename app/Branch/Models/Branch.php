<?php

namespace App\Branch\Models;

use Illuminate\Database\Eloquent\Model;
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
    | CASTING
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
    public function users()
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
}