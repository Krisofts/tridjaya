<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\User\Models\User;
use App\User\Filters\UserFilter;
use App\User\Services\UserService;
use App\Auth\Services\AuthGroupService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected UserService $userService,
        protected AuthGroupService $authGroupService,
    ) {}

    /*
    |---------------------------------------------------
    | LIST USER
    |---------------------------------------------------
    */
    public function index(UserFilter $filter): View
    {
        $this->authorize('viewAny', User::class);

        $users = $this->userService->paginate($filter, 20);

        return view('user.index', compact('users'));
    }

    /*
    |---------------------------------------------------
    | CREATE FORM
    |---------------------------------------------------
    */
    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('user.create', [
            'branches' => app(\App\Branch\Models\Branch::class)
                ->where('is_active', true)
                ->get(),
        ]);
    }

    /*
    |---------------------------------------------------
    | STORE USER
    |---------------------------------------------------
    */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $this->userService->create(
            $request->validated()
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /*
    |---------------------------------------------------
    | SHOW USER
    |---------------------------------------------------
    */
    public function show(User $user): View
    {
        $this->authorize('view', $user);

        return view('user.show', compact('user'));
    }

    /*
    |---------------------------------------------------
    | EDIT FORM
    |---------------------------------------------------
    */
    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('user.edit', [
            'user' => $user,
            'groups' => $this->authGroupService->getAvailableGroups(),
            'branches' => app(\App\Branch\Models\Branch::class)
                ->where('is_active', true)
                ->get(),
        ]);
    }

    /*
    |---------------------------------------------------
    | UPDATE USER
    |---------------------------------------------------
    */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $data = $request->validated();

        // update user data
        $this->userService->update($user->id, $data);

        // sync groups
        $this->authGroupService->syncGroups(
            $user->id,
            ...($data['groups'] ?? [])
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /*
    |---------------------------------------------------
    | DELETE USER
    |---------------------------------------------------
    */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $this->userService->delete($user->id);

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}