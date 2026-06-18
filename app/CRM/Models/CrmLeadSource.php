<?php

namespace App\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmLeadSource extends Model
{
    protected $table = 'crm_lead_sources';

    protected $fillable = [
        'name',
        'is_active',
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(CrmLead::class, 'lead_source_id');
    }
}
