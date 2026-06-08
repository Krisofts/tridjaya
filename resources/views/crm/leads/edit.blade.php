@extends('layouts.app')

@section('title', 'Edit Lead')

@section('content')

<div class="max-w-3xl p-6">

    <h1 class="text-2xl font-bold mb-6">
        Edit Lead
    </h1>

    <form method="POST"
          action="{{ route('crm.leads.update', $lead) }}">

        @csrf
        @method('PUT')

        @include('crm.leads.partials.form')

        <div class="mt-6 flex gap-3">

            <button
                type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
            >
                Update Lead
            </button>

            <a href="{{ route('crm.leads.show', $lead) }}"
               class="px-4 py-2 border rounded hover:bg-gray-50">
                Cancel
            </a>

        </div>

    </form>

</div>

@endsection