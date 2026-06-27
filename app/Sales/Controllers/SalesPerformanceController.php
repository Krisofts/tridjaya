<?php

namespace App\Sales\Controllers;

use App\Http\Controllers\Controller;
use App\Sales\Services\SalesPerformanceService;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SalesPerformanceController extends Controller
{
    private const KODE_JABATAN = 8;

    public function __construct(
        protected SalesPerformanceService $service,
    ) {}

    public function index(): View
    {
        return view('sales.performance.index', [
            'salesData'  => collect($this->service->getSalesPerformance(0, 0, self::KODE_JABATAN)),
            'cabangData' => collect($this->service->getBranchPerformance()),
            'fincoData'  => collect($this->service->getFincoPerformance()),
        ]);
    }

    public function show(string $slug): View
    {
        $salesData = collect(
            $this->service->getSalesPerformance(0, 0, self::KODE_JABATAN)
        );

        $sales = $salesData->first(
            fn ($row) => Str::slug($row['sales_name']) === $slug,
        );

        abort_if(! $sales, 404);

        $daily = collect(
            $this->service->getDailyPerformance($sales['sales_name'])
        );

        return view('sales.performance.show', compact('sales', 'daily'));
    }
}