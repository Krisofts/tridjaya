<?php

namespace App\CRM\Controllers;

use App\CRM\Exports\LeadReportExport;
use App\CRM\Models\CrmLead;
use App\CRM\Models\CrmPipelineStage;
use App\Http\Controllers\Controller;
use App\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class LeadReportController extends Controller
{
    // -------------------------------------------------------------------------
    // INDEX
    // -------------------------------------------------------------------------

    public function index(Request $request): View
    {
        $dateFrom = $request->string('date_from')->toString() ?: now()->startOfMonth()->format('Y-m-d');
        $dateTo   = $request->string('date_to')->toString()   ?: now()->format('Y-m-d');
        $userId   = $request->integer('user_id') ?: null;

        $report = $this->buildReport($dateFrom, $dateTo, $userId);
        $users  = User::orderBy('name')->pluck('name', 'id');

        return view('crm.reports.leads', compact('report', 'users', 'dateFrom', 'dateTo', 'userId'));
    }

    // -------------------------------------------------------------------------
    // EXPORT
    // -------------------------------------------------------------------------

    public function export(Request $request)
    {
        $dateFrom = $request->string('date_from')->toString() ?: now()->startOfMonth()->format('Y-m-d');
        $dateTo   = $request->string('date_to')->toString()   ?: now()->format('Y-m-d');
        $userId   = $request->integer('user_id') ?: null;

        $filename = 'laporan-leads-' . $dateFrom . '-sd-' . $dateTo . '.xlsx';

        return Excel::download(
            new LeadReportExport($dateFrom, $dateTo, $userId),
            $filename,
        );
    }

    // -------------------------------------------------------------------------
    // BUILD REPORT DATA
    // -------------------------------------------------------------------------

    private function buildReport(string $dateFrom, string $dateTo, ?int $userId): array
    {
        $wonStageIds = CrmPipelineStage::where('is_won', true)->pluck('id');

        $users = User::when($userId, fn ($q) => $q->where('id', $userId))
            ->orderBy('name')
            ->get();

        $rows = $users->map(function (User $user) use ($dateFrom, $dateTo, $wonStageIds) {

            $base = CrmLead::where('assigned_to', $user->id);

            $todayLeads   = (clone $base)->whereDate('created_at', now()->format('Y-m-d'))->count();
            $periodLeads  = (clone $base)->whereDate('created_at', '>=', $dateFrom)->whereDate('created_at', '<=', $dateTo)->count();
            $totalLeads   = (clone $base)->count();
            $closedDeals  = (clone $base)->whereIn('pipeline_stage_id', $wonStageIds)->count();
            $closedPeriod = (clone $base)->whereIn('pipeline_stage_id', $wonStageIds)->whereDate('updated_at', '>=', $dateFrom)->whereDate('updated_at', '<=', $dateTo)->count();

            return [
                'user_id'       => $user->id,
                'name'          => $user->name,
                'today_leads'   => $todayLeads,
                'period_leads'  => $periodLeads,
                'total_leads'   => $totalLeads,
                'closed_deals'  => $closedDeals,
                'closed_period' => $closedPeriod,
                'close_rate'    => $totalLeads > 0
                    ? round(($closedDeals / $totalLeads) * 100, 1)
                    : 0,
            ];
        });

        return [
            'rows'         => $rows,
            'total_period' => $rows->sum('period_leads'),
            'total_all'    => $rows->sum('total_leads'),
            'total_closed' => $rows->sum('closed_deals'),
        ];
    }
}