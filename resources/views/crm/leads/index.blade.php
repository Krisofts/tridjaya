@extends('layouts.app')

@section('title', 'Leads')

@section('content')

<div class="p-6">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Leads</h1>
            <p class="text-sm text-gray-500">
                Manage customer prospects
            </p>
        </div>

        <a href="{{ route('crm.leads.create') }}"
           class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
            New Lead
        </a>
    </div>

    {{-- FILTER --}}
    <form method="GET" class="mb-4 grid md:grid-cols-5 gap-3">

        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search name or phone"
            class="border rounded px-3 py-2"
        >

        <select name="status" class="border rounded px-3 py-2">
            <option value="">All Status</option>
            @foreach(App\CRM\Models\Lead::statuses() as $value => $label)
                <option value="{{ $value }}" @selected(request('status') == $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        <select name="source" class="border rounded px-3 py-2">
            <option value="">All Sources</option>
            @foreach(App\CRM\Models\Lead::sources() as $value => $label)
                <option value="{{ $value }}" @selected(request('source') == $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        <select name="interest" class="border rounded px-3 py-2">
            <option value="">All Interests</option>
            @foreach(App\CRM\Models\Lead::interests() as $value => $label)
                <option value="{{ $value }}" @selected(request('interest') == $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        <button class="bg-gray-900 text-white rounded px-4 hover:bg-black">
            Filter
        </button>

    </form>

    {{-- TABLE --}}
    <div class="bg-white rounded shadow overflow-hidden">

        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Phone</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Interest</th>
                    <th class="p-3 text-left">Assigned</th>
                    <th class="p-3 text-right">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($leads as $lead)
                    <tr class="border-t hover:bg-gray-50">

                        <td class="p-3 font-medium">
                            {{ $lead->name }}
                        </td>

                        <td class="p-3">
                            {{ $lead->phone }}
                        </td>

                        <td class="p-3">
                            <span class="px-2 py-1 text-xs rounded bg-gray-100">
                                {{ $lead->status_label }}
                            </span>
                        </td>

                        <td class="p-3">
                            {{ $lead->interest_label }}
                        </td>

                        <td class="p-3">
                            {{ $lead->assignedTo?->name ?? '-' }}
                        </td>

                        <td class="p-3 text-right space-x-2">

                            <a href="{{ route('crm.leads.show', $lead) }}"
                               class="text-blue-600 hover:underline">
                                View
                            </a>

                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-gray-500">
                            No leads found.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>

    {{-- PAGINATION --}}
    <div class="mt-4">
        {{ $leads->links() }}
    </div>

</div>

@endsection