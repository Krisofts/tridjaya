<?php

namespace App\Sales\Controllers;

use App\Http\Controllers\Controller;
use App\Sales\Services\SalesAnalyticsService;
use Illuminate\View\View;

class SalesAnalyticsController extends Controller
{
    public function __construct(
        protected SalesAnalyticsService $service,
    ) {}

    public function index(): View
    {
        $day    = (int) request('day',   now()->day);
        $month  = (int) request('month', now()->month);
        $year   = (int) request('year',  now()->year);

        $summary        = $this->service->getSummary($day, $month, $year);
        $monthlyRevenue = $this->service->getMonthlyChart($year);
        $topFinco       = $this->service->getTopFinco($day, $month, $year);
        $branchRanking  = $this->service->getBranchRanking($month, $year);
        $topProducts    = $this->service->getTopProducts($day, $month, $year);

        return view('sales.analytics.index', compact(
            'summary',
            'monthlyRevenue',
            'topFinco',
            'branchRanking',
            'topProducts',
            'day',
            'month',
            'year',
        ));
    }
}