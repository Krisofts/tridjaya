@extends('layouts.app')

@section('title', 'Customers')

@section('content')

<x-common.page-breadcrumb pageTitle="Customers" />

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

    {{-- HEADER --}}
    <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-800">

        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                Customers
            </h2>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                List all customers from CRM pipeline.
            </p>
        </div>

    </div>

    {{-- TABLE --}}
    <div class="max-w-full overflow-x-auto custom-scrollbar">

        <table class="min-w-full">

            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-800">

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                        Name
                    </th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                        Phone
                    </th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                        Source Lead
                    </th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                        Status
                    </th>

                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">
                        Action
                    </th>

                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">

                @forelse($customers as $customer)

                    <tr>

                        {{-- NAME --}}
                        <td class="px-6 py-3.5 font-medium text-gray-800 dark:text-white/90">
                            {{ $customer->name }}
                        </td>

                        {{-- PHONE --}}
                        <td class="px-6 py-3.5 text-gray-500 dark:text-gray-400">
                            {{ $customer->phone ?? '-' }}
                        </td>

                        {{-- SOURCE --}}
                        <td class="px-6 py-3.5 text-gray-500 dark:text-gray-400">
                            {{ $customer->lead?->source ?? '-' }}
                        </td>

                        {{-- STATUS --}}
                        <td class="px-6 py-3.5">

                            @php
                                $statusColors = [
                                    'active' => 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-400',
                                    'inactive' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
                                ];
                            @endphp

                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium
                                {{ $statusColors[$customer->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($customer->status) }}
                            </span>

                        </td>

                        {{-- ACTION --}}
                        <td class="px-6 py-3.5">
                            <div class="flex justify-end gap-4">

                                <a href="{{ route('crm.customers.show', $customer) }}"
                                   class="text-brand-500 text-sm font-medium hover:text-brand-600">
                                    View
                                </a>

                                <a href="{{ route('crm.customers.edit', $customer) }}"
                                   class="text-warning-600 text-sm font-medium hover:text-warning-700 dark:text-warning-400">
                                    Edit
                                </a>

                            </div>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                            No customers found
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{-- PAGINATION --}}
    @if ($customers->hasPages())
        <div class="flex flex-col items-center justify-between border-t border-gray-200 px-5 py-4 sm:flex-row dark:border-gray-800">

            {{-- INFO --}}
            <div class="pb-3 sm:pb-0 text-sm text-gray-500">
                Showing
                <span class="text-gray-800 dark:text-white/90">{{ $customers->firstItem() }}</span>
                to
                <span class="text-gray-800 dark:text-white/90">{{ $customers->lastItem() }}</span>
                of
                <span class="text-gray-800 dark:text-white/90">{{ $customers->total() }}</span>
            </div>

            {{-- LINKS --}}
            <div>
                {{ $customers->links() }}
            </div>

        </div>
    @endif

</div>

@endsection