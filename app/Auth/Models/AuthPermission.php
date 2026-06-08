<?php

namespace App\Auth\Models;

use App\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuthPermission extends Model
{
    protected $table = 'auth_permissions_users';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'permission',
    ];

    /*
    |---------------------------------------------------
    | RELATION TO USER
    |---------------------------------------------------
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |---------------------------------------------------
    | CHECK PERMISSION
    |---------------------------------------------------
    */
    public function isPermission($permission)
    {
        return $this->permission === $permission;
    }
}