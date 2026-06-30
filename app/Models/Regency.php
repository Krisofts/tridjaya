<?php

namespace App\Models;

use App\CRM\Models\CrmLead;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Regency extends Model
{
    use HasFactory;

    protected $fillable = [
        'province_id',
        'name',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function districts()
    {
        return $this->hasMany(District::class);
    }

    public function leads()
    {
        return $this->hasMany(CrmLead::class);
    }
}