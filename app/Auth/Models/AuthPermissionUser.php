<?php

namespace App\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User\Models\User;

class AuthPermissionUser extends Model
{
    protected $table = 'auth_permissions_users';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'permission',
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