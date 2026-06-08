<div class="grid gap-4">

    {{-- NAME --}}
    <div>
        <label class="block mb-1 font-medium">
            Name
        </label>

        <input 
            type="text"
            name="name"
            value="{{ old('name', $lead->name ?? '') }}"
            class="w-full border rounded px-3 py-2"
            required
        >

        @error('name')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- PHONE --}}
    <div>
        <label class="block mb-1 font-medium">
            Phone
        </label>

        <input
            type="text"
            name="phone"
            value="{{ old('phone', $lead->phone ?? '') }}"
            class="w-full border rounded px-3 py-2"
            placeholder="08123456789"
        >

        @error('phone')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- ADDRESS --}}
    <div>
        <label class="block mb-1 font-medium">
            Address
        </label>

        <textarea
            name="address"
            rows="3"
            class="w-full border rounded px-3 py-2"
        >{{ old('address', $lead->address ?? '') }}</textarea>

        @error('address')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- SOURCE --}}
    <div>
        <label class="block mb-1 font-medium">
            Source
        </label>

        <select name="source" class="w-full border rounded px-3 py-2">
            <option value="">Select Source</option>

            @foreach(App\CRM\Models\Lead::sources() as $value => $label)
                <option value="{{ $value }}"
                    @selected(old('source', $lead->source ?? '') == $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        @error('source')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- STATUS --}}
    <div>
        <label class="block mb-1 font-medium">
            Status
        </label>

        <select name="status" class="w-full border rounded px-3 py-2">

            @foreach(App\CRM\Models\Lead::statuses() as $value => $label)
                <option value="{{ $value }}"
                    @selected(old('status', $lead->status ?? App\CRM\Models\Lead::defaultStatus()) == $value)>
                    {{ $label }}
                </option>
            @endforeach

        </select>

        @error('status')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- INTEREST (UPDATED: INPUT + SUGGESTION) --}}
    <div>
        <label class="block mb-1 font-medium">
            Interest
        </label>

        <input
            list="interest-list"
            name="interest"
            value="{{ old('interest', $lead->interest ?? '') }}"
            class="w-full border rounded px-3 py-2"
            placeholder="Type or choose interest"
        >

        <datalist id="interest-list">
            @foreach(App\CRM\Models\Lead::interests() as $value => $label)
                <option value="{{ $value }}">
                    {{ $label }}
                </option>
            @endforeach
        </datalist>

        @error('interest')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- ASSIGNED TO --}}
    <div>
        <label class="block mb-1 font-medium">
            Assigned To
        </label>

        <select name="assigned_to" class="w-full border rounded px-3 py-2">
            <option value="">Unassigned</option>

            @foreach($users as $user)
                <option value="{{ $user->id }}"
                    @selected(old('assigned_to', $lead->assigned_to ?? '') == $user->id)>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>

        @error('assigned_to')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- NOTES --}}
    <div>
        <label class="block mb-1 font-medium">
            Notes
        </label>

        <textarea
            name="notes"
            rows="5"
            class="w-full border rounded px-3 py-2"
        >{{ old('notes', $lead->notes ?? '') }}</textarea>

        @error('notes')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

</div>