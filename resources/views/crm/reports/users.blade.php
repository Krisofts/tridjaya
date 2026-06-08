@extends('layouts.app')

@section('title', 'User Report')

@section('content')
<div class="p-6 space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-bold">User Performance Report</h1>
        <p class="text-sm text-gray-500">
            Detailed report of user leads, deal, won, and conversion activity
        </p>
    </div>

    {{-- FILTER --}}
    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 bg-white p-4 rounded-lg shadow">

        {{-- SEARCH --}}
        <input type="text"
               name="search"
               value="{{ $filters['search'] ?? '' }}"
               placeholder="Search user..."
               class="border rounded p-2 w-full">

        {{-- FROM --}}
        <input type="date"
               name="from"
               value="{{ $filters['from'] ?? '' }}"
               class="border rounded p-2 w-full">

        {{-- TO --}}
        <input type="date"
               name="to"
               value="{{ $filters['to'] ?? '' }}"
               class="border rounded p-2 w-full">

        {{-- GROUP (SAFE CONFIG HANDLING) --}}
        <select name="group" class="border rounded p-2 w-full">
            <option value="">All Groups</option>

            @foreach(config('auth_groups.groups') as $key => $group)
                <option value="{{ $key }}"
                    {{ ($filters['group'] ?? '') == $key ? 'selected' : '' }}>

                    {{ is_array($group) ? ($group['label'] ?? $key) : $group }}

                </option>
            @endforeach
        </select>

        {{-- ACTION --}}
        <div class="flex gap-2">
            <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">
                Apply
            </button>

            <a href="{{ url()->current() }}"
               class="bg-gray-200 px-4 py-2 rounded w-full text-center">
                Reset
            </a>
        </div>

    </form>

    {{-- TABLE --}}
    <div class="bg-white p-4 rounded shadow overflow-x-auto">

        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b bg-gray-50">
                    <th class="py-2">User</th>
                    <th>Total Leads</th>
                    <th>Deal</th>
                    <th>Won</th>
                    <th>Today</th>
                    <th>Performance</th>
                </tr>
            </thead>

            <tbody>
                @forelse($users as $user)
                    <tr class="border-b hover:bg-gray-50">

                        {{-- USER NAME --}}
                        <td class="py-2 font-medium">
                            {{ $user->name }}
                        </td>

                        {{-- TOTAL LEADS --}}
                        <td>{{ $user->total_leads }}</td>

                        {{-- DEAL --}}
                        <td class="text-blue-600">
                            {{ $user->total_deal }}
                        </td>

                        {{-- WON --}}
                        <td class="text-green-600">
                            {{ $user->total_won }}
                        </td>

                        {{-- TODAY --}}
                        <td class="text-gray-600">
                            {{ $user->leads_today }}
                        </td>

                        {{-- PERFORMANCE --}}
                        <td>
                            @php
                                $performance = $user->total_leads > 0
                                    ? ($user->total_won / $user->total_leads) * 100
                                    : 0;
                            @endphp

                            <span class="font-semibold">
                                {{ number_format($performance, 2) }}%
                            </span>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-6 text-gray-500">
                            No report data found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

</div>
@endsection