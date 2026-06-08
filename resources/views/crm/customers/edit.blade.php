@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')

<div class="p-6 max-w-3xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">
            Edit Customer
        </h1>
        <p class="text-sm text-gray-500">
            Update customer data
        </p>
    </div>

    {{-- FORM --}}
    <form method="POST"
          action="{{ route('crm.customers.update', $customer) }}"
          class="bg-white rounded-xl shadow p-6 space-y-5">

        @csrf
        @method('PUT')

        {{-- NAME --}}
        <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text"
                   name="name"
                   value="{{ old('name', $customer->name) }}"
                   class="w-full border rounded-lg px-3 py-2"
                   required>
        </div>

        {{-- PHONE --}}
        <div>
            <label class="block text-sm font-medium mb-1">Phone</label>
            <input type="text"
                   name="phone"
                   value="{{ old('phone', $customer->phone) }}"
                   class="w-full border rounded-lg px-3 py-2"
                   placeholder="628xxxx">
        </div>

        {{-- ADDRESS --}}
        <div>
            <label class="block text-sm font-medium mb-1">Address</label>
            <textarea name="address"
                      rows="3"
                      class="w-full border rounded-lg px-3 py-2">{{ old('address', $customer->address) }}</textarea>
        </div>

        {{-- EMAIL (optional future-proof CRM) --}}
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email"
                   name="email"
                   value="{{ old('email', $customer->email) }}"
                   class="w-full border rounded-lg px-3 py-2">
        </div>

        {{-- STATUS --}}
        <div>
            <label class="block text-sm font-medium mb-1">Status</label>

            <select name="status" class="w-full border rounded-lg px-3 py-2">
                <option value="active" @selected($customer->status === 'active')>Active</option>
                <option value="inactive" @selected($customer->status === 'inactive')>Inactive</option>
                <option value="blacklist" @selected($customer->status === 'blacklist')>Blacklist</option>
            </select>
        </div>

        {{-- NOTES --}}
        <div>
            <label class="block text-sm font-medium mb-1">Notes</label>
            <textarea name="notes"
                      rows="3"
                      class="w-full border rounded-lg px-3 py-2">{{ old('notes', $customer->notes) }}</textarea>
        </div>

        {{-- ACTIONS --}}
        <div class="flex justify-end gap-3">

            <a href="{{ route('crm.customers.index') }}"
               class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">
                Cancel
            </a>

            <button type="submit"
                    class="px-5 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700">
                Update Customer
            </button>

        </div>

    </form>

</div>

@endsection