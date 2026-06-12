<?php

namespace App\User\Services;

use App\User\Models\User;
use App\User\Filters\UserFilter;
use App\Auth\Services\AuthorizationService;
use App\Branch\Services\BranchService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        protected AuthorizationService $authz,
        protected BranchService $branchService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | GET USERS (FILTER + PAGINATION)
    |--------------------------------------------------------------------------
    */
    public function getAll(UserFilter $filter): LengthAwarePaginator
    {
        return $filter->apply(
                User::query()->with([
                    'branch:id,name',
                    'groups:user_id,group'
                ])
            )
            ->latest('id')
            ->paginate(10)
            ->withQueryString();
    }

    /*
    |--------------------------------------------------------------------------
    | GET BRANCH OPTIONS (FOR FORM)
    |--------------------------------------------------------------------------
    */
    public function getBranchOptions(): array
    {
        return $this->branchService->getOptions();
    }

    /*
    |--------------------------------------------------------------------------
    | GET GROUP OPTIONS (FROM CONFIG)
    |--------------------------------------------------------------------------
    */
    public function getGroupOptions(): array
    {
        return cache()->remember('auth.groups.options', 3600, function () {

            $groups = config('auth_groups.groups');

            $options = [];

            foreach ($groups as $key => $group) {
                $options[$key] = $group['title'];
            }

            return $options;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE USER
    |--------------------------------------------------------------------------
    */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {

            $user = User::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'branch_id' => $data['branch_id'] ?? null,
                'password'  => Hash::make($data['password']),
            ]);

            $this->syncGroups($user, $data['groups'] ?? []);

            return $user;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE USER
    |--------------------------------------------------------------------------
    */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {

            $payload = $this->buildUpdatePayload($data);

            if (!empty($payload)) {
                $user->update($payload);
            }

            if (array_key_exists('groups', $data)) {
                $this->syncGroups($user, $data['groups'] ?? []);
            }

            return $user->refresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE USER
    |--------------------------------------------------------------------------
    */
    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {

            $user->groups()->delete();
            $user->delete();

            $this->authz->clearCache($user);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ASSIGN GROUPS (SHORTCUT)
    |--------------------------------------------------------------------------
    */
    public function assignGroups(User $user, array $groups): void
    {
        $this->syncGroups($user, $groups);
    }

    /*
    |--------------------------------------------------------------------------
    | BUILD UPDATE PAYLOAD (SAFE FIELD CONTROL)
    |--------------------------------------------------------------------------
    */
    private function buildUpdatePayload(array $data): array
    {
        $payload = [];

        foreach (['name', 'email', 'branch_id'] as $field) {
            if (array_key_exists($field, $data)) {
                $payload[$field] = $data[$field];
            }
        }

        if (!empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        return $payload;
    }

    /*
    |--------------------------------------------------------------------------
    | GROUP SYNC STRATEGY
    |--------------------------------------------------------------------------
    */
    private function syncGroups(User $user, array $groups): void
    {
        if (!empty($groups)) {
            $this->authz->syncGroups($user, $groups);
            return;
        }

        $this->authz->assignGroup(
            $user,
            config('auth_groups.defaultGroup', 'user')
        );
    }
}