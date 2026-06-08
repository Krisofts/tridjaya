<?php

namespace App\CRM\Services;

use App\CRM\Models\Lead;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;

class LeadService
{
    /*
    |--------------------------------------------------------------------------
    | BASE QUERY
    |--------------------------------------------------------------------------
    */

    private function baseQuery()
    {
        return Lead::query()
            ->with([
                'assignedTo',
                'createdBy',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LIST WITH FILTERS
    |--------------------------------------------------------------------------
    */

    public function paginateWithFilters(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {
        return $this->baseQuery()
            ->when(
                $filters['search'] ?? null,
                $this->search(...)
            )
            ->when(
                $filters['status'] ?? null,
                fn ($q, $value) => $q->where('status', $value)
            )
            ->when(
                $filters['interest'] ?? null,
                fn ($q, $value) => $q->where('interest', $value)
            )
            ->when(
                $filters['source'] ?? null,
                fn ($q, $value) => $q->where('source', $value)
            )
            ->when(
                $filters['assigned_to'] ?? null,
                fn ($q, $value) => $q->where('assigned_to', $value)
            )
            ->when(
                $filters['sort'] ?? null,
                $this->sort(...)
            )
            ->when(
                empty($filters['sort']),
                fn ($q) => $q->latest()
            )
            ->paginate($perPage)
            ->withQueryString();
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    private function search($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    /*
    |--------------------------------------------------------------------------
    | SORT
    |--------------------------------------------------------------------------
    */

    private function sort($query, string $sort)
    {
        return match ($sort) {
            'oldest' => $query->oldest(),
            'name'   => $query->orderBy('name'),
            default  => $query->latest(),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | GET ALL
    |--------------------------------------------------------------------------
    */

    public function all(): Collection
    {
        return $this->baseQuery()
            ->latest()
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | FIND
    |--------------------------------------------------------------------------
    */

    public function find(int $id): Lead
    {
        return $this->baseQuery()
            ->with([
                'activities.createdBy',
                'reminders',
            ])
            ->findOrFail($id);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create(array $data): Lead
    {
        $this->validateEnum($data);

        $data['status'] ??= Lead::defaultStatus();

        return Lead::create($data);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Lead $lead,
        array $data
    ): Lead {
        $this->validateEnum($data);

        $lead->update($data);

        return $lead->fresh();
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function delete(Lead $lead): bool
    {
        return (bool) $lead->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | ASSIGNMENT
    |--------------------------------------------------------------------------
    */

    public function assign(
        Lead $lead,
        int $userId
    ): Lead {
        if ($lead->assigned_to === $userId) {
            return $lead;
        }

        $lead->update([
            'assigned_to' => $userId,
        ]);

        return $lead->fresh();
    }

    public function unassign(Lead $lead): Lead
    {
        if (is_null($lead->assigned_to)) {
            return $lead;
        }

        $lead->update([
            'assigned_to' => null,
        ]);

        return $lead->fresh();
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION ENUM
    |--------------------------------------------------------------------------
    */

    private function validateEnum(array $data): void
    {
        validator($data, [
            'status' => [
                'nullable',
                Rule::in(array_keys(Lead::statuses())),
            ],

            'source' => [
                'nullable',
                Rule::in(array_keys(Lead::sources())),
            ],

            'interest' => [
                'nullable',
                Rule::in(array_keys(Lead::interests())),
            ],
        ])->validate();
    }
}