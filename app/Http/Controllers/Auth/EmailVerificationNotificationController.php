<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Auth\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Send email verification notification
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $this->authService->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(
                route('dashboard', absolute: false)
            );
        }

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}