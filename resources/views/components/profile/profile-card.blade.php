@php
    $user = auth()->user();
    $branches = \App\Branch\Models\Branch::select('id','name')->get();
@endphp

<div
    x-data="{
        open: false,

        name: '{{ $user->name }}',
        email: '{{ $user->email }}',
        branch_id: '{{ $user->branch_id ?? '' }}',

        branches: @js($branches),

        saveProfile() {
            console.log({
                name: this.name,
                email: this.email,
                branch_id: this.branch_id,
            });

            this.open = false;
        }
    }"
>

    {{-- PROFILE CARD --}}
    <div class="mb-6 rounded-2xl border border-gray-200 p-5 lg:p-6 dark:border-gray-800">

        <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">

            <div class="flex w-full flex-col items-center gap-6 xl:flex-row">

                {{-- AVATAR --}}
                <div class="w-20 h-20 rounded-full bg-gray-300 flex items-center justify-center text-gray-800 font-bold text-lg">
                    {{ $user->initials }}
                </div>

                {{-- INFO --}}
                <div>
                    <h4 class="mb-2 text-center text-lg font-semibold xl:text-left dark:text-white/90">
                        {{ $user->name }}
                    </h4>

                    <div class="flex flex-col items-center gap-1 xl:flex-row xl:gap-3 xl:text-left">

                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $user->email }}
                        </p>

                        <div class="hidden h-3.5 w-px bg-gray-300 xl:block dark:bg-gray-700"></div>

                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $user->branch?->name ?? '-' }}
                        </p>

                    </div>
                </div>

            </div>

            {{-- EDIT BUTTON --}}
            <button
                @click="open = true"
                class="flex w-full lg:w-auto items-center justify-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-3 text-sm font-medium dark:border-gray-700 dark:bg-gray-800"
            >
                Edit Profile
            </button>

        </div>
    </div>

    {{-- MODAL --}}
    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center"
    >

        {{-- BACKDROP --}}
        <div
            class="absolute inset-0 bg-black/50"
            @click="open = false"
        ></div>

        {{-- MODAL BOX --}}
        <div class="relative z-50 w-full max-w-[700px] rounded-3xl bg-white p-6 shadow-lg dark:bg-gray-900">

            {{-- HEADER --}}
            <div class="mb-6">
                <h4 class="text-2xl font-semibold text-gray-800 dark:text-white">
                    Edit Profile
                </h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Update your personal information
                </p>
            </div>

            {{-- FORM --}}
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">

                {{-- NAME --}}
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-300">Full Name</label>
                    <input
                        type="text"
                        x-model="name"
                        class="mt-1 h-11 w-full rounded-lg border px-4 text-sm dark:bg-transparent dark:border-gray-700"
                    >
                </div>

                {{-- EMAIL --}}
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-300">Email</label>
                    <input
                        type="email"
                        x-model="email"
                        class="mt-1 h-11 w-full rounded-lg border px-4 text-sm dark:bg-transparent dark:border-gray-700"
                    >
                </div>

                {{-- BRANCH --}}
                <div class="lg:col-span-2">
                    <label class="text-sm text-gray-600 dark:text-gray-300">Branch</label>

                    <select
                        x-model="branch_id"
                        class="mt-1 h-11 w-full rounded-lg border px-4 text-sm dark:bg-transparent dark:border-gray-700"
                    >
                        <template x-for="b in branches" :key="b.id">
                            <option :value="b.id" x-text="b.name"></option>
                        </template>
                    </select>
                </div>

            </div>

            {{-- ACTION --}}
            <div class="mt-6 flex justify-end gap-3">

                <button
                    type="button"
                    @click="open = false"
                    class="rounded-lg border px-4 py-2 text-sm dark:border-gray-700 dark:text-gray-300"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    @click="saveProfile"
                    class="rounded-lg bg-brand-500 px-4 py-2 text-sm text-white hover:bg-brand-600"
                >
                    Save Changes
                </button>

            </div>

        </div>
    </div>

</div>