<div x-data="{ show: false }">

    {{-- ========================= --}}
    {{-- PASSWORD CARD --}}
    {{-- ========================= --}}
    <div class="rounded-2xl border border-gray-200 p-5 lg:p-6 dark:border-gray-800">

        <h4 class="text-lg font-semibold text-gray-800 lg:mb-6 dark:text-white/90">
            Security
        </h4>
        <div>
            <div class="flex flex-col justify-between gap-4  border-gray-200 py-4 first:pt-0 last:border-b-0 last:pb-0 sm:flex-row sm:items-end dark:border-gray-800">
                <div>
                    <span class="mb-1 block text-base font-medium text-gray-800 dark:text-white/90">Change Password</span>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Receive real-time notifications and team alerts.
                    </p>
                </div>
                <div>
                    <button type="button"
                        @click="show = true" class="shadow-theme-xs flex h-10 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white py-2.5 pr-4 pl-3.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3 dark:hover:text-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M12.3861 5.08087L14.9182 7.61296M15.6437 3.5917L16.408 4.35603C16.8962 4.84419 16.8962 5.63564 16.408 6.1238L7.83547 14.6963C7.69039 14.8414 7.51182 14.9486 7.31554 15.0083L3.97461 16.0251L4.99141 12.6842C5.05115 12.4879 5.15829 12.3093 5.30337 12.1642L13.8759 3.5917C14.3641 3.10355 15.1555 3.10355 15.6437 3.5917Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                        Change Password
                    </button>
                </div>
            </div>







        </div>

        {{-- ========================= --}}
        {{-- MODAL --}}
        {{-- ========================= --}}
        <x-modal.modal
            :show="'show'"
            title="Update Password"
            description="Ganti password akun kamu untuk keamanan"
            >

            <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                @csrf
                @method('put')




                {{-- CURRENT PASSWORD --}}
                <x-form.input.input-with-label
                    
                    name="current_password"
                    label="Current Password"
                    bag="updatePassword"
                    required
                    
                    />

                {{-- NEW PASSWORD --}}
                <x-form.input.input-with-label
                    type="password"
                    name="password"
                    label="New Password"
                    autocomplete="new-password"
                    bag="updatePassword"
                    required
                    
                    />

                {{-- CONFIRM PASSWORD --}}
                <x-form.input.input-with-label
                    type="password"
                    name="password_confirmation"
                    label="Confirm Password"
                    autocomplete="new-password"
                    bag="updatePassword"
                    required
                    
                    />

                {{-- ACTION --}}
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

        </x-modal.modal>

    </div>