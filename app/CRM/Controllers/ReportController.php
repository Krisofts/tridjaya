<?php

namespace App\CRM\Controllers;

use App\CRM\Exports\ActivityReportExport;
use App\CRM\Exports\LeadReportExport;
use App\CRM\Exports\SalesPerformanceExport;
use App\CRM\Models\CrmPipeline;
use App\CRM\Models\CrmSource;
use App\CRM\Services\ReportService;
use App\Http\Controllers\Controller;
use App\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $service,
    ) {}

    // -------------------------------------------------------------------------
    // INDEX — satu halaman dengan 3 tab
    // -------------------------------------------------------------------------

    public function index(Request $request): View
    {
        $tab     = $request->get('tab', 'leads'); // leads | sales | activities
        $filters = $this->resolveFilters($request);
        $data    = null;

        if ($request->filled('date_from')) {
            $data = match ($tab) {
                'sales'      => $this->service->salesPerformanceReport($filters),
                'activities' => $this->service->activityReport($filters),
                default      => $this->service->leadReport($filters),
            };
        }

        $pipelines = CrmPipeline::active()->orderBy('name')->get();
        $sources   = CrmSource::active()->ordered()->get();
        $users     = User::orderBy('name')->get();

        return view('pages.crm.reports.index', compact(
            'tab', 'data', 'filters', 'pipelines', 'sources', 'users'
        ));
    }

    // -------------------------------------------------------------------------
    // EXPORT
    // -------------------------------------------------------------------------

    public function export(Request $request)
    {
        $tab     = $request->get('tab', 'leads');
        $filters = $this->resolveFilters($request);

        return match ($tab) {
            'sales' => Excel::download(
                new SalesPerformanceExport($filters),
                'performa-sales-' . now()->format('Ymd-His') . '.xlsx'
            ),
            'activities' => Excel::download(
                new ActivityReportExport($filters),
                'laporan-aktivitas-' . now()->format('Ymd-His') . '.xlsx'
            ),
            default => Excel::download(
                new LeadReportExport($filters),
                'laporan-lead-' . now()->format('Ymd-His') . '.xlsx'
            ),
        };
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