<?php

namespace App\User\Filters;

use Illuminate\Database\Eloquent\Builder;

class UserFilter
{
    public function apply(
        Builder $query,
        array $filters
    ): Builder {
        return $query

            /*
            |--------------------------------------------------------------------------
            | SEARCH
            |--------------------------------------------------------------------------
            */
            ->when($filters['search'] ?? null, function ($q, $search) {

                $q->where(function ($q) use ($search) {

                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('branch', function ($branch) use ($search) {
                            $branch->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                });
            })

            /*
            |--------------------------------------------------------------------------
            | GROUP FILTER
            |--------------------------------------------------------------------------
            */
            ->when($filters['group'] ?? null, function ($q, $group) {

                $q->whereHas('groups', function ($groupQuery) use ($group) {
                    $groupQuery->where('group', $group);
                });
            })

            /*
            |--------------------------------------------------------------------------
            | BRANCH FILTER
            |--------------------------------------------------------------------------
            */
            ->when($filters['branch'] ?? null, function ($q, $branchId) {

                $q->where('branch_id', $branchId);
            })

            /*
            |--------------------------------------------------------------------------
            | SORT
            |--------------------------------------------------------------------------
            */
            ->when(
                $filters['sort'] ?? null,
                function ($q, $sort) {

                    match ($sort) {

                        'oldest' => $q->oldest(),

                        'name' => $q->orderBy('name'),

                        'email' => $q->orderBy('email'),

                        'branch' => $q->leftJoin(
                            'branches',
                            'branches.id',
                            '=',
                            'users.branch_id'
                        )
                            ->orderBy('branches.name')
                            ->select('users.*'),

                        default => $q->latest(),
                    };
                },
                fn($q) => $q->latest()
            );
    }
}