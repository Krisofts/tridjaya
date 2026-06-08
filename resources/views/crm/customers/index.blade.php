@extends('layouts.app')

@section('title', 'Customers')

@section('content')

<div class="p-6 max-w-7xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Customers</h1>
            <p class="text-sm text-gray-500">List semua customer dari CRM</p>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">

        <table class="w-full text-sm">

            <thead class="bg-gray-100 text-gray-600">
                <tr>
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Phone</th>
                    <th class="p-3 text-left">Source Lead</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-right">Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse($customers as $customer)

                    <tr class="border-t">

                        {{-- NAME --}}
                        <td class="p-3 font-medium text-gray-900">
                            {{ $customer->name }}
                        </td>

                        {{-- PHONE --}}
                        <td class="p-3">
                            {{ $customer->phone ?? '-' }}
                        </td>

                        {{-- LEAD SOURCE --}}
                        <td class="p-3 text-gray-600">
                            {{ $customer->lead?->source ?? '-' }}
                        </td>

                        {{-- STATUS --}}
                        <td class="p-3">
                            @if($customer->status === 'active')
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded">
                                    Active
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">
                                    {{ $customer->status }}
                                </span>
                            @endif
                        </td>

                        {{-- ACTION --}}
                        <td class="p-3 text-right space-x-2">

                            <a href="{{ route('crm.customers.show', $customer) }}"
                               class="text-blue-600">
                                View
                            </a>

                            <a href="{{ route('crm.customers.edit', $customer) }}"
                               class="text-yellow-600">
                                Edit
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500">
                            No customers found
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{-- PAGINATION --}}
    <div>
        {{ $customers->links() }}
    </div>

</div>

@endsection