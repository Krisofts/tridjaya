<?php

namespace App\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User\Models\User;

class AuthGroupUser extends Model
{
    protected $table = 'auth_groups_users';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'group',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION: USER
    |--------------------------------------------------------------------------
    */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}