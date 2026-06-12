@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="Edit User" />

<form action="{{ route('users.update', $user->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

        {{-- LEFT COLUMN --}}
        <div class="space-y-6">

            {{-- USER INFO --}}
            <x-common.component-card title="User Information">

                <div class="space-y-4">

                    {{-- NAME --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Name
                        </label>

                        <input type="text"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 dark:bg-dark-900 dark:text-white/90"
                            required
                        />

                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- EMAIL --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Email
                        </label>

                        <input type="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 dark:bg-dark-900 dark:text-white/90"
                            required
                        />

                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

            </x-common.component-card>


            {{-- PASSWORD (OPTIONAL UPDATE) --}}
            <x-common.component-card title="Authentication">

                <div class="space-y-4">

                    {{-- PASSWORD --}}
                    <div x-data="{ show: false }">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Password (optional)
                        </label>

                        <div class="relative">
                            <input :type="show ? 'text' : 'password'"
                                name="password"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 pr-10 dark:bg-dark-900 dark:text-white/90"
                                placeholder="Leave empty if not changing"
                            />

                            <span @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer">

                                <!-- Eye Open -->
                                <svg x-show="!show" class="fill-gray-500 dark:fill-gray-400" width="20" height="20" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M10 3.75C5.857 3.75 2.5 6.64 2.5 10s3.357 6.25 7.5 6.25 7.5-2.89 7.5-6.25S14.143 3.75 10 3.75z"/>
                                </svg>

                                <!-- Eye Off -->
                                <svg x-show="show" class="fill-gray-500 dark:fill-gray-400" width="20" height="20" viewBox="0 0 20 20">
                                    <path d="M3.2 2.5L2 3.7l3.1 3.1C3.6 7.9 2.6 9.2 2.5 10c.6 3.3 3.8 6.25 7.5 6.25 1.3 0 2.5-.3 3.6-.8"/>
                                </svg>

                            </span>
                        </div>

                        <p class="text-xs text-gray-500 mt-1">
                            Kosongkan jika tidak ingin mengubah password
                        </p>
                    </div>

                </div>

            </x-common.component-card>

        </div>

        {{-- RIGHT COLUMN --}}
        <div class="space-y-6">

            {{-- ACCESS CONTROL --}}
            <x-common.component-card title="Access Control">

                <div class="space-y-4">

                    {{-- BRANCH --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Branch
                        </label>

                        <div class="relative">

                            <select name="branch_id"
                                class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 pr-11 dark:bg-dark-900 dark:text-white/90"
                                required
                            >
                                <option value="">Select Branch</option>

                                @foreach($branches ?? [] as $id => $name)
                                    <option value="{{ $id }}"
                                        @selected(old('branch_id', $user->branch_id) == $id)
                                    >
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>

                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M5 7.5L10 12.5L15 7.5"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        stroke-linecap="round"/>
                                </svg>
                            </span>

                        </div>
                    </div>

                    {{-- GROUPS --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Groups
                        </label>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">

                            @foreach($groups as $key => $label)
                                <label class="flex items-center gap-2 border border-gray-200 dark:border-gray-700 p-2 rounded-lg">

                                    <input type="checkbox"
                                        name="groups[]"
                                        value="{{ $key }}"
                                        class="h-4 w-4"
                                        @checked(
                                            is_array(old('groups', $userGroups ?? [])) &&
                                            in_array($key, old('groups', $userGroups ?? []))
                                        )
                                    />

                                    <span class="text-sm">
                                        {{ $label }}
                                    </span>

                                </label>
                            @endforeach

                        </div>
                    </div>

                </div>

                {{-- FOOTER ACTION --}}
                <div class="mt-6 flex justify-end gap-2">

                    <a href="{{ route('users.index') }}"
                        class="px-4 py-2 rounded-lg bg-gray-300 text-gray-800 hover:bg-gray-400">
                        Cancel
                    </a>

                    <button type="submit"
                        class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                        Update User
                    </button>

                </div>

            </x-common.component-card>

        </div>

    </div>

</form>

@endsection