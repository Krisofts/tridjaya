<?php

namespace App\CRM\Filters;

use Illuminate\Database\Eloquent\Builder;

class LeadFilter
{
    public function apply(Builder $query, array $filters): Builder
    {
        return $query
            ->when($this->value($filters, 'search'), function (Builder $query, string $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('lead_code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($this->value($filters, 'source_id'), function (Builder $query, int $sourceId) {
                $query->where('lead_source_id', $sourceId);
            })
            ->when($this->value($filters, 'pipeline_id'), function (Builder $query, int $pipelineId) {
                $query->where('pipeline_id', $pipelineId);
            })
            ->when($this->value($filters, 'assigned_to'), function (Builder $query, int $assignedTo) {
                $query->where('assigned_to', $assignedTo);
            })
            ->when($this->value($filters, 'temperature'), function (Builder $query, string $temperature) {
                $query->whereHas('stage', function (Builder $q) use ($temperature) {
                    $q->where('temperature', $temperature);
                });
            });
    }

    // -------------------------------------------------------------------------
    // PRIVATE
    // -------------------------------------------------------------------------

    /**
     * Ambil nilai filter — return null jika kosong/tidak ada,
     * sehingga ->when() tidak akan dieksekusi.
     */
    private function value(array $filters, string $key): mixed
    {
        $value = $filters[$key] ?? null;

        if (is_string($value)) {
            $value = trim($value);
        }

        return $value ?: null;
    }
}