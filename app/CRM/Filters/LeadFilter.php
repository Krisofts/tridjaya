<?php

namespace App\CRM\Filters;

use Illuminate\Database\Eloquent\Builder;

class LeadFilter
{
    public function apply(Builder $query, array $filters): Builder
    {
        return $query

            // SEARCH
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('lead_code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })

            // SOURCE FILTER
            ->when($filters['source_id'] ?? null, function ($query, $sourceId) {
                $query->where('lead_source_id', $sourceId);
            })

            // PIPELINE FILTER
            ->when($filters['pipeline_id'] ?? null, function ($query, $pipelineId) {
                $query->where('pipeline_id', $pipelineId);
            })

            // 👤 ASSIGNEE FILTER (NEW)
            ->when($filters['assigned_to'] ?? null, function ($query, $assignedTo) {
                $query->where('assigned_to', $assignedTo);
            })

            // 🔥 TEMPERATURE FILTER
            ->when($filters['temperature'] ?? null, function ($query, $temperature) {
                $query->whereHas('stage', function ($q) use ($temperature) {
                    $q->where('temperature', $temperature);
                });
            });
    }
}