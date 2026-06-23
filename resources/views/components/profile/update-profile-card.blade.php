<div x-data="{ show: false }">

    {{-- CARD --}}
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

                        {{-- INFO --}}
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

                {{-- GRID --}}
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
                    @click="show = true"
                    class="shadow-theme-xs flex h-10 w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/5"
                >
                   
                   <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" fill=""></path>
                      </svg>
                   
                    Edit
                </button>
            </div>

        </div>
    </div>

    {{-- MODAL (PAKAI COMPONENT KAMU) --}}
    <x-modal.modal
        :show="'show'"
        title="Update Profile"
        description="Edit informasi akun kamu"
    >

        <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
            @csrf
            @method('patch')
            
            
                        <x-form.input.input-with-label name="name" label="Name" 
                                            :value="$user->name"
                        required placeholder="Masukan Nama Lengkap" />
            

                        <x-form.input.input-with-label name="email" label="Email" 
                                            :value="$user->email"
                        required placeholder="Masukan Email" />
            

        <div class="flex items-center justify-end gap-3">

            <a                     @click="show = false"
                class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                Cancel
            </a>

            <button type="submit"
                class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                Update
            </button>

        </div>




        </form>

    </x-modal-profile>

</div>