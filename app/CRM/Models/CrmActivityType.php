<?php

namespace App\CRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmActivityType extends Model
{
    use HasFactory;

    protected $table = 'crm_activity_types';

    protected $fillable = [
        'name',
        'slug',
        'sort_order',
        'is_default',
        'is_active',
        'description',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function results()
    {
        return $this->hasMany(CrmActivityResult::class, 'activity_type_id')
            ->orderBy('sort_order');
    }

    public function activities()
    {
        return $this->hasMany(CrmLeadActivity::class, 'activity_type_id');
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

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function defaultResult(): ?CrmActivityResult
    {
        return $this->results()->where('is_default', true)->where('is_active', true)->first();
    }
}