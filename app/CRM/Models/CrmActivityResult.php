<?php

namespace App\CRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmActivityResult extends Model
{
    use HasFactory;

    protected $table = 'crm_activity_results';

    protected $fillable = [
        'activity_type_id',
        'name',
        'slug',
        'sort_order',
        'is_default',
        'is_success',
        'is_active',
        'description',
    ];

    protected $casts = [
        'activity_type_id' => 'integer',
        'sort_order'       => 'integer',
        'is_default'       => 'boolean',
        'is_success'       => 'boolean',
        'is_active'        => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function activityType()
    {
        return $this->belongsTo(CrmActivityType::class, 'activity_type_id');
    }

    public function activities()
    {
        return $this->hasMany(CrmLeadActivity::class, 'activity_result_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, int $activityTypeId)
    {
        return $query->where('activity_type_id', $activityTypeId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeSuccess($query)
    {
        return $query->where('is_success', true);
    }
}