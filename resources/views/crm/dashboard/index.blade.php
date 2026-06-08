@extends('layouts.app')

@section('title', 'CRM Dashboard')

@section('content')

<div class="p-6 space-y-6 max-w-7xl mx-auto">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">CRM Dashboard</h1>
        <p class="text-sm text-gray-500">Overview sales, funnel, revenue & customers</p>
    </div>

    {{-- KPI CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <div class="bg-white p-5 rounded-xl shadow">
            <div class="text-sm text-gray-500">Total Leads</div>
            <div class="text-2xl font-bold">{{ $totalLeads }}</div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow">
            <div class="text-sm text-gray-500">New Leads</div>
            <div class="text-2xl font-bold text-blue-600">{{ $newLeads }}</div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow">
            <div class="text-sm text-gray-500">Won Leads</div>
            <div class="text-2xl font-bold text-green-600">{{ $wonLeads }}</div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow">
            <div class="text-sm text-gray-500">Lost Leads</div>
            <div class="text-2xl font-bold text-red-600">{{ $lostLeads }}</div>
        </div>

    </div>

    {{-- REVENUE + CUSTOMER --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <div class="bg-white p-5 rounded-xl shadow">
            <div class="text-sm text-gray-500">Total Revenue</div>
            <div class="text-2xl font-bold">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow">
            <div class="text-sm text-gray-500">Cash Revenue</div>
            <div class="text-xl font-semibold text-green-600">
                Rp {{ number_format($cashRevenue, 0, ',', '.') }}
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow">
            <div class="text-sm text-gray-500">Credit Revenue</div>
            <div class="text-xl font-semibold text-blue-600">
                Rp {{ number_format($creditRevenue, 0, ',', '.') }}
            </div>
        </div>

    </div>

    {{-- CONVERSION RATE --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <div class="bg-white p-5 rounded-xl shadow">
            <div class="text-sm text-gray-500">Lead → Deal</div>
            <div class="text-2xl font-bold text-blue-600">
                {{ $conversion['lead_to_deal'] }}%
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow">
            <div class="text-sm text-gray-500">Deal → Won</div>
            <div class="text-2xl font-bold text-green-600">
                {{ $conversion['deal_to_won'] }}%
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow">
            <div class="text-sm text-gray-500">Lead → Won</div>
            <div class="text-2xl font-bold text-purple-600">
                {{ $conversion['lead_to_won'] }}%
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow">
            <div class="text-sm text-gray-500">Won → Customer</div>
            <div class="text-2xl font-bold text-orange-600">
                {{ $conversion['won_to_customer'] }}%
            </div>
        </div>

    </div>

    {{-- FUNNEL CHART --}}
    <div class="bg-white p-5 rounded-xl shadow">
        <h2 class="font-semibold mb-4">CRM Funnel</h2>

        <canvas id="funnelChart" height="100"></canvas>
    </div>

    {{-- LATEST DATA --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- LEADS --}}
        <div class="bg-white p-5 rounded-xl shadow">
            <h2 class="font-semibold mb-3">Latest Leads</h2>

            <div class="space-y-2">
                @foreach($latestLeads as $lead)
                    <div class="text-sm border-b pb-2">
                        <div class="font-medium">{{ $lead->name }}</div>
                        <div class="text-gray-500 text-xs">{{ $lead->status }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- TRANSACTIONS --}}
        <div class="bg-white p-5 rounded-xl shadow">
            <h2 class="font-semibold mb-3">Latest Transactions</h2>

            <div class="space-y-2">
                @foreach($latestTransactions as $trx)
                    <div class="text-sm border-b pb-2">
                        <div class="font-medium">
                            {{ $trx->lead->name ?? '-' }}
                        </div>
                        <div class="text-gray-500 text-xs">
                            {{ $trx->type }} • Rp {{ number_format($trx->amount, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- CUSTOMERS --}}
        <div class="bg-white p-5 rounded-xl shadow">
            <h2 class="font-semibold mb-3">Latest Customers</h2>

            <div class="space-y-2">
                @foreach($latestCustomers as $customer)
                    <div class="text-sm border-b pb-2">
                        <div class="font-medium">{{ $customer->name }}</div>
                        <div class="text-gray-500 text-xs">
                            {{ $customer->type }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>

{{-- CHART JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('funnelChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Leads', 'Deal', 'Won', 'Customers'],
        datasets: [{
            data: [
                {{ $funnel['leads'] }},
                {{ $funnel['deal'] }},
                {{ $funnel['won'] }},
                {{ $funnel['customers'] }}
            ],
            backgroundColor: ['#3b82f6', '#f59e0b', '#22c55e', '#8b5cf6'],
            borderRadius: 8
        }]
    },
    options: {
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

@endsection