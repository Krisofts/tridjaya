@extends('layouts.app')

@section('title', 'Create Lead')

@section('content')

<x-common.page-breadcrumb pageTitle="Create Lead" />

<div class="max-w-3xl mx-auto">

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

        {{-- HEADER --}}
        <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-800">

            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                Create Lead
            </h2>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Add a new prospect to your CRM pipeline.
            </p>

        </div>

        {{-- FORM --}}
        <form method="POST" action="{{ route('crm.leads.store') }}">

            @csrf

            <div class="p-6 space-y-6">

                @include('crm.leads.partials.form')

            </div>

            {{-- FOOTER ACTION --}}
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4 dark:border-gray-800">

                {{-- CANCEL --}}
                <a href="{{ route('crm.leads.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-300
                          bg-white px-4 py-2.5 text-sm font-medium text-gray-700
                          hover:bg-gray-50 transition
                          dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300
                          dark:hover:bg-gray-700">

                    Cancel

                </a>

                {{-- SUBMIT --}}
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg
                               bg-brand-500 px-4 py-2.5 text-sm font-medium text-white
                               shadow-theme-xs hover:bg-brand-600 transition
                               focus:ring-4 focus:ring-brand-500/20">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="20"
                         height="20"
                         viewBox="0 0 20 20"
                         fill="none">

                        <path d="M16.667 10V14.1667C16.667 15.0871 15.9208 15.8333 15.0003 15.8333H5.00033C4.07985 15.8333 3.33366 15.0871 3.33366 14.1667V5.83333C3.33366 4.91286 4.07985 4.16667 5.00033 4.16667H11.667"
                              stroke="currentColor"
                              stroke-width="1.5"
                              stroke-linecap="round"
                              stroke-linejoin="round" />

                        <path d="M15 2.5V7.5M17.5 5H12.5"
                              stroke="currentColor"
                              stroke-width="1.5"
                              stroke-linecap="round"
                              stroke-linejoin="round" />

                    </svg>

                    Save Lead

                </button>

            </div>

        </form>

    </div>

</div>

@endsection