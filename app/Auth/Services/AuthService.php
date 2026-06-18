<?php

namespace App\Auth\Services;

use App\User\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Attempt login using credentials.
     *
     * @throws ValidationException
     */
    public function attempt(
        string $email,
        string $password,
        string $ip,
        bool $remember = false
    ): bool {

        $throttleKey = Str::transliterate(
            Str::lower($email).'|'.$ip
        );

        if (RateLimiter::tooManyAttempts(
            $throttleKey,
            5
        )) {

            event(new Lockout(request()));

            $seconds = RateLimiter::availableIn(
                $throttleKey
            );

            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        if (! Auth::attempt([
            'email' => $email,
            'password' => $password,
        ], $remember)) {

            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($throttleKey);

        return true;
    }

    /**
     * Register new user.
     */
    public function register(array $data): User
    {
        $user = DB::transaction(function () use ($data) {
            return User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
        });

        event(new Registered($user));

        return $user;
    }

    /**
     * Login a specific user instance.
     */
    public function login(
        User $user,
        bool $remember = false
    ): void {
        Auth::login($user, $remember);
    }

    /**
     * Logout current user.
     */
    public function logout(): void
    {
        Auth::logout();
    }
}