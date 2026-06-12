<?php

namespace App\Auth\Controllers;

use App\Auth\Requests\RegisterRequest;
use App\Auth\Services\AuthService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Show registration form.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle registration request.
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = $this->authService->register(
            $request->validated()
        );

        $this->authService->loginUser($user);

        return redirect()->route('dashboard');
    }
}