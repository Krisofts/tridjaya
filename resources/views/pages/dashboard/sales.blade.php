@extends('layouts.app')

@section('content')
<div class="grid grid-cols-12 gap-4 md:gap-6">

    {{-- Daily Sales — selalu paling atas --}}
    <div class="col-span-12">
        <x-dashboard.sales.daily-sales
            :sales="$sales"
            :sparkline="$sparkline"
        />
    </div>

    {{-- Target Bulanan + Target Harian Cabang --}}
    <div class="col-span-12 space-y-4 xl:col-span-5">
        <x-dashboard.sales.monthly-target
            :target="$monthlyTarget"
        />
        <x-dashboard.sales.daily-dealer-target
            :dealers="$dailyDealerTarget"
        />
    </div>

    {{-- Ranking Sales + Ranking Cabang --}}
    <div class="col-span-12 space-y-4 xl:col-span-7">
        <x-dashboard.sales.sales-ranking
            :all-rankings="$allSalesRanking"
        />
        <x-dashboard.sales.branch-ranking
            :branches="$branchRanking"
        />
    </div>

</div>
@endsection