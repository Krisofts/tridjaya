<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                User Management
            </h1>

            <p class="text-sm text-gray-500">
                Manage users, branches, and permissions
            </p>
        </div>

        <a href="{{ route('users.create') }}"
           class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + Create User
        </a>

    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- SEARCH --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">

        <form method="GET">

            <div class="flex flex-col gap-3 md:flex-row">

                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search name or email..."
                       class="flex-1 rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring focus:ring-blue-200">

                <button type="submit"
                        class="px-5 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800">
                    Search
                </button>

            </div>

        </form>

    </div>

    {{-- DESKTOP TABLE --}}
    <div class="hidden md:block bg-white rounded-xl shadow-sm overflow-hidden">

        <table class="w-full">

            <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-4 py-3 font-semibold">Name</th>
                <th class="text-left px-4 py-3 font-semibold">Email</th>
                <th class="text-left px-4 py-3 font-semibold">Branch</th>
                <th class="text-left px-4 py-3 font-semibold">Groups</th>
                <th class="text-right px-4 py-3 font-semibold">Actions</th>
            </tr>
            </thead>

            <tbody>

            @forelse($users as $user)
                <tr class="border-t">

                    {{-- NAME --}}
                    <td class="px-4 py-3">
                        {{ $user->name }}
                    </td>

                    {{-- EMAIL --}}
                    <td class="px-4 py-3">
                        {{ $user->email }}
                    </td>

                    {{-- BRANCH --}}
                    <td class="px-4 py-3">

                        @if($user->branch)
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                {{ $user->branch->code }} - {{ $user->branch->name }}
                            </span>
                        @else
                            <span class="text-gray-400 text-sm">No Branch</span>
                        @endif

                    </td>

                    {{-- GROUPS --}}
                    <td class="px-4 py-3">

                        @forelse($user->groups as $group)
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700 mr-1">
                                {{ $group->group }}
                            </span>
                        @empty
                            <span class="text-gray-400">No Group</span>
                        @endforelse

                    </td>

                    {{-- ACTIONS --}}
                    <td class="px-4 py-3">

                        <div class="flex justify-end gap-3">

                            @can('update', $user)
                                <a href="{{ route('users.edit', $user) }}"
                                   class="text-blue-600 hover:text-blue-800">
                                    Edit
                                </a>
                            @endcan

                            <form action="{{ route('users.destroy', $user) }}" method="POST">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        onclick="return confirm('Delete user?')"
                                        class="text-red-600 hover:text-red-800">
                                    Delete
                                </button>
                            </form>

                        </div>

                    </td>

                </tr>

            @empty
                <tr>
                    <td colspan="5" class="text-center py-10 text-gray-500">
                        No users found.
                    </td>
                </tr>
            @endforelse

            </tbody>

        </table>

    </div>

    {{-- MOBILE CARDS --}}
    <div class="md:hidden space-y-4">

        @forelse($users as $user)

            <div class="bg-white rounded-xl shadow-sm p-4">

                {{-- NAME + EMAIL --}}
                <div class="mb-2">
                    <h3 class="font-semibold text-gray-900">
                        {{ $user->name }}
                    </h3>

                    <p class="text-sm text-gray-500">
                        {{ $user->email }}
                    </p>

                    {{-- BRANCH --}}
                    <div class="mt-1">

                        @if($user->branch)
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                {{ $user->branch->code }} - {{ $user->branch->name }}
                            </span>
                        @else
                            <span class="text-gray-400 text-sm">No Branch</span>
                        @endif

                    </div>

                </div>

                {{-- GROUPS --}}
                <div class="mb-4">

                    @forelse($user->groups as $group)
                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700 mr-1">
                            {{ $group->group }}
                        </span>
                    @empty
                        <span class="text-gray-400 text-sm">No Group</span>
                    @endforelse

                </div>

                {{-- ACTIONS --}}
                <div class="flex gap-4">

                    @can('update', $user)
                        <a href="{{ route('users.edit', $user) }}"
                           class="text-blue-600 hover:text-blue-800">
                            Edit
                        </a>
                    @endcan

                    <form action="{{ route('users.destroy', $user) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                onclick="return confirm('Delete user?')"
                                class="text-red-600">
                            Delete
                        </button>
                    </form>

                </div>

            </div>

        @empty
            <div class="bg-white rounded-xl p-6 text-center text-gray-500">
                No users found.
            </div>
        @endforelse

    </div>

    {{-- PAGINATION --}}
    <div class="mt-6">
        {{ $users->links() }}
    </div>

</div>

</body>
</html>