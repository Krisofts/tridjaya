<?php

namespace App\Policies;

use App\User\Models\User;
use App\Auth\Services\AuthService;

class UserPolicy
{
    public function __construct(
        protected AuthService $auth
    ) {}

    public function viewAny(User $authUser): bool
    {
        return $this->auth->canAccess('user.view');
    }

    public function view(User $authUser, User $user): bool
    {
        return $this->auth->canAccess('user.view');
    }

    public function create(User $authUser): bool
    {
        return $this->auth->canAccess('user.create');
    }

    public function update(User $authUser, User $user): bool
    {
        if ($authUser->id === $user->id) {
            return true;
        }

        return $this->auth->canAccess('user.update');
    }

    public function delete(User $authUser, User $user): bool
    {
        if ($authUser->id === $user->id) {
            return false;
        }

        return $this->auth->canAccess('user.delete');
    }

    public function restore(User $authUser, User $user): bool
    {
        return $this->auth->canAccess('user.restore');
    }

    public function forceDelete(User $authUser, User $user): bool
    {
        return $this->auth->canAccess('user.force_delete');
    }
}