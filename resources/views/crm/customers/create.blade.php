@extends('layouts.app')

@section('title', 'Create Customer')

@section('content')

<x-common.page-breadcrumb pageTitle="Convert Lead to Customer" />

<div class="max-w-3xl mx-auto">

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

        {{-- HEADER --}}
        <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-800">

            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                Convert Lead to Customer
            </h2>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Turn this lead into an active customer in your CRM.
            </p>

        </div>

        {{-- FORM --}}
        <form method="POST" action="{{ route('crm.customers.store') }}">
            @csrf

            <div class="p-6 space-y-6">

                <input type="hidden" name="lead_id" value="{{ $lead->id ?? '' }}">

                {{-- LEAD INFO --}}
                @if($lead)
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">

                        <div class="text-sm font-medium text-gray-800 dark:text-white/90">
                            {{ $lead->name }}
                        </div>

                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $lead->phone }}
                        </div>

                        @if($lead->email)
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $lead->email }}
                            </div>
                        @endif

                    </div>
                @endif

                {{-- STATUS --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Customer Status
                    </label>

                    <select name="status"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                            text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
                            focus:border-brand-300 focus:ring-brand-500/10">

                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>

                    </select>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4 dark:border-gray-800">

                <a href="{{ $lead ? route('crm.leads.show', $lead) : route('crm.customers.index') }}"
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

                    Convert Customer
                </button>

            </div>

        </form>

    </div>

</div>

@endsection