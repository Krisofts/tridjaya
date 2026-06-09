@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')

<x-common.page-breadcrumb pageTitle="Edit Customer" />

<div class="max-w-3xl mx-auto">

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

        {{-- HEADER --}}
        <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-800">

            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                Edit Customer
            </h2>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Update customer information and profile details.
            </p>

        </div>

        {{-- FORM --}}
        <form method="POST" action="{{ route('crm.customers.update', $customer) }}">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-5">

                {{-- NAME --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Name
                    </label>

                    <input type="text"
                           name="name"
                           value="{{ old('name', $customer->name) }}"
                           required
                           class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                           text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
                           focus:border-brand-300 focus:ring-brand-500/10">
                </div>

                {{-- PHONE --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Phone
                    </label>

                    <input type="text"
                           name="phone"
                           value="{{ old('phone', $customer->phone) }}"
                           placeholder="628xxxx"
                           class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                           text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
                           focus:border-brand-300 focus:ring-brand-500/10">
                </div>

                {{-- ADDRESS --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Address
                    </label>

                    <textarea name="address"
                              rows="3"
                              class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm
                              text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
                              focus:border-brand-300 focus:ring-brand-500/10">{{ old('address', $customer->address) }}</textarea>
                </div>

                {{-- EMAIL --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Email
                    </label>

                    <input type="email"
                           name="email"
                           value="{{ old('email', $customer->email) }}"
                           class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                           text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
                           focus:border-brand-300 focus:ring-brand-500/10">
                </div>

                {{-- STATUS --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Status
                    </label>

                    <select name="status"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                            dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                        <option value="active" @selected($customer->status === 'active')>Active</option>
                        <option value="inactive" @selected($customer->status === 'inactive')>Inactive</option>
                        <option value="blacklist" @selected($customer->status === 'blacklist')>Blacklist</option>

                    </select>
                </div>

                {{-- NOTES --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Notes
                    </label>

                    <textarea name="notes"
                              rows="3"
                              class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm
                              text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
                              focus:border-brand-300 focus:ring-brand-500/10">{{ old('notes', $customer->notes) }}</textarea>
                </div>

            </div>

            {{-- FOOTER ACTION --}}
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4 dark:border-gray-800">

                <a href="{{ route('crm.customers.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-300
                          bg-white px-4 py-2.5 text-sm font-medium text-gray-700
                          hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800
                          dark:text-gray-300 dark:hover:bg-gray-700">

                    Cancel
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg
                               bg-brand-500 px-4 py-2.5 text-sm font-medium text-white
                               shadow-theme-xs hover:bg-brand-600 transition">

                    Update Customer
                </button>

            </div>

        </form>

    </div>

</div>

@endsection