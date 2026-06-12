<?php

namespace App\User\Controllers;

use App\Http\Controllers\Controller;
use App\User\Requests\UpdateProfileRequest;
use App\User\Services\ProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        protected ProfileService $profileService
    ) {}

    /** 
     * Show profile page.
     */
    public function edit(): View
    {
        return view('pages.profile');
    }

    /**
     * Update profile.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $this->profileService->update(
            $request->user(),
            $request->validated()
        );

        return back()->with(
            'success',
            'Profile updated successfully.'
        );
    }
}