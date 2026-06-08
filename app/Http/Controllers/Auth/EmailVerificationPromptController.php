<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Auth\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Show verification prompt
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        $user = $this->authService->user();

        return $user && $user->hasVerifiedEmail()
            ? redirect()->intended(route('dashboard', absolute: false))
            : view('auth.verify-email');
    }
}