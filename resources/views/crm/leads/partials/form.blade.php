<div class="p-4 sm:p-6 dark:border-gray-800">

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

        {{-- NAME --}}
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Name
            </label>

            <input
                type="text"
                name="name"
                value="{{ old('name', $lead->name ?? '') }}"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 
                       dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 
                       bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 
                       focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 
                       dark:text-white/90 dark:placeholder:text-white/30"
                required
            >

            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- PHONE --}}
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Phone
            </label>

            <input
                type="text"
                name="phone"
                value="{{ old('phone', $lead->phone ?? '') }}"
                placeholder="08123456789"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 
                       dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 
                       bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 
                       focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 
                       dark:text-white/90 dark:placeholder:text-white/30"
            >

            @error('phone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- ADDRESS --}}
        <div class="col-span-full">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Address
            </label>

            <textarea
                name="address"
                rows="3"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 
                       dark:focus:border-brand-800 w-full resize-none rounded-lg border border-gray-300 
                       bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 
                       focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 
                       dark:text-white/90 dark:placeholder:text-white/30"
            >{{ old('address', $lead->address ?? '') }}</textarea>

            @error('address')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- SOURCE --}}
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Source
            </label>

            <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                <select
                    name="source"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 
                           dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border 
                           border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 
                           focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 
                           dark:text-white/90"
                    @change="isOptionSelected = true"
                >
                    <option value="">Select Source</option>

                    @foreach(App\CRM\Models\Lead::sources() as $value => $label)
                        <option value="{{ $value }}" @selected(old('source', $lead->source ?? '') == $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M4.79 7.39L10 12.6l5.21-5.21" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </span>
            </div>

            @error('source')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- STATUS --}}
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Status
            </label>

            <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                <select
                    name="status"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 
                           dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border 
                           border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 
                           focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 
                           dark:text-white/90"
                >
                    @foreach(App\CRM\Models\Lead::statuses() as $value => $label)
                        <option value="{{ $value }}"
                            @selected(old('status', $lead->status ?? App\CRM\Models\Lead::defaultStatus()) == $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M4.79 7.39L10 12.6l5.21-5.21" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </span>
            </div>

            @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- INTEREST --}}
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Interest
            </label>

            <input
                list="interest-list"
                name="interest"
                value="{{ old('interest', $lead->interest ?? '') }}"
                placeholder="Type or choose interest"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 
                       dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 
                       bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 
                       focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 
                       dark:text-white/90 dark:placeholder:text-white/30"
            >

            <datalist id="interest-list">
                @foreach(App\CRM\Models\Lead::interests() as $value => $label)
                    <option value="{{ $value }}"></option>
                @endforeach
            </datalist>

            @error('interest')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- ASSIGNED TO --}}
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Assigned To
            </label>

            <div class="relative z-20 bg-transparent">
                <select
                    name="assigned_to"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 
                           dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border 
                           border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 
                           focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 
                           dark:text-white/90"
                >
                    <option value="">Unassigned</option>

                    @foreach($users as $user)
                        <option value="{{ $user->id }}"
                            @selected(old('assigned_to', $lead->assigned_to ?? '') == $user->id)>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>

                <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M4.79 7.39L10 12.6l5.21-5.21" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </span>
            </div>
        </div>

        {{-- NOTES --}}
        <div class="col-span-full">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Notes
            </label>

            <textarea
                name="notes"
                rows="5"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 
                       dark:focus:border-brand-800 w-full resize-none rounded-lg border border-gray-300 
                       bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 
                       focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 
                       dark:text-white/90 dark:placeholder:text-white/30"
            >{{ old('notes', $lead->notes ?? '') }}</textarea>
        </div>

    </div>
</div>