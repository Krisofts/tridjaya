<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PasswordController extends Controller
{
    /*
    |---------------------------------------------------
    | SHOW FORM
    |---------------------------------------------------
    */
    public function edit(): View
    {
        return view('auth.change-password');
    }

    /*
    |---------------------------------------------------
    | UPDATE PASSWORD
    |---------------------------------------------------
    */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $request->user();

        // 🔥 gunakan method model (single source of truth)
        $user->password = $request->password;

        $user->markPasswordAsChanged();

        // refresh session biar aman setelah change password
        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Password berhasil diperbarui');
    }
}