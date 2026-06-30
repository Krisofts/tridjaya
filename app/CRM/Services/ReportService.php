<?php

namespace App\CRM\Services;

use App\CRM\Models\CrmLead;
use App\CRM\Models\CrmLeadActivity;
use Illuminate\Support\Collection;

class ReportService
{
    // -------------------------------------------------------------------------
    // LAPORAN LEAD
    // -------------------------------------------------------------------------

    public function leadReport(array $filters): array
    {
        $query = CrmLead::query()
            ->with(['pipeline', 'stage', 'source', 'assignedUser', 'lostReason'])
            ->whereBetween('created_at', [
                $filters['date_from'] . ' 00:00:00',
                $filters['date_to']   . ' 23:59:59',
            ])
            ->when($filters['pipeline_id'], fn ($q) => $q->where('pipeline_id', $filters['pipeline_id']))
            ->when($filters['source_id'],   fn ($q) => $q->where('source_id',   $filters['source_id']))
            ->when($filters['assigned_to'], fn ($q) => $q->where('assigned_to', $filters['assigned_to']))
            ->when($filters['status'],      fn ($q) => $q->where('status',      $filters['status']))
            ->orderByDesc('created_at');

        $leads = $query->get();

        // Summary stats
        $summary = [
            'total'          => $leads->count(),
            'open'           => $leads->where('status', 'open')->count(),
            'won'            => $leads->where('status', 'won')->count(),
            'lost'           => $leads->where('status', 'lost')->count(),
            'conversion'     => $leads->count() > 0
                ? round(($leads->where('status', 'won')->count() / $leads->count()) * 100, 1)
                : 0,
            'pipeline_value' => $leads->where('status', 'open')->sum('estimated_value'),
            'won_value'      => $leads->where('status', 'won')->sum('estimated_value'),
        ];

        // Per pipeline breakdown
        $byPipeline = $leads->groupBy('pipeline_id')->map(function ($group) {
            $pipeline = $group->first()->pipeline;
            return [
                'name'  => $pipeline?->name ?? '—',
                'total' => $group->count(),
                'won'   => $group->where('status', 'won')->count(),
                'lost'  => $group->where('status', 'lost')->count(),
                'open'  => $group->where('status', 'open')->count(),
            ];
        })->values();

        // Per source breakdown
        $bySource = $leads->groupBy('source_id')->map(function ($group) {
            $source = $group->first()->source;
            return [
                'name'  => $source?->name ?? 'Tidak diketahui',
                'total' => $group->count(),
                'won'   => $group->where('status', 'won')->count(),
            ];
        })->values()->sortByDesc('total');

        return compact('leads', 'summary', 'byPipeline', 'bySource');
    }

    // -------------------------------------------------------------------------
    // LAPORAN PERFORMA SALES
    // -------------------------------------------------------------------------

    public function salesPerformanceReport(array $filters): array
    {
        $query = CrmLead::query()
            ->with('assignedUser')
            ->whereBetween('created_at', [
                $filters['date_from'] . ' 00:00:00',
                $filters['date_to']   . ' 23:59:59',
            ])
            ->when($filters['pipeline_id'], fn ($q) => $q->where('pipeline_id', $filters['pipeline_id']))
            ->when($filters['assigned_to'], fn ($q) => $q->where('assigned_to', $filters['assigned_to']));

        $leads = $query->get();

        // Group per sales
        $bySales = $leads->groupBy('assigned_to')->map(function ($group) {
            $user  = $group->first()->assignedUser;
            $total = $group->count();
            $won   = $group->where('status', 'won')->count();
            $lost  = $group->where('status', 'lost')->count();

            return [
                'name'       => $user?->name ?? '—',
                'total'      => $total,
                'open'       => $group->where('status', 'open')->count(),
                'won'        => $won,
                'lost'       => $lost,
                'conv_rate'  => $total > 0 ? round(($won / $total) * 100, 1) : 0,
                'won_value'  => $group->where('status', 'won')->sum('estimated_value'),
                'activities' => 0, // diisi di bawah
            ];
        })->sortByDesc('won')->values();

        // Hitung aktivitas per sales
        $activityCounts = CrmLeadActivity::query()
            ->whereBetween('activity_at', [
                $filters['date_from'] . ' 00:00:00',
                $filters['date_to']   . ' 23:59:59',
            ])
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $bySales = $bySales->map(function ($row) use ($leads, $activityCounts) {
            $userId = $leads->where('assignedUser.name', $row['name'])->first()?->assigned_to;
            $row['activities'] = $activityCounts[$userId] ?? 0;
            return $row;
        });

        $summary = [
            'total_leads'    => $leads->count(),
            'total_won'      => $leads->where('status', 'won')->count(),
            'total_lost'     => $leads->where('status', 'lost')->count(),
            'total_value'    => $leads->where('status', 'won')->sum('estimated_value'),
            'avg_conv'       => $bySales->avg('conv_rate'),
            'top_sales'      => $bySales->first()['name'] ?? '—',
        ];

        return compact('bySales', 'summary');
    }

    // -------------------------------------------------------------------------
    // LAPORAN AKTIVITAS
    // -------------------------------------------------------------------------

    public function activityReport(array $filters): array
    {
        $query = CrmLeadActivity::query()
            ->with(['lead', 'type', 'result', 'user'])
            ->whereBetween('activity_at', [
                $filters['date_from'] . ' 00:00:00',
                $filters['date_to']   . ' 23:59:59',
            ])
            ->when($filters['assigned_to'], fn ($q) => $q->where('user_id', $filters['assigned_to']))
            ->whereHas('type', fn ($q) => $q->where('slug', '!=', 'sistem'))
            ->orderByDesc('activity_at');

        $activities = $query->get();

        // Per type breakdown
        $byType = $activities->groupBy('activity_type_id')->map(function ($group) {
            $type    = $group->first()->type;
            $success = $group->filter(fn ($a) => $a->result?->is_success)->count();

            return [
                'name'         => $type?->name ?? '—',
                'total'        => $group->count(),
                'success'      => $success,
                'contacted'    => $group->where('is_contacted', true)->count(),
                'success_rate' => $group->count() > 0
                    ? round(($success / $group->count()) * 100, 1)
                    : 0,
            ];
        })->values()->sortByDesc('total');

        // Per sales breakdown
        $bySales = $activities->groupBy('user_id')->map(function ($group) {
            $user    = $group->first()->user;
            $success = $group->filter(fn ($a) => $a->result?->is_success)->count();

            return [
                'name'      => $user?->name ?? '—',
                'total'     => $group->count(),
                'success'   => $success,
                'contacted' => $group->where('is_contacted', true)->count(),
            ];
        })->values()->sortByDesc('total');

        $summary = [
            'total'          => $activities->count(),
            'total_success'  => $activities->filter(fn ($a) => $a->result?->is_success)->count(),
            'total_contacted'=> $activities->where('is_contacted', true)->count(),
            'success_rate'   => $activities->count() > 0
                ? round(($activities->filter(fn ($a) => $a->result?->is_success)->count() / $activities->count()) * 100, 1)
                : 0,
        ];

        return compact('activities', 'summary', 'byType', 'bySales');
    }
}