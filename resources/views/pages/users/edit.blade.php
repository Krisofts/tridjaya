@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb
    pageTitle="Edit User"
    :breadcrumbs="[
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Users', 'url' => route('users.index')],
        ['label' => 'Edit']
    ]"
/>

<div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

    {{-- LEFT: FORM --}}
    <div class="space-y-6">

        <x-common.component-card title="Edit User">

            <form id="userForm"
                  action="{{ route('users.update', $user) }}"
                  method="POST"
                  class="space-y-5">

                @csrf
                @method('PUT')



                
              {{-- NAME --}}
<x-form.input.input-with-label
    label="Name"
    name="name"
    :value="old('name', $user->name)"
    placeholder="Masukan nama lengkap"
/>

{{-- EMAIL --}}
<x-form.input.input-with-label
    label="Email"
    name="email"
    type="email"
    :value="old('email', $user->email)"
    placeholder="example@tridjaya.com"
/>

{{-- BRANCH --}}
<x-form.select.select
    name="branch_id"
    label="Branch"
    :options="$branches"
    valueField="id"
    labelField="name"
    :selected="old('branch_id', $user->branch_id)"
    placeholder="Select Branch"
/>

{{-- PASSWORD --}}
<x-form.input.input-with-label
    label="Password"
    name="password"
    type="password"
    placeholder="Leave blank if not change"
    show-password-toggle
/>

{{-- ACTION --}}
<x-common.card-action
    submitText="Update User"
    :cancelUrl="route('users.index')"
/>

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
                        <input type="checkbox"
                               form="userForm"
                               name="groups[]"
                               value="{{ $group }}"
                               class="rounded"
                               @checked(in_array($group, old('groups', $selectedGroups ?? [])))>

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
                        <input type="checkbox"
                               name="permissions[]"
                               value="{{ $permission }}"
                               form="userForm"
                               class="rounded"
                               @checked(in_array($permission, old('permissions', $selectedPermissions ?? [])))>

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