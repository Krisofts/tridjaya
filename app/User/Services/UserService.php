<?php

namespace App\User\Services;

use App\Auth\Services\AuthorizationService;
use App\Models\Branch;
use App\User\Filters\UserFilter;
use App\User\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function __construct(
        protected AuthorizationService $auth
    ) {}

    /*
    |--------------------------------------------------------------------------
    | LIST
    |--------------------------------------------------------------------------
    */
    public function paginate(
        int $perPage = 5,
        array $filters = []
    ): LengthAwarePaginator {
        return app(UserFilter::class)
            ->apply(
                User::query()->with([
                    'branch',
                    'groups',
                ]),
                $filters
            )
            ->paginate($perPage)
            ->withQueryString();
    }

    /*
    |--------------------------------------------------------------------------
    | FIND
    |--------------------------------------------------------------------------
    */
    public function findOrFail(int|string $id): User
    {
        return User::query()
            ->with([
                'branch',
                'groups',
                'permissions',
            ])
            ->findOrFail($id);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {

            $user = User::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => $data['password'],
                'branch_id' => $data['branch_id'] ?? null,
            ]);

            $this->auth->syncGroups(
                $user,
                $data['groups'] ?? []
            );

            $this->auth->syncPermissions(
                $user,
                $data['permissions'] ?? []
            );

            return $user->fresh([
                'branch',
                'groups',
                'permissions',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {

            $payload = [
                'name'      => $data['name'],
                'email'     => $data['email'],
                'branch_id' => $data['branch_id'] ?? null,
            ];

            if (!empty($data['password'])) {
                $payload['password'] = $data['password'];
            }

            $user->update($payload);

            if (array_key_exists('groups', $data)) {
                $this->auth->syncGroups($user, $data['groups']);
            }

            if (array_key_exists('permissions', $data)) {
                $this->auth->syncPermissions($user, $data['permissions']);
            }

            return $user->fresh([
                'branch',
                'groups',
                'permissions',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function delete(User $user): void
    {
        $user->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | RESTORE
    |--------------------------------------------------------------------------
    */
    public function restore(int|string $id): User
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return $user->fresh();
    }

    /*
    |--------------------------------------------------------------------------
    | FORCE DELETE
    |--------------------------------------------------------------------------
    */
    public function forceDelete(int|string $id): void
    {
        $user = User::withTrashed()->findOrFail($id);

        DB::transaction(function () use ($user) {
            $this->auth->syncGroups($user, []);
            $this->auth->syncPermissions($user, []);
            $user->forceDelete();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER DATA (UI READY)
    |--------------------------------------------------------------------------
    */
    public function getFilterData(): array
{
    return [
        'branches' => Branch::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn (Branch $branch) => [
                'value' => $branch->id,
                'label' => "{$branch->code} - {$branch->name}",
            ])
            ->values()
            ->toArray(),

        'groups' => collect(config('auth_groups.groups', []))
            ->map(fn (array $group, string $key) => [
                'value' => $key,
                'label' => $group['title'],
            ])
            ->values()
            ->toArray(),

        'sortOptions' => [
            [
                'value' => 'oldest',
                'label' => 'Oldest',
            ],
            [
                'value' => 'name',
                'label' => 'Name',
            ],
            [
                'value' => 'email',
                'label' => 'Email',
            ],
        ],
    ];
}

    /*
    |--------------------------------------------------------------------------
    | CREATE FORM DATA (UI READY)
    |--------------------------------------------------------------------------
    */
    public function getCreateData(): array
{
    return [
        'branches' => Branch::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']),

        'availableGroups' => collect(config('auth_groups.groups', []))
            ->keys()
            ->toArray(),

        'availablePermissions' => collect(config('auth_groups.permissions', []))
            ->keys()
            ->toArray(),
    ];
}

    /*
    |--------------------------------------------------------------------------
    | EDIT FORM DATA (UI READY)
    |--------------------------------------------------------------------------
    */
    public function getEditData(User $user): array
{
    return [
        'user' => $user->load('branch'),

        'selectedGroups' => $this->auth->getGroups($user),
        'selectedPermissions' => $this->auth->getPermissions($user),

        'branches' => Branch::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']),

        'availableGroups' => collect(config('auth_groups.groups', []))
            ->keys()
            ->toArray(),

        'availablePermissions' => collect(config('auth_groups.permissions', []))
            ->keys()
            ->toArray(),
    ];
}
}