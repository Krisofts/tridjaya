<?php

namespace App\CRM\Models;

use Illuminate\Database\Eloquent\Model;

class CrmResult extends Model
{
    protected $table = 'crm_results';

    protected $fillable = [
        'pipeline_id',
        'name',
        'code',
        'color',
        'is_active',
        'sort_order',
        'is_terminal',
    ];
}