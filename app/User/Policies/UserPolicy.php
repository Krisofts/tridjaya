<?php

namespace App\User\Policies;

use App\User\Models\User;

class UserPolicy
{
    /*
    |---------------------------------------------------
    | BASE PERMISSION
    |---------------------------------------------------
    */
    public function viewAny(User $authUser): bool
    {
        return $authUser->can('users.view');
    }

    public function view(User $authUser, User $targetUser): bool
    {
        return $authUser->can('users.view');
    }

    public function create(User $authUser): bool
    {
        return $authUser->can('users.create');
    }

    /*
    |---------------------------------------------------
    | UPDATE
    |---------------------------------------------------
    */
    public function update(User $authUser, User $targetUser): bool
    {
        if ($this->isProtectedSuperadmin($authUser, $targetUser)) {
            return false;
        }

        return $authUser->can('users.edit');
    }

    /*
    |---------------------------------------------------
    | DELETE
    |---------------------------------------------------
    */
    public function delete(User $authUser, User $targetUser): bool
    {
        if ($this->isProtectedSuperadmin($authUser, $targetUser)) {
            return false;
        }

        return $authUser->can('users.delete');
    }

    /*
    |---------------------------------------------------
    | HELPER METHODS
    |---------------------------------------------------
    */
    private function isSuperadmin(User $user): bool
    {
        return $user->groups()
            ->where('group', 'superadmin')
            ->exists();
    }

    /**
     * Rule:
     * - Superadmin hanya boleh diubah/dihapus oleh superadmin juga
     */
    private function isProtectedSuperadmin(User $authUser, User $targetUser): bool
    {
        return $this->isSuperadmin($targetUser)
            && !$this->isSuperadmin($authUser);
    }
}