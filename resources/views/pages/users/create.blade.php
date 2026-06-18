@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Create User" :breadcrumbs="[
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Users', 'url' => route('users.index')],
        ['label' => 'Create'],
    ]" />


    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        {{-- LEFT: USER FORM --}}
        <div class="space-y-6">
            <x-common.component-card title="User Information">

                <form id="userForm" action="{{ route('users.store') }}" method="POST" class="space-y-5">
                    @csrf

                    {{-- NAME --}}
                    <x-form.input.input-with-label label="Name" name="name" placeholder="Masukan nama lengkap" />
                    {{-- EMAIL --}}
                    <x-form.input.input-with-label label="Email" name="email" type="email"
                        placeholder="example@tridjaya.com" />
                    {{-- BRANCH --}}
                    <x-form.select.select name="branch_id" label="Branch" :options="$branches" valueField="id"
                        labelField="name" :selected="old('branch_id')" placeholder="Select Branch" />
                    {{-- PASSWORD --}}
                    <x-form.input.input-with-label label="Password" name="password" show-password-toggle />
                    
                    {{-- ACTION --}}
                    <x-common.card-action submitText="Create User" :cancelUrl="route('users.index')" />

                </form>

            </x-common.component-card>

        </div>

        {{-- RIGHT: GROUPS & PERMISSIONS --}}
        <div class="space-y-6">

            {{-- GROUPS --}}
            <x-common.component-card title="Groups">

                <div class="grid grid-cols-2 gap-2">
                    @forelse ($availableGroups as $group)
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" form="userForm" name="groups[]" value="{{ $group }}"
                                class="rounded" @checked(in_array($group, old('groups', [])))>

                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                {{ $group }}
                            </span>
                        </label>
                    @empty
                        <p class="text-sm text-gray-500">No groups available</p>
                    @endforelse
                </div>

            </x-common.component-card>

            {{-- PERMISSIONS --}}
            <x-common.component-card title="Permissions">

                <div class="grid grid-cols-2 gap-2">
                    @forelse ($availablePermissions as $permission)
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="permissions[]" value="{{ $permission }}" class="rounded"
                                @checked(in_array($permission, old('permissions', [])))>

                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                {{ $permission }}
                            </span>
                        </label>
                    @empty
                        <p class="text-sm text-gray-500">No permissions available</p>
                    @endforelse
                </div>

            </x-common.component-card>

        </div>

    </div>
@endsection
