<div class="mb-6 rounded-2xl border border-gray-200 p-5 lg:p-6 dark:border-gray-800">
    <div class="flex flex-col gap-5 sm:flex-row xl:gap-10">

        {{-- LEFT --}}
        <div class="flex-1">

            <div class="mb-6 flex flex-col gap-5 sm:flex-row xl:items-center xl:justify-between">

                <div class="flex w-full flex-col items-start gap-6 sm:flex-row sm:items-center">

                    {{-- AVATAR --}}
                    <div class="overflow-hidden rounded-full border border-gray-200 dark:border-gray-800">
                        <img
                            src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=80"
                            class="size-20"
                            alt="user"
                        >
                    </div>

                    {{-- NAME + EMAIL --}}
                    <div>
                        <h4 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">
                            {{ $user->name }}
                        </h4>

                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $user->email }}
                        </p>
                    </div>

                </div>

            </div>

            {{-- INFO GRID --}}
            <div class="grid max-w-4xl grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

                <div>
                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">Name</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                        {{ $user->name }}
                    </p>
                </div>

                <div>
                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">Email</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                        {{ $user->email }}
                    </p>
                </div>

            </div>
        </div>

        {{-- BUTTON --}}
        <div class="flex items-start">
            <button
                x-data
                x-on:click="$dispatch('open-modal', 'edit-profile')"
                class="shadow-theme-xs flex h-10 w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/5"
            >
                
                Edit
            </button>
        </div>

    </div>
</div>

{{-- ========================= --}}
{{-- MODAL EDIT PROFILE --}}
{{-- ========================= --}}
<x-modal name="edit-profile" :show="false" focusable>
    <form method="post" action="{{ route('profile.update') }}" class="p-6 space-y-6">
        @csrf
        @method('patch')

        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            Edit Profile
        </h2>

        {{-- NAME --}}
        <div>
            <x-input-label for="name" value="Name" />

            <x-text-input
                id="name"
                name="name"
                type="text"
                class="mt-1 block w-full"
                :value="old('name', $user->name)"
                required
            />

            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- EMAIL --}}
        <div>
            <x-input-label for="email" value="Email" />

            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full"
                :value="old('email', $user->email)"
                required
            />

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- ACTION --}}
        <div class="flex justify-end gap-3 pt-4">

            <x-secondary-button x-on:click="$dispatch('close')">
                Cancel
            </x-secondary-button>

            <x-primary-button>
                Save
            </x-primary-button>

        </div>
    </form>
</x-modal>