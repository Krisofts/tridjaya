<?php

namespace App\User\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserFilter
{
    public function __construct(
        protected Request $request
    ) {}

    public function apply(Builder $query): Builder
    {
        return $query
            ->when(
                $this->request->filled('search'),
                fn (Builder $query) => $this->search(
                    $query,
                    $this->request->string('search')->value()
                )
            )
            ->when(
                $this->request->filled('group'),
                fn (Builder $query) => $this->group(
                    $query,
                    $this->request->string('group')->value()
                )
            )
            ->when(
                $this->request->filled('sort'),
                fn (Builder $query) => $this->sort(
                    $query,
                    $this->request->string('sort')->value()
                )
            );
    }

    /*
    |---------------------------------------------------
    | SEARCH
    |---------------------------------------------------
    */
    protected function search(
        Builder $query,
        string $keyword
    ): Builder {
        return $query->where(
            fn (Builder $query) => $query
                ->where('name', 'like', "%{$keyword}%")
                ->orWhere('email', 'like', "%{$keyword}%")
        );
    }

    /*
    |---------------------------------------------------
    | FILTER GROUP
    |---------------------------------------------------
    */
    protected function group(
        Builder $query,
        string $group
    ): Builder {
        return $query->whereHas(
            'groups',
            fn (Builder $query) => $query->where(
                'group',
                strtolower(trim($group))
            )
        );
    }

    /*
    |---------------------------------------------------
    | SORTING
    |---------------------------------------------------
    */
    protected function sort(
        Builder $query,
        string $sort
    ): Builder {
        return match ($sort) {
            'name_asc'  => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),

            'email_asc'  => $query->orderBy('email'),
            'email_desc' => $query->orderByDesc('email'),

            'oldest' => $query->oldest(),

            default => $query->latest(),
        };
    }
}