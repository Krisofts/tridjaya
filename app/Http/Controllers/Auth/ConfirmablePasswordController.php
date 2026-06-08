<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Auth\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConfirmablePasswordController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Show confirm password view
     */
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    /**
     * Confirm password
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $this->authService->user();

        if (! $this->authService->validatePassword(
            $user,
            $request->password
        )) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put(
            'auth.password_confirmed_at',
            time()
        );

        return redirect()->intended(
            route('dashboard', absolute: false)
        );
    }
}