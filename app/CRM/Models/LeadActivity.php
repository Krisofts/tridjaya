<?php

namespace App\CRM\Models;

use App\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadActivity extends Model
{
    protected $fillable = [
        'lead_id',
        'title',
        'type',
        'description',
        'created_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION: LEAD
    |--------------------------------------------------------------------------
    */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /*
    |--------------------------------------------------------------------------
    | RELATION: USER (CREATOR)
    |--------------------------------------------------------------------------
    */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}