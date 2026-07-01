@extends('layouts.app')

@section('content')

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

        {{-- LEFT: USER FORM --}}
        <div class="space-y-6">
            <x-common.component-card title="User Information">

                <form id="userForm" action="{{ route('users.store') }}" method="POST" class="space-y-5">
                    @csrf

                    {{-- NAME --}}
                    <x-form.input.input-with-label
                        label="Nama Lengkap"
                        name="name"
                        placeholder="Masukkan nama lengkap"
                    />

                    {{-- NIK --}}
                    <x-form.input.input-with-label
                        label="NIK"
                        name="nik"
                        placeholder="Masukkan NIK"
                    />

                    {{-- EMAIL --}}
                    <x-form.input.input-with-label
                        label="Email"
                        name="email"
                        type="email"
                        placeholder="example@tridjaya.com"
                    />

                    {{-- BRANCH --}}
                    <x-form.select.select
                        name="branch_id"
                        label="Branch"
                        :options="$branches"
                        valueField="id"
                        labelField="name"
                        :selected="old('branch_id')"
                        placeholder="Pilih Branch"
                    />

                    {{-- PASSWORD --}}
                    <x-form.input.input-with-label
                        label="Password"
                        name="password"
                        show-password-toggle
                    />

                    {{-- ACTION --}}
                    <x-common.card-action submitText="Buat User" :cancelUrl="route('users.index')" />

                </form>

            </x-common.component-card>
        </div>

        {{-- RIGHT: GROUPS & PERMISSIONS --}}
        <div class="space-y-6">

            {{-- GROUPS --}}
            <x-common.component-card title="Groups">
                <div class="grid grid-cols-2 gap-3">
                    @forelse($availableGroups as $group)
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input
                                type="checkbox"
                                form="userForm"
                                name="groups[]"
                                value="{{ $group }}"
                                @checked(in_array($group, old('groups', [])))
                                class="w-4 h-4 rounded border-gray-300 dark:border-gray-600
                                       text-blue-600 dark:bg-gray-800
                                       focus:ring-blue-500 dark:focus:ring-offset-gray-900
                                       cursor-pointer"
                            >
                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors capitalize">
                                {{ $group }}
                            </span>
                        </label>
                    @empty
                        <p class="col-span-2 text-sm text-gray-400 dark:text-gray-500">Tidak ada group tersedia.</p>
                    @endforelse
                </div>
            </x-common.component-card>

            {{-- PERMISSIONS --}}
            <x-common.component-card title="Permissions">
                <div class="grid grid-cols-2 gap-3">
                    @forelse($availablePermissions as $permission)
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input
                                type="checkbox"
                                form="userForm"
                                name="permissions[]"
                                value="{{ $permission }}"
                                @checked(in_array($permission, old('permissions', [])))
                                class="w-4 h-4 rounded border-gray-300 dark:border-gray-600
                                       text-blue-600 dark:bg-gray-800
                                       focus:ring-blue-500 dark:focus:ring-offset-gray-900
                                       cursor-pointer"
                            >
                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">
                                {{ $permission }}
                            </span>
                        </label>
                    @empty
                        <p class="col-span-2 text-sm text-gray-400 dark:text-gray-500">Tidak ada permission tersedia.</p>
                    @endforelse
                </div>
            </x-common.component-card>

        </div>

    </div>

@endsection