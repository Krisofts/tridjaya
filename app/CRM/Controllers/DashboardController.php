<?php

namespace App\CRM\Controllers;

use App\Auth\Services\AuthorizationService;
use App\CRM\Services\DashboardService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // Group yang dianggap manager / bisa lihat data semua tim
    private const MANAGER_GROUPS = ['superadmin', 'owner', 'manager'];

    public function __construct(
        private readonly DashboardService    $dashboard,
        private readonly AuthorizationService $auth,
    ) {}

    public function index(): View
    {
        $user      = Auth::user();
        $isManager = $this->auth->inGroup($user, self::MANAGER_GROUPS);
        $branchId  = $user->branch_id; // null = superadmin/owner lihat semua

        if ($isManager) {
            return $this->managerView($branchId);
        }

        return $this->salesView($user);
    }

    // -------------------------------------------------------------------------

    private function managerView(?int $branchId): View
    {
        $stats       = $this->dashboard->managerStats($branchId);
        $pipelines   = $this->dashboard->pipelineSummary($branchId);
        $performance = $this->dashboard->salesPerformance($branchId);
        $recentLeads = $this->dashboard->recentLeads($branchId);
        $trend       = $this->dashboard->leadTrend($branchId);

        return view('pages.crm.dashboard.manager', compact(
            'stats', 'pipelines', 'performance', 'recentLeads', 'trend'
        ));
    }

    private function salesView($user): View
    {
        $stats      = $this->dashboard->salesStats($user);
        $tasks      = $this->dashboard->salesTodayTasks($user);
        $myLeads    = $this->dashboard->salesMyLeads($user);
        $activities = $this->dashboard->salesRecentActivities($user);

        return view('pages.crm.dashboard.sales', compact(
            'stats', 'tasks', 'myLeads', 'activities'
        ));
    }
}