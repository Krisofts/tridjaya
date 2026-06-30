<?php

namespace App\CRM\Controllers;

use App\CRM\Exports\LeadReportExport;
use App\CRM\Exports\SalesPerformanceExport;
use App\CRM\Exports\ActivityReportExport;
use App\CRM\Services\ReportService;
use App\Http\Controllers\Controller;
use App\User\Models\User;
use App\CRM\Models\CrmPipeline;
use App\CRM\Models\CrmSource;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller 
{
    public function __construct(
        private readonly ReportService $service,
    ) {}
 
    // -------------------------------------------------------------------------
    // LAPORAN LEAD
    // -------------------------------------------------------------------------

    public function leads(Request $request): View
    {
        $filters = $this->resolveFilters($request);
        $data    = null;

        if ($request->filled('date_from')) {
            $data = $this->service->leadReport($filters);
        }

        $pipelines = CrmPipeline::active()->orderBy('name')->get();
        $sources   = CrmSource::active()->ordered()->get();
        $users     = User::orderBy('name')->get();

        return view('pages.crm.reports.leads', compact(
            'data', 'filters', 'pipelines', 'sources', 'users'
        ));
    }

    public function leadsExport(Request $request)
    {
        $filters  = $this->resolveFilters($request);
        $filename = 'laporan-lead-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new LeadReportExport($filters), $filename);
    }

    // -------------------------------------------------------------------------
    // LAPORAN PERFORMA SALES
    // -------------------------------------------------------------------------

    public function salesPerformance(Request $request): View
    {
        $filters = $this->resolveFilters($request);
        $data    = null;

        if ($request->filled('date_from')) {
            $data = $this->service->salesPerformanceReport($filters);
        }

        $pipelines = CrmPipeline::active()->orderBy('name')->get();
        $users     = User::orderBy('name')->get();

        return view('pages.crm.reports.sales-performance', compact(
            'data', 'filters', 'pipelines', 'users'
        ));
    }

    public function salesPerformanceExport(Request $request)
    {
        $filters  = $this->resolveFilters($request);
        $filename = 'performa-sales-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new SalesPerformanceExport($filters), $filename);
    }

    // -------------------------------------------------------------------------
    // LAPORAN AKTIVITAS
    // -------------------------------------------------------------------------

    public function activities(Request $request): View
    {
        $filters = $this->resolveFilters($request);
        $data    = null;

        if ($request->filled('date_from')) {
            $data = $this->service->activityReport($filters);
        }

        $users = User::orderBy('name')->get();

        return view('pages.crm.reports.activities', compact(
            'data', 'filters', 'users'
        ));
    }

    public function activitiesExport(Request $request)
    {
        $filters  = $this->resolveFilters($request);
        $filename = 'laporan-aktivitas-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new ActivityReportExport($filters), $filename);
    }

    // -------------------------------------------------------------------------
    // PRIVATE
    // -------------------------------------------------------------------------

    private function resolveFilters(Request $request): array
    {
        return [
            'date_from'   => $request->get('date_from', now()->startOfMonth()->format('Y-m-d')),
            'date_to'     => $request->get('date_to',   now()->format('Y-m-d')),
            'pipeline_id' => $request->get('pipeline_id'),
            'source_id'   => $request->get('source_id'),
            'assigned_to' => $request->get('assigned_to'),
            'status'      => $request->get('status'),
        ];
    }
}