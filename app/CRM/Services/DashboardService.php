<?php

namespace App\CRM\Services;

use App\CRM\Models\CrmLead;
use App\CRM\Models\CrmLeadActivity;
use App\CRM\Models\CrmPipeline;
use App\CRM\Models\CrmTask;
use App\User\Models\User;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    // -------------------------------------------------------------------------
    // SALES VIEW
    // -------------------------------------------------------------------------

    public function salesStats(User $user): array
    {
        return CrmCacheService::rememberStats(
            CrmCacheService::keyDashboardSalesStats($user->id),
            function () use ($user) {
                $myLeads = CrmLead::query()->where('assigned_to', $user->id);

                return [
                    'leads_open'       => (int)(clone $myLeads)->where('status', 'open')->count(),
                    'leads_won'        => (int)(clone $myLeads)->where('status', 'won')->count(),
                    'leads_lost'       => (int)(clone $myLeads)->where('status', 'lost')->count(),
                    'leads_total'      => (int)(clone $myLeads)->count(),
                    'leads_today'      => (int)(clone $myLeads)->whereDate('created_at', today())->count(),
                    'pipeline_value'   => (float)(clone $myLeads)->where('status', 'open')->sum('estimated_value'),
                    'tasks_today'      => (int)CrmTask::assignedTo($user->id)->dueToday()->count(),
                    'tasks_overdue'    => (int)CrmTask::assignedTo($user->id)->overdue()->count(),
                    'tasks_open'       => (int)CrmTask::assignedTo($user->id)->open()->count(),
                    'followup_overdue' => (int)(clone $myLeads)
                        ->where('status', 'open')
                        ->whereNotNull('next_follow_up_at')
                        ->where('next_follow_up_at', '<', now())
                        ->count(),
                ];
            }
        );
    }

    public function salesTodayLeads(User $user, int $limit = 10)
    {
        return CrmLead::query()
            ->with(['pipeline', 'stage', 'source'])
            ->where('assigned_to', $user->id)
            ->whereDate('created_at', today())
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function salesRecentActivities(User $user, int $limit = 8)
    {
        return CrmLeadActivity::query()
            ->with(['lead', 'type', 'result'])
            ->where('user_id', $user->id)
            ->orderByDesc('activity_at')
            ->limit($limit)
            ->get();
    }

    public function salesTodayTasks(User $user, int $limit = 10)
    {
        return CrmTask::query()
            ->with('lead')
            ->assignedTo($user->id)
            ->open()
            ->orderByRaw("FIELD(priority,'high','medium','low')")
            ->orderBy('due_at')
            ->limit($limit)
            ->get();
    }

    public function salesMyLeads(User $user, int $limit = 8)
    {
        return CrmLead::query()
            ->with(['pipeline', 'stage', 'source'])
            ->where('assigned_to', $user->id)
            ->where('status', 'open')
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get();
    }

    // -------------------------------------------------------------------------
    // MANAGER VIEW
    // -------------------------------------------------------------------------

    public function managerStats(?int $branchId = null): array
    {
        return CrmCacheService::rememberStats(
            CrmCacheService::keyDashboardManagerStats($branchId),
            function () use ($branchId) {
                $base = CrmLead::query()->when($branchId, fn ($q) => $q->where('branch_id', $branchId));

                $today     = (clone $base)->whereDate('created_at', today());
                $thisMonth = (clone $base)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                $lastMonth = (clone $base)->whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year);

                $wonThisMonth   = (int)(clone $thisMonth)->where('status', 'won')->count();
                $wonLastMonth   = (int)(clone $lastMonth)->where('status', 'won')->count();
                $totalThisMonth = (int)(clone $thisMonth)->count();

                return [
                    'leads_today'        => (int)(clone $today)->count(),
                    'leads_won_today'    => (int)(clone $today)->where('status', 'won')->count(),
                    'leads_open'         => (int)(clone $base)->where('status', 'open')->count(),
                    'leads_won_month'    => $wonThisMonth,
                    'leads_lost_month'   => (int)(clone $thisMonth)->where('status', 'lost')->count(),
                    'leads_new_month'    => $totalThisMonth,
                    'pipeline_value'     => (float)(clone $base)->where('status', 'open')->sum('estimated_value'),
                    'won_value_month'    => (float)(clone $thisMonth)->where('status', 'won')->sum('estimated_value'),
                    'conversion_rate'    => $totalThisMonth > 0
                        ? (float)round(($wonThisMonth / $totalThisMonth) * 100, 1)
                        : 0.0,
                    'won_vs_last_month'  => $wonLastMonth > 0
                        ? (float)round((($wonThisMonth - $wonLastMonth) / $wonLastMonth) * 100, 1)
                        : null,
                    'tasks_overdue_team' => (int)CrmTask::overdue()->count(),
                ];
            }
        );
    }

    public function pipelineSummary(?int $branchId = null): array
    {
        return CrmCacheService::rememberStats(
            CrmCacheService::keyDashboardPipeline($branchId),
            function () use ($branchId) {
                $result = [];

                $pipelines = CrmPipeline::query()
                    ->with(['stages' => fn ($q) => $q->orderBy('sort_order')])
                    ->where('is_active', true)
                    ->get();

                foreach ($pipelines as $pipeline) {
                    $stageData = [];

                    foreach ($pipeline->stages as $stage) {
                        $isLostStage = strtolower($stage->name) === 'lost';

                        $count = CrmLead::query()
                            ->where('pipeline_id', $pipeline->id)
                            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                            ->when(
                                $isLostStage,
                                fn ($q) => $q->where('status', 'lost'),
                                fn ($q) => $q->where('stage_id', $stage->id)->where('status', 'open')
                            )
                            ->count();

                        // Plain array — aman untuk Redis serialize
                        $stageData[] = [
                            'name'  => (string) $stage->name,
                            'count' => (int) $count,
                        ];
                    }

                    $totalOpen  = CrmLead::query()
                        ->where('pipeline_id', $pipeline->id)
                        ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                        ->where('status', 'open')
                        ->count();

                    $totalValue = CrmLead::query()
                        ->where('pipeline_id', $pipeline->id)
                        ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                        ->where('status', 'open')
                        ->sum('estimated_value');

                    // Plain array — aman untuk Redis serialize
                    $result[] = [
                        'pipeline'    => (string) $pipeline->name,
                        'total_open'  => (int) $totalOpen,
                        'total_value' => (float) $totalValue,
                        'stages'      => $stageData,
                    ];
                }

                return $result;
            }
        );
    }

    public function salesPerformance(?int $branchId = null, int $limit = 10): array
    {
        return CrmCacheService::rememberStats(
            'crm:dashboard:performance:' . ($branchId ?? 'all'),
            function () use ($branchId, $limit) {
                return User::query()
                    ->select('users.id', 'users.name')
                    ->join('crm_leads', 'crm_leads.assigned_to', '=', 'users.id')
                    ->when($branchId, fn ($q) => $q->where('crm_leads.branch_id', $branchId))
                    ->whereMonth('crm_leads.created_at', now()->month)
                    ->whereYear('crm_leads.created_at', now()->year)
                    ->groupBy('users.id', 'users.name')
                    ->selectRaw('
                        COUNT(*) as total_leads,
                        SUM(CASE WHEN crm_leads.status = "won" THEN 1 ELSE 0 END) as won,
                        SUM(CASE WHEN crm_leads.status = "lost" THEN 1 ELSE 0 END) as lost,
                        SUM(CASE WHEN crm_leads.status = "open" THEN 1 ELSE 0 END) as open,
                        SUM(CASE WHEN crm_leads.status = "won" THEN crm_leads.estimated_value ELSE 0 END) as won_value
                    ')
                    ->orderByDesc('won')
                    ->limit($limit)
                    ->get()
                    ->map(fn ($row) => [
                        'name'        => $row->name,
                        'total_leads' => $row->total_leads,
                        'won'         => $row->won,
                        'lost'        => $row->lost,
                        'open'        => $row->open,
                        'won_value'   => $row->won_value,
                        'conv_rate'   => $row->total_leads > 0
                            ? round(($row->won / $row->total_leads) * 100, 1)
                            : 0,
                    ])
                    ->toArray();
            }
        );
    }

    public function recentLeads(?int $branchId = null, int $limit = 8)
    {
        return CrmLead::query()
            ->with(['pipeline', 'stage', 'assignedUser', 'source'])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function todayLeads(?int $branchId = null, int $limit = 10)
    {
        return CrmLead::query()
            ->with(['pipeline', 'stage', 'assignedUser', 'source'])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereDate('created_at', today())
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function leadTrend(?int $branchId = null): array
    {
        return CrmCacheService::rememberStats(
            CrmCacheService::keyDashboardTrend($branchId),
            function () use ($branchId) {
                $result = [];

                foreach (range(5, 0) as $i) {
                    $date = now()->subMonths($i);
                    $base = CrmLead::query()
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->when($branchId, fn ($q) => $q->where('branch_id', $branchId));

                    // Plain scalar values — aman untuk Redis
                    $result[] = [
                        'month' => (string) $date->format('M Y'),
                        'total' => (int)(clone $base)->count(),
                        'won'   => (int)(clone $base)->where('status', 'won')->count(),
                        'lost'  => (int)(clone $base)->where('status', 'lost')->count(),
                    ];
                }

                return $result;
            }
        );
    }
}