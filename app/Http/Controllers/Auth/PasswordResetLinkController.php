<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Show forgot password page
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Send reset password link
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return match ($status) {
            Password::RESET_LINK_SENT
                => back()->with('status', __($status)),

            default
                => back()
                    ->withInput($request->only('email'))
                    ->withErrors([
                        'email' => __($status)
                    ]),
        };
    }
}