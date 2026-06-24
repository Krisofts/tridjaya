<?php

namespace App\Dashboard\Sales\Controllers;

use App\Dashboard\Sales\Services\SalesService;
use App\Http\Controllers\Controller;

class SalesController extends Controller
{
    private const KODE_JABATAN_SALES = 8;

    public function __construct(
        private readonly SalesService $salesService
    ) {}

    public function index()
    {
        return view('pages.dashboard.sales', [
            'sales'             => $this->salesService->getTodaySale(),
            'sparkline'         => $this->salesService->getSparkline(),
            'monthlyTarget'     => $this->salesService->getMonthlyRevenueTarget(),
            'allSalesRanking'   => $this->salesService->getAllSalesRanking(self::KODE_JABATAN_SALES),
            'branchRanking'     => $this->salesService->getBranchRanking(),
            'dailyDealerTarget' => $this->salesService->getDailyDealerTarget(),
        ]);
    }
}