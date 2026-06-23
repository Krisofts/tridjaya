<?php

namespace App\Dashboard\Sales\Controllers;

use App\Dashboard\Sales\Services\SalesService;
use App\Http\Controllers\Controller;

class SalesController extends Controller
{
    public function index(SalesService $salesService)
    {
        $sales = $salesService->getTodaySale();

        $monthlyTarget = $salesService->getMonthlyRevenueTarget();

        return view('pages.dashboard.sales', [
            'sales' => $sales,
            'monthlyTarget' => $monthlyTarget,
        ]);
    }
}