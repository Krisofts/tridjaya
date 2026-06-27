<?php

namespace App\Ecommerce\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class EcommerceCustomer extends Authenticatable
{
    use SoftDeletes, Notifiable;

    protected $table = 'ecommerce_customers';

    protected $fillable = [
        'name', 'email', 'password', 'phone',
        'address', 'city', 'province', 'postal_code',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(EcommerceOrder::class, 'customer_id')->latest();
    }
}