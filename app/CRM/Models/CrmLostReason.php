<?php

namespace App\CRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmLostReason extends Model
{
    use HasFactory;

    protected $table = 'crm_lost_reasons';

    protected $fillable = [
        'pipeline_id',
        'name',
        'slug',
        'sort_order',
        'is_default',
        'is_active',
        'description',
    ];

    protected $casts = [
        'pipeline_id' => 'integer',
        'sort_order'  => 'integer',
        'is_default'  => 'boolean',
        'is_active'   => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function pipeline()
    {
        return $this->belongsTo(CrmPipeline::class);
    }

    public function leads()
    {
        return $this->hasMany(CrmLead::class, 'lost_reason_id');
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

    /**
     * Filter by pipeline — ambil yang spesifik pipeline tsb + yang global (null).
     */
    public function scopeForPipeline($query, ?int $pipelineId)
    {
        return $query->where(function ($q) use ($pipelineId) {
            $q->whereNull('pipeline_id');
            if ($pipelineId) {
                $q->orWhere('pipeline_id', $pipelineId);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isGlobal(): bool
    {
        return is_null($this->pipeline_id);
    }
}