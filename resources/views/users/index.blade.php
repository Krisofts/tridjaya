@extends('layouts.app')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold">Users</h1>
            <p class="text-sm text-gray-500">Manage system users</p>
        </div>

        <a href="{{ route('users.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            + Create User
        </a>
    </div>

    {{-- FILTER --}}
    <x-common.component-card title="Filter Users">

        <form method="GET" class="space-y-4">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                {{-- SEARCH --}}
                <div>
                    <input type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search name / email"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:bg-dark-900 dark:text-white/90"
                    />
                </div>

                {{-- BRANCH --}}
                <div>
                    <select name="branch_id"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:bg-dark-900 dark:text-white/90">

                        <option value="">All Branch</option>

                        @foreach($branches ?? [] as $id => $name)
                            <option value="{{ $id }}" @selected(request('branch_id') == $id)>
                                {{ $name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- GROUP --}}
                <div>
                    <select name="group"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:bg-dark-900 dark:text-white/90">

                        <option value="">All Group</option>

                        @foreach($groups ?? [] as $key => $title)
                            <option value="{{ $key }}" @selected(request('group') == $key)>
                                {{ $title }}
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- SORT --}}
                <div>
                    <select name="sort"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:bg-dark-900 dark:text-white/90">

                        <option value="">Latest</option>
                        <option value="name_asc" @selected(request('sort') == 'name_asc')>Name A-Z</option>
                        <option value="name_desc" @selected(request('sort') == 'name_desc')>Name Z-A</option>
                        <option value="email_asc" @selected(request('sort') == 'email_asc')>Email A-Z</option>
                        <option value="email_desc" @selected(request('sort') == 'email_desc')>Email Z-A</option>
                        <option value="oldest" @selected(request('sort') == 'oldest')>Oldest</option>

                    </select>
                </div>

            </div>

            {{-- ACTION --}}
            <div class="flex gap-2 justify-end">

                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Apply Filter
                </button>

                <a href="{{ route('users.index') }}"
                   class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                    Reset
                </a>

            </div>

        </form>

    </x-common.component-card>


    {{-- TABLE --}}
    <x-common.component-card title="Users List">

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead>
                    <tr class="text-left border-b border-gray-200 dark:border-gray-700">
                        <th class="py-3">Name</th>
                        <th>Email</th>
                        <th>Branch</th>
                        <th>Groups</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($users as $user)
                        <tr class="border-b border-gray-100 dark:border-gray-800">

                            <td class="py-3 font-medium text-gray-800 dark:text-white/90">
                                {{ $user->name }}
                            </td>

                            <td class="text-gray-600 dark:text-gray-300">
                                {{ $user->email }}
                            </td>

                            <td class="text-gray-600 dark:text-gray-300">
                                {{ $user->branch?->name ?? '-' }}
                            </td>

                            <td>
                                <div class="flex flex-wrap gap-1">

                                    @forelse($user->groups ?? [] as $group)
                                        <span class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-800 rounded">
                                            {{ $group->group ?? '-' }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400">No group</span>
                                    @endforelse

                                </div>
                            </td>

                            <td class="text-right">
                                <a href="{{ route('users.edit', $user) }}"
                                   class="text-blue-600 hover:underline">
                                    Edit
                                </a>
                            </td>

                        </tr>
                    @empty

                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-500">
                                No users found
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- PAGINATION --}}
        <div class="mt-4">
            {{ $users->links() }}
        </div>

    </x-common.component-card>

</div>

@endsection