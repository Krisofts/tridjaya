<?php

namespace App\Models;

use App\CRM\Models\CrmLead;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Province extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function regencies()
    {
        return $this->hasMany(Regency::class);
    }

    public function leads()
    {
        return $this->hasMany(CrmLead::class);
    }
}