<?php

namespace App\CRM\Services;

use App\CRM\Models\CrmLead;
use App\CRM\Models\CrmLeadActivity;
use App\CRM\Models\CrmPipeline;
use App\CRM\Models\CrmTask;
use App\User\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    // -------------------------------------------------------------------------
    // SALES VIEW — data milik sales yang login
    // -------------------------------------------------------------------------

    public function salesStats(User $user): array
    {
        $myLeads = CrmLead::query()->where('assigned_to', $user->id);

        return [
            // Lead stats
            'leads_open'     => (clone $myLeads)->where('status', 'open')->count(),
            'leads_won'      => (clone $myLeads)->where('status', 'won')->count(),
            'leads_lost'     => (clone $myLeads)->where('status', 'lost')->count(),
            'leads_total'    => (clone $myLeads)->count(),

            // Nilai pipeline
            'pipeline_value' => (clone $myLeads)->where('status', 'open')->sum('estimated_value'),

            // Task
            'tasks_today'    => CrmTask::assignedTo($user->id)->dueToday()->count(),
            'tasks_overdue'  => CrmTask::assignedTo($user->id)->overdue()->count(),
            'tasks_open'     => CrmTask::assignedTo($user->id)->open()->count(),

            // Follow-up terlambat
            'followup_overdue' => (clone $myLeads)
                ->where('status', 'open')
                ->whereNotNull('next_follow_up_at')
                ->where('next_follow_up_at', '<', now())
                ->count(),
        ];
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
    // MANAGER / OWNER VIEW — data semua tim
    // -------------------------------------------------------------------------

    public function managerStats(?int $branchId = null): array
    {
        $base = CrmLead::query()->when($branchId, fn ($q) => $q->where('branch_id', $branchId));

        // Bulan ini
        $thisMonth = (clone $base)->whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year);

        // Bulan lalu
        $lastMonth = (clone $base)->whereMonth('created_at', now()->subMonth()->month)
                                  ->whereYear('created_at', now()->subMonth()->year);

        $wonThisMonth  = (clone $thisMonth)->where('status', 'won')->count();
        $wonLastMonth  = (clone $lastMonth)->where('status', 'won')->count();
        $totalThisMonth = (clone $thisMonth)->count();

        return [
            // Overview
            'leads_open'          => (clone $base)->where('status', 'open')->count(),
            'leads_won_month'     => $wonThisMonth,
            'leads_lost_month'    => (clone $thisMonth)->where('status', 'lost')->count(),
            'leads_new_month'     => $totalThisMonth,

            // Nilai
            'pipeline_value'      => (clone $base)->where('status', 'open')->sum('estimated_value'),
            'won_value_month'     => (clone $thisMonth)->where('status', 'won')->sum('estimated_value'),

            // Konversi bulan ini
            'conversion_rate'     => $totalThisMonth > 0
                ? round(($wonThisMonth / $totalThisMonth) * 100, 1)
                : 0,

            // Trend vs bulan lalu
            'won_vs_last_month'   => $wonLastMonth > 0
                ? round((($wonThisMonth - $wonLastMonth) / $wonLastMonth) * 100, 1)
                : null,

            // Task overdue tim
            'tasks_overdue_team'  => CrmTask::overdue()->count(),
        ];
    }

    public function pipelineSummary(?int $branchId = null): array
    {
        return CrmPipeline::query()
            ->with(['stages' => fn ($q) => $q->orderBy('sort_order')])
            ->where('is_active', true)
            ->get()
            ->map(function ($pipeline) use ($branchId) {
                $base = CrmLead::query()
                    ->where('pipeline_id', $pipeline->id)
                    ->when($branchId, fn ($q) => $q->where('branch_id', $branchId));

                $stageData = $pipeline->stages->map(function ($stage) use ($pipeline, $branchId) {
                    // Stage bernama 'Lost' — hitung dari status=lost bukan stage_id
                    // karena saat markLost() lead tidak pindah stage, hanya status berubah
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

                    return [
                        'name'  => $stage->name,
                        'count' => $count,
                    ];
                });

                return [
                    'pipeline'    => $pipeline->name,
                    'total_open'  => (clone $base)->where('status', 'open')->count(),
                    'total_value' => (clone $base)->where('status', 'open')->sum('estimated_value'),
                    'stages'      => $stageData,
                ];
            })
            ->toArray();
    }

    public function salesPerformance(?int $branchId = null, int $limit = 10): array
    {
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

    public function recentLeads(?int $branchId = null, int $limit = 8)
    {
        return CrmLead::query()
            ->with(['pipeline', 'stage', 'assignedUser', 'source'])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function leadTrend(?int $branchId = null): array
    {
        $months = collect(range(5, 0))->map(function ($i) use ($branchId) {
            $date = now()->subMonths($i);

            $base = CrmLead::query()
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId));

            return [
                'month' => $date->format('M Y'),
                'total' => (clone $base)->count(),
                'won'   => (clone $base)->where('status', 'won')->count(),
                'lost'  => (clone $base)->where('status', 'lost')->count(),
            ];
        });

        return $months->toArray();
    }
}