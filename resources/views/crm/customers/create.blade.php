@extends('layouts.app')

@section('title', 'Create Customer')

@section('content')

<div class="p-6 max-w-3xl mx-auto space-y-6">

    <h1 class="text-2xl font-bold">Convert Lead to Customer</h1>

    <form method="POST" action="{{ route('crm.customers.store') }}"
          class="bg-white p-6 rounded-xl shadow space-y-4">

        @csrf

        <input type="hidden" name="lead_id" value="{{ $lead->id ?? '' }}">

        {{-- LEAD INFO --}}
        @if($lead)
            <div class="p-3 bg-gray-50 rounded">
                <div class="font-medium">{{ $lead->name }}</div>
                <div class="text-sm text-gray-500">{{ $lead->phone }}</div>
            </div>
        @endif

        {{-- STATUS --}}
        <div>
            <label class="text-sm font-medium">Customer Status</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('crm.leads.show', $lead) }}"
               class="px-4 py-2 bg-gray-200 rounded">
                Cancel
            </a>

            <button class="px-4 py-2 bg-blue-600 text-white rounded">
                Convert
            </button>
        </div>

    </form>

</div>

@endsection