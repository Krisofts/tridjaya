<?php

namespace App\Models;

use App\CRM\Models\CrmLead;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'regency_id',
        'name',
    ];

    public function regency()
    {
        return $this->belongsTo(Regency::class);
    }

    public function leads()
    {
        return $this->hasMany(CrmLead::class);
    }
}