<?php

namespace App\User\Services;

use App\User\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProfileService
{
    /**
     * Update profile information.
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {

            $oldEmail = $user->email;

            // update core fields
            $payload = [
                'name'  => $data['name'],
                'email' => $data['email'],
            ];

            $user->fill($payload);

            // reset verification if email changed
            if ($oldEmail !== $data['email']) {
                $user->email_verified_at = null;
            }

            try {
                $user->save();
            } catch (\Throwable $e) {

                throw ValidationException::withMessages([
                    'email' => 'Email sudah digunakan oleh akun lain.',
                ]);
            }

            return $user
                ->refresh()
                ->load('branch:id,name'); // ✅ added branch relation
        });
    }

    /**
     * Delete account safely.
     */
    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
            $user->delete();
        });
    }
}