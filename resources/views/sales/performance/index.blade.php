@extends('layouts.app')

@section('content')

<div class="mx-auto max-w-(--breakpoint-2xl) p-4 pb-20 md:p-6 md:pb-6">

    {{-- HEADER --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Sales Performance</h2>
            <p class="mt-0.5 text-sm text-gray-500">Rekap performa sales, cabang, dan finco — {{ now()->translatedFormat('F Y') }}</p>
        </div>
        <p class="flex items-center gap-1.5 text-xs text-gray-400">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Update: {{ now()->translatedFormat('d M Y, H:i') }}
        </p>
    </div>

    {{-- TABS --}}
    <div x-data="{ tab: 'sales' }" class="space-y-5">

        <div class="flex flex-wrap items-center gap-x-1 gap-y-2 rounded-xl bg-gray-100 p-1 dark:bg-gray-900">
            <button @click="tab='sales'"
                class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition"
                :class="tab==='sales'?'bg-white text-gray-900 shadow-sm dark:bg-gray-800 dark:text-white':'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white'">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Performa Sales
            </button>
            <button @click="tab='cabang'"
                class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition"
                :class="tab==='cabang'?'bg-white text-gray-900 shadow-sm dark:bg-gray-800 dark:text-white':'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white'">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Cabang
            </button>
            <button @click="tab='finco'"
                class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition"
                :class="tab==='finco'?'bg-white text-gray-900 shadow-sm dark:bg-gray-800 dark:text-white':'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white'">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Finco
            </button>
        </div>

        <div x-show="tab==='sales'" x-cloak>@include('sales.performance._sales-table')</div>
        <div x-show="tab==='cabang'" x-cloak>@include('sales.performance._cabang-table')</div>
        <div x-show="tab==='finco'" x-cloak>@include('sales.performance._finco-table')</div>

    </div>
</div>

@endsection