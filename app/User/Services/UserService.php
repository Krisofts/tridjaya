<?php

namespace App\User\Services;

use App\User\Models\User;
use App\User\Filters\UserFilter;
use App\Auth\Services\AuthGroupService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        protected AuthGroupService $authGroupService
    ) {}

    /*
    |---------------------------------------------------
    | BASE QUERY
    |---------------------------------------------------
    */
    public function query(): Builder
    {
        return User::query()
            ->with([
                'groups',
                'permissions',
                'branch', // 👈 ADD
            ]);
    }

    /*
    |---------------------------------------------------
    | FIND USER BY ID
    |---------------------------------------------------
    */
    public function findById(int $id): ?User
    {
        return $this->query()->find($id);
    }

    /*
    |---------------------------------------------------
    | FIND USER BY EMAIL
    |---------------------------------------------------
    */
    public function findByEmail(string $email): ?User
    {
        return $this->query()
            ->where('email', $email)
            ->first();
    }

    /*
    |---------------------------------------------------
    | CREATE USER
    |---------------------------------------------------
    */
    public function create(array $data): User
    {
        $user = User::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),

            // 👇 BRANCH SUPPORT
            'branch_id'  => $data['branch_id'] ?? null,

            'force_password_change' => true,
            'password_changed_at'   => null,
        ]);

        $this->authGroupService
            ->addToDefaultGroup($user->id);

        return $user->fresh([
            'groups',
            'permissions',
            'branch', // 👈 ADD
        ]);
    }

    /*
    |---------------------------------------------------
    | UPDATE USER
    |---------------------------------------------------
    */
    public function update(int $id, array $data): User
    {
        $user = User::findOrFail($id);

        $user->fill([
            'name'       => $data['name'] ?? $user->name,
            'email'      => $data['email'] ?? $user->email,

            // 👇 BRANCH SUPPORT
            'branch_id'  => $data['branch_id'] ?? $user->branch_id,
        ]);

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
            $user->force_password_change = false;
            $user->password_changed_at = now();
        }

        $user->save();

        return $user->fresh([
            'groups',
            'permissions',
            'branch', // 👈 ADD
        ]);
    }

    /*
    |---------------------------------------------------
    | DELETE USER
    |---------------------------------------------------
    */
    public function delete(int $id, bool $force = false): bool
    {
        $user = User::withTrashed()->findOrFail($id);

        return $force
            ? (bool) $user->forceDelete()
            : (bool) $user->delete();
    }

    /*
    |---------------------------------------------------
    | RESTORE USER
    |---------------------------------------------------
    */
    public function restore(int $id): User
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return $user;
    }

    /*
    |---------------------------------------------------
    | PAGINATION
    |---------------------------------------------------
    */
    public function paginate(UserFilter $filter, int $perPage = 15): LengthAwarePaginator
    {
        return $filter
            ->apply($this->query())
            ->paginate($perPage)
            ->withQueryString();
    }

    /*
    |---------------------------------------------------
    | ALL USERS
    |---------------------------------------------------
    */
    public function all()
    {
        return $this->query()->latest()->get();
    }

    /*
    |---------------------------------------------------
    | TRASHED USERS
    |---------------------------------------------------
    */
    public function trashed(int $perPage = 15): LengthAwarePaginator
    {
        return User::onlyTrashed()
            ->with(['groups', 'permissions', 'branch'])
            ->latest()
            ->paginate($perPage);
    }

    /*
    |---------------------------------------------------
    | COUNT
    |---------------------------------------------------
    */
    public function count(): int
    {
        return User::count();
    }

    /*
    |---------------------------------------------------
    | EMAIL CHECK
    |---------------------------------------------------
    */
    public function existsByEmail(string $email, ?int $exceptUserId = null): bool
    {
        return User::query()
            ->when(
                $exceptUserId,
                fn ($query) => $query->where('id', '!=', $exceptUserId)
            )
            ->where('email', $email)
            ->exists();
    }

    /*
    |---------------------------------------------------
    | CHANGE PASSWORD
    |---------------------------------------------------
    */
    public function changePassword(int $userId, string $password): User
    {
        $user = User::findOrFail($userId);

        $user->update([
            'password'              => Hash::make($password),
            'force_password_change' => false,
            'password_changed_at'   => now(),
        ]);

        return $user;
    }

    /*
    |---------------------------------------------------
    | FORCE RESET PASSWORD
    |---------------------------------------------------
    */
    public function forcePasswordReset(int $userId): User
    {
        $user = User::findOrFail($userId);

        $user->update([
            'force_password_change' => true,
        ]);

        return $user;
    }

    /*
    |---------------------------------------------------
    | USERS BY BRANCH (OPTIONAL BUT USEFUL)
    |---------------------------------------------------
    */
    public function byBranch(int $branchId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()
            ->where('branch_id', $branchId)
            ->paginate($perPage);
    }

    /*
    |---------------------------------------------------
    | CURRENT BRANCH USERS
    |---------------------------------------------------
    */
    public function currentBranchUsers(int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()
            ->where('branch_id', auth()->user()->branch_id)
            ->paginate($perPage);
    }
}