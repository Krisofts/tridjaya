@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Create Lead" :breadcrumbs="[
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'CRM Leads', 'url' => route('crm.leads.index')],
        ['label' => 'Create'],
    ]" />

    <form action="{{ route('crm.leads.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- CUSTOMER INFORMATION --}}
        <x-common.component-card title="Customer Information" desc="Basic information about the customer.">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                <x-form.input.input-with-label name="name" label="Name" required placeholder="Enter customer name" />

                <x-form.input.input-with-label name="phone" label="Phone" placeholder="Enter phone number" />

                <x-form.input.input-with-label name="email" label="Email" type="email"
                    placeholder="Enter email address" />

                <x-form.input.input-with-label name="interest" label="Interest" placeholder="TV, AC, Kulkas..."
                    hint="Products that interest the customer." />

                <div class="lg:col-span-2">
                    <x-form.textarea.textarea-with-label name="address" label="Address" rows="3"
                        placeholder="Enter customer address" />
                </div>

            </div>
        </x-common.component-card>

        {{-- LEAD INFORMATION --}}
        <x-common.component-card title="Lead Information" desc="CRM settings and lead management information.">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                <x-form.select.select-with-label name="lead_source_id" label="Lead Source" placeholder="Select Source"
                    :options="$sources->pluck('name', 'id')->toArray()" />

                <x-form.select.select-with-label name="pipeline_id" label="Pipeline" placeholder="Select Pipeline"
                    :options="$pipelines->pluck('name', 'id')->toArray()" required hint="Choose the initial pipeline stage for this lead." />

                <x-form.select.select-with-label name="assigned_to" label="Assigned To" placeholder="Unassigned"
                    :options="$users->pluck('name', 'id')->toArray()" />

                <div class="lg:col-span-2">
                    <x-form.textarea.textarea-with-label name="notes" label="Notes" rows="4"
                        placeholder="Additional notes about this lead" />
                </div>

            </div>
        </x-common.component-card>

        {{-- ACTIONS --}}
        <div class="flex items-center justify-end gap-3">

            <a href="{{ route('crm.leads.index') }}"
                class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                Cancel
            </a>

            <button type="submit"
                class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                Save Lead
            </button>

        </div>
    </form>
@endsection
