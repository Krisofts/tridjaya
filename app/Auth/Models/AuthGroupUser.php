<?php

namespace App\Auth\Models;

use App\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthGroupUser extends Model
{
    protected $table = 'auth_groups_users';

    protected $fillable = [
        'user_id',
        'group',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}