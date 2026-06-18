<?php

namespace App\User\Controllers;

use App\User\Models\User;
use App\User\Requests\StoreUserRequest;
use App\User\Requests\UpdateUserRequest;
use App\User\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController
{
    public function __construct(
        protected UserService $userService,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | LIST
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        $filters = $request->only([
            'search',
            'group',
            'branch',
            'sort',
        ]);

        return view('pages.users.index', [
            'users' => $this->userService->paginate(
                perPage: 15,
                filters: $filters
            ),

            ...$this->userService->getFilterData(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */
    public function show(User $user): View
    {
        $data = $this->userService->getEditData($user);

        return view('pages.users.show', [
            'user' => $data['user'],
            'groups' => $data['selectedGroups'],
            'permissions' => $data['selectedPermissions'],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE FORM
    |--------------------------------------------------------------------------
    */
    public function create(): View
    {
        return view(
            'pages.users.create',
            $this->userService->getCreateData()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->userService->create(
            $request->validated()
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT FORM
    |--------------------------------------------------------------------------
    */
    public function edit(User $user): View
    {
        return view(
            'pages.users.edit',
            $this->userService->getEditData($user)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(
        UpdateUserRequest $request,
        User $user
    ): RedirectResponse {
        $this->userService->update(
            $user,
            $request->validated()
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diupdate.');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy(User $user): RedirectResponse
    {
        $this->userService->delete($user);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | RESTORE
    |--------------------------------------------------------------------------
    */
    public function restore(int|string $id): RedirectResponse
    {
        $this->userService->restore($id);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil direstore.');
    }

    /*
    |--------------------------------------------------------------------------
    | FORCE DELETE
    |--------------------------------------------------------------------------
    */
    public function forceDelete(int|string $id): RedirectResponse
    {
        $this->userService->forceDelete($id);

        return redirect()
            ->route('users.index')
            ->with('success', 'User dihapus permanen.');
    }
}