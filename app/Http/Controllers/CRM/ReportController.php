<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\CRM\Services\LeadReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected LeadReportService $reportService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | USER PERFORMANCE REPORT
    |--------------------------------------------------------------------------
    */
    public function users(Request $request)
    {
        $filters = $this->filters($request);

        return view('crm.reports.users', [
            'users'   => $this->reportService->userPerformanceReport($filters),
            'filters' => $filters,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER HANDLER (SINGLE GROUP READY)
    |--------------------------------------------------------------------------
    */
    private function filters(Request $request): array
    {
        return [
            'search' => $request->string('search')->toString() ?: null,
            'from'   => $request->string('from')->toString() ?: null,
            'to'     => $request->string('to')->toString() ?: null,

            // 🔥 CHANGED: group_id → group (slug)
            'group'  => $request->string('group')->toString() ?: null,
        ];
    }
}