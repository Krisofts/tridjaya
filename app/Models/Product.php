<?php

namespace App\Models;

use App\CRM\Models\CrmLead;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function leads()
    {
        return $this->hasMany(CrmLead::class);
    }
}