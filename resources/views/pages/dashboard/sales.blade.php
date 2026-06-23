@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-12 gap-4 md:gap-6">
        <div class="col-span-12 space-y-6 xl:col-span-7">
            <x-dashboard.sales.daily-sales :sales="$sales" />
        </div>

        {{-- Monthly Revenue Target --}}
        <div class="col-span-12 xl:col-span-5">
            <x-dashboard.sales.monthly-target :target="$monthlyTarget" />
        </div>

    </div>
@endsection
