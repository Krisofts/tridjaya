<?php

namespace App\Models;

use App\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = [
        'code',
        'name',
        'address',
        'phone',
        'manager_name',
        'is_active',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(
            User::class,
            'branch_id'
        );
    }
}