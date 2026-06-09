<?php

namespace App\CRM\Filters;

use App\CRM\Models\LeadTask;
use Illuminate\Database\Eloquent\Builder;

class LeadTaskFilter
{
    public function apply(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, fn ($q, $search) =>
                $this->search($q, $search)
            )

            ->when($filters['status'] ?? null, fn ($q, $status) =>
                $q->where('status', $status)
            )

            ->when($filters['type'] ?? null, fn ($q, $type) =>
                $q->where('type', $type)
            )

            ->when($filters['priority'] ?? null, fn ($q, $priority) =>
                $q->where('priority', $priority)
            )

            ->when($filters['assigned_to'] ?? null, fn ($q, $userId) =>
                $q->where('assigned_to', $userId)
            )

            ->when($filters['overdue'] ?? null, fn ($q) =>
                $this->overdue($q)
            );
    }

    protected function search(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('notes', 'like', "%{$search}%");
        });
    }

    protected function overdue(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
                LeadTask::STATUS_DONE,
                LeadTask::STATUS_CANCELLED,
            ])
            ->where('due_date', '<', now());
    }
}