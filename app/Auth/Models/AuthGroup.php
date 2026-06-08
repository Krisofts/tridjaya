<?php

namespace App\Auth\Models;

use App\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuthGroup extends Model
{
    protected $table = 'auth_groups_users';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'group',
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
    | CHECK GROUP NAME
    |---------------------------------------------------
    */
    public function isGroup($name)
    {
        return $this->group === $name;
    }
}