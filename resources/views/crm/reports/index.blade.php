@extends('layouts.app')

@section('title', 'CRM Dashboard Report')

@section('content')

<div class="p-6 max-w-7xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">
            CRM Analytics Dashboard
        </h1>
        <p class="text-sm text-gray-500">
            Lead performance, funnel, and conversion insights
        </p>
    </div>

    {{-- FILTER --}}
    <form method="GET" class="bg-white p-4 rounded-xl shadow flex flex-wrap gap-3 items-end">

        <div>
            <label class="text-xs text-gray-500">Search User</label>
            <input type="text"
                   name="search"
                   value="{{ $filters['search'] ?? '' }}"
                   class="border rounded-lg px-3 py-2 w-48"
                   placeholder="User name...">
        </div>

        <div>
            <label class="text-xs text-gray-500">From</label>
            <input type="date"
                   name="from"
                   value="{{ $filters['from'] ?? '' }}"
                   class="border rounded-lg px-3 py-2">
        </div>

        <div>
            <label class="text-xs text-gray-500">To</label>
            <input type="date"
                   name="to"
                   value="{{ $filters['to'] ?? '' }}"
                   class="border rounded-lg px-3 py-2">
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">
            Filter
        </button>

    </form>

    {{-- FUNNEL CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <div class="bg-white p-5 rounded-xl shadow">
            <div class="text-sm text-gray-500">Leads</div>
            <div class="text-2xl font-bold">{{ $funnel['leads'] }}</div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow">
            <div class="text-sm text-gray-500">Deal</div>
            <div class="text-2xl font-bold text-blue-600">{{ $funnel['deal'] }}</div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow">
            <div class="text-sm text-gray-500">Won</div>
            <div class="text-2xl font-bold text-green-600">{{ $funnel['won'] }}</div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow">
            <div class="text-sm text-gray-500">Customers</div>
            <div class="text-2xl font-bold text-purple-600">{{ $funnel['customers'] }}</div>
        </div>

    </div>

    {{-- CONVERSION RATE --}}
    <div class="bg-white p-5 rounded-xl shadow">

        <h2 class="font-semibold mb-4">Conversion Rate</h2>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">

            <div>
                <div class="text-gray-500">Lead → Deal</div>
                <div class="text-xl font-bold">{{ $conversion['lead_to_deal'] }}%</div>
            </div>

            <div>
                <div class="text-gray-500">Deal → Won</div>
                <div class="text-xl font-bold">{{ $conversion['deal_to_won'] }}%</div>
            </div>

            <div>
                <div class="text-gray-500">Lead → Won</div>
                <div class="text-xl font-bold">{{ $conversion['lead_to_won'] }}%</div>
            </div>

            <div>
                <div class="text-gray-500">Won → Customer</div>
                <div class="text-xl font-bold">{{ $conversion['won_to_customer'] }}%</div>
            </div>

        </div>

    </div>

    {{-- USER TABLE --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">

        <div class="p-4 border-b">
            <h2 class="font-semibold">User Performance</h2>
        </div>

        <table class="w-full text-sm">

            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">User</th>
                    <th class="p-3 text-center">Today</th>
                    <th class="p-3 text-center">Leads</th>
                    <th class="p-3 text-center">Won</th>
                    <th class="p-3 text-center">Win Rate</th>
                </tr>
            </thead>

            <tbody>

                @forelse($users as $user)

                    @php
                        $winRate = $user->total_leads > 0
                            ? round(($user->total_won / $user->total_leads) * 100, 2)
                            : 0;
                    @endphp

                    <tr class="border-t hover:bg-gray-50">

                        <td class="p-3 font-medium">
                            {{ $user->name }}
                        </td>

                        <td class="p-3 text-center">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                                {{ $user->leads_today }}
                            </span>
                        </td>

                        <td class="p-3 text-center font-semibold">
                            {{ $user->total_leads }}
                        </td>

                        <td class="p-3 text-center text-green-600 font-semibold">
                            {{ $user->total_won }}
                        </td>

                        <td class="p-3 text-center">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $winRate >= 50 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                {{ $winRate }}%
                            </span>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500">
                            No data found
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection