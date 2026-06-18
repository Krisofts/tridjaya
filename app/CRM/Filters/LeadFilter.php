<?php

namespace App\CRM\Filters;

use Illuminate\Database\Eloquent\Builder;

class LeadFilter
{
    public function apply(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('lead_code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })

            ->when($filters['source_id'] ?? null, function ($query, $sourceId) {
                $query->where('lead_source_id', $sourceId);
            })

            ->when($filters['pipeline_id'] ?? null, function ($query, $pipelineId) {
                $query->where('pipeline_id', $pipelineId);
            })

            ->when($filters['stage_id'] ?? null, function ($query, $stageId) {
                $query->where('pipeline_stage_id', $stageId);
            });
    }
}