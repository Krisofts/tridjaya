@extends('layouts.app')

@section('content')

    <x-common.page-breadcrumb pageTitle="Create User" />

    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

            {{-- LEFT --}}
            <div class="space-y-6">

                <x-common.component-card title="User Information">

                    <div class="space-y-4">

                        {{-- NAME --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Name
                            </label>

                            <input type="text"
                                name="name"
                                value="{{ old('name') }}"
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
                                value="{{ old('email') }}"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 dark:bg-dark-900 dark:text-white/90"
                                required
                            />

                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                </x-common.component-card>




            </div>

            {{-- RIGHT --}}

            <div class="space-y-6">
                
                
                               <x-common.component-card title="Authentication">

                    <div class="space-y-4">

                        {{-- PASSWORD --}}
<div x-data="{ show: false }">
    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
        Password
    </label>

    <div class="relative">
        <input :type="show ? 'text' : 'password'"
            name="password"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 pr-10 text-sm dark:bg-dark-900 dark:text-white/90"
            required
        />

        <span @click="show = !show"
            class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer">

            <!-- Eye Open --> 
                <svg x-show="!show" class="fill-gray-500 dark:fill-gray-400" width="20" height="20"
                    viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM10.0002 4.04297C6.48191 4.04297 3.49489 6.30917 2.4155 9.4593C2.3615 9.61687 2.3615 9.78794 2.41549 9.94552C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C13.5184 15.3619 16.5055 13.0957 17.5849 9.94555C17.6389 9.78797 17.6389 9.6169 17.5849 9.45932C16.5055 6.30919 13.5184 4.04297 10.0002 4.04297ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z" />
                </svg>

                <svg x-show="show" class="fill-gray-500 dark:fill-gray-400" width="20" height="20"
                    viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M4.63803 3.57709C4.34513 3.2842 3.87026 3.2842 3.57737 3.57709C3.28447 3.86999 3.28447 4.34486 3.57737 4.63775L4.85323 5.91362C3.74609 6.84199 2.89363 8.06395 2.4155 9.45936C2.3615 9.61694 2.3615 9.78801 2.41549 9.94558C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C11.255 15.3619 12.4422 15.0737 13.4994 14.5598L15.3625 16.4229C15.6554 16.7158 16.1302 16.7158 16.4231 16.4229C16.716 16.13 16.716 15.6551 16.4231 15.3622L4.63803 3.57709ZM12.3608 13.4212L10.4475 11.5079C10.3061 11.5423 10.1584 11.5606 10.0064 11.5606H9.99151C8.96527 11.5606 8.13333 10.7286 8.13333 9.70237C8.13333 9.5461 8.15262 9.39434 8.18895 9.24933L5.91885 6.97923C5.03505 7.69015 4.34057 8.62704 3.92328 9.70247C4.86803 12.1373 7.23361 13.8619 10.0002 13.8619C10.8326 13.8619 11.6287 13.7058 12.3608 13.4212ZM16.0771 9.70249C15.7843 10.4569 15.3552 11.1432 14.8199 11.7311L15.8813 12.7925C16.6329 11.9813 17.2187 11.0143 17.5849 9.94561C17.6389 9.78803 17.6389 9.61696 17.5849 9.45938C16.5055 6.30925 13.5184 4.04303 10.0002 4.04303C9.13525 4.04303 8.30244 4.17999 7.52218 4.43338L8.75139 5.66259C9.1556 5.58413 9.57311 5.54303 10.0002 5.54303C12.7667 5.54303 15.1323 7.26768 16.0771 9.70249Z" />
                </svg>

        </span>
    </div>
</div>

                        {{-- CONFIRM --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Confirm Password
                            </label>

                            <input type="password"
                                name="password_confirmation"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 dark:bg-dark-900 dark:text-white/90"
                                required
                            />
                        </div>

                    </div>

                </x-common.component-card> 
                
                
                

<x-common.component-card title="Access Control">

    <div class="space-y-4">

        {{-- BRANCH --}}
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Branch
            </label>

            <div x-data="{ isOptionSelected: false }" class="relative">

                <select
                    name="branch_id"
                    class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 pr-11 text-sm dark:bg-dark-900 dark:text-white/90"
                    @change="isOptionSelected = true"
                    required
                >
                    <option value="">Select Branch</option>

                    @foreach($branches ?? [] as $id => $name)
                        <option value="{{ $id }}" @selected(old('branch_id') == $id)>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>

                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M5 7.5L10 12.5L15 7.5"
                            stroke="currentColor"
                            stroke-width="1.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"/>
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
                            @checked(is_array(old('groups')) && in_array($key, old('groups')))
                        />

                        <span class="text-sm">
                            {{ $label }}
                        </span>
                    </label>
                @endforeach

            </div>
        </div>

    </div>

    {{-- FOOTER ACTION (POJOK KANAN BAWAH) --}}
    <div class="mt-6 flex justify-end gap-2">

        <a href="{{ route('users.index') }}"
            class="px-4 py-2 rounded-lg bg-gray-300 text-gray-800 hover:bg-gray-400 transition">
            Cancel
        </a>

        <button type="submit"
            class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
            Create User
        </button>

    </div>

</x-common.component-card>



            </div>

        </div>

    </form>

@endsection