<?php

namespace App\User\Controllers;

use App\Http\Controllers\Controller;
use App\User\Models\User;
use App\User\Services\UserService;
use App\User\Filters\UserFilter;
use App\User\Requests\StoreUserRequest;
use App\User\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | INDEX (FILTER + PAGINATION + FILTER OPTIONS)
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        $users = $this->userService->getAll(
            new UserFilter($request)
        );

        return view('users.index', [
            'users'    => $users,
            'branches' => $this->userService->getBranchOptions(),
            'groups'   => $this->userService->getGroupOptions(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE VIEW
    |--------------------------------------------------------------------------
    */
    public function create(): View
    {
        return view('users.create', [
            'branches' => $this->userService->getBranchOptions(),
            'groups'   => $this->userService->getGroupOptions(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE USER
    |--------------------------------------------------------------------------
    */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->userService->create(
            $request->validated()
        );

        return $this->redirectWithMessage(
            'users.index',
            'User berhasil dibuat'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT VIEW
    |--------------------------------------------------------------------------
    */
    public function edit(User $user): View
    {
        return view('users.edit', [
            'user'     => $user,
            'branches' => $this->userService->getBranchOptions(),
            'groups'   => $this->userService->getGroupOptions(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE USER
    |--------------------------------------------------------------------------
    */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->userService->update(
            $user,
            $request->validated()
        );

        return $this->redirectWithMessage(
            'users.index',
            'User berhasil diperbarui'
        );
    }
    

    /*
    |--------------------------------------------------------------------------
    | DELETE USER
    |--------------------------------------------------------------------------
    */
    public function destroy(User $user): RedirectResponse
    {
        $this->userService->delete($user);

        return $this->redirectWithMessage(
            'users.index',
            'User berhasil dihapus'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | REDIRECT HELPER
    |--------------------------------------------------------------------------
    */
    private function redirectWithMessage(string $route, string $message): RedirectResponse
    {
        return redirect()
            ->route($route)
            ->with('success', $message);
    }
}