<?php

namespace App\CRM\Services;

use App\User\Models\User;
use App\CRM\Models\Lead;
use App\CRM\Models\Customer;
use Carbon\Carbon;

class LeadReportService
{
    /*
    |--------------------------------------------------------------------------
    | BASE LEAD QUERY (REUSABLE)
    |--------------------------------------------------------------------------
    */
    private function leadBaseQuery(array $filters = [])
    {
        $query = Lead::query();

        // DATE FILTER
        if (!empty($filters['from']) && !empty($filters['to'])) {
            $from = Carbon::parse($filters['from'])->startOfDay();
            $to   = Carbon::parse($filters['to'])->endOfDay();

            $query->whereBetween('created_at', [$from, $to]);
        }

        // GROUP FILTER (SINGLE GROUP - SLUG)
        if (!empty($filters['group'])) {
            $query->whereHas('user', function ($q) use ($filters) {
                $q->inGroup($filters['group']);
            });
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | USER PERFORMANCE REPORT
    |--------------------------------------------------------------------------
    */
    public function userPerformanceReport(array $filters = [])
    {
        $today = Carbon::today();

        return User::query()

            // SEARCH USER
            ->when(!empty($filters['search']), function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%');
            })

            // GROUP FILTER (SINGLE GROUP)
            ->when(!empty($filters['group']), function ($q) use ($filters) {
                $q->inGroup($filters['group']);
            })

            ->withCount([

                // TOTAL LEADS
                'leads as total_leads' => function ($q) use ($filters) {
                    $this->applyDateFilter($q, $filters);
                },

                // WON
                'leads as total_won' => function ($q) use ($filters) {
                    $q->where('status', 'won');
                    $this->applyDateFilter($q, $filters);
                },

                // DEAL
                'leads as total_deal' => function ($q) use ($filters) {
                    $q->where('status', 'deal');
                    $this->applyDateFilter($q, $filters);
                },

                // TODAY
                'leads as leads_today' => function ($q) use ($today) {
                    $q->whereDate('created_at', $today);
                },
            ])

            ->orderByDesc('total_leads')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | FUNNEL SUMMARY
    |--------------------------------------------------------------------------
    */
    public function funnelSummary(array $filters = []): array
    {
        $query = $this->leadBaseQuery($filters);
        $base = clone $query;

        return [
            'leads'     => $base->count(),
            'deal'      => (clone $query)->where('status', 'deal')->count(),
            'won'       => (clone $query)->where('status', 'won')->count(),
            'lost'      => (clone $query)->where('status', 'lost')->count(),
            'customers' => $this->customerCount($filters),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CONVERSION RATE
    |--------------------------------------------------------------------------
    */
    public function conversionRate(array $filters = []): array
    {
        $baseQuery = $this->leadBaseQuery($filters);

        $leads = (clone $baseQuery)->count();
        $deal  = (clone $baseQuery)->where('status', 'deal')->count();
        $won   = (clone $baseQuery)->where('status', 'won')->count();

        $customers = $this->customerCount($filters);

        return [
            'lead_to_deal'    => $this->percent($deal, $leads),
            'deal_to_won'     => $this->percent($deal > 0 ? $won : 0, $deal),
            'lead_to_won'     => $this->percent($won, $leads),
            'won_to_customer' => $this->percent($customers, max($won, 1)),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CUSTOMER COUNT
    |--------------------------------------------------------------------------
    */
    private function customerCount(array $filters = []): int
    {
        $query = Customer::query();

        // DATE FILTER
        if (!empty($filters['from']) && !empty($filters['to'])) {
            $from = Carbon::parse($filters['from'])->startOfDay();
            $to   = Carbon::parse($filters['to'])->endOfDay();

            $query->whereBetween('created_at', [$from, $to]);
        }

        // GROUP FILTER (SINGLE GROUP)
        if (!empty($filters['group'])) {
            $query->whereHas('user', function ($q) use ($filters) {
                $q->inGroup($filters['group']);
            });
        }

        return $query->count();
    }

    /*
    |--------------------------------------------------------------------------
    | DATE FILTER HELPER
    |--------------------------------------------------------------------------
    */
    private function applyDateFilter($query, array $filters)
    {
        if (!empty($filters['from']) && !empty($filters['to'])) {
            $from = Carbon::parse($filters['from'])->startOfDay();
            $to   = Carbon::parse($filters['to'])->endOfDay();

            $query->whereBetween('created_at', [$from, $to]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PERCENT HELPER
    |--------------------------------------------------------------------------
    */
    private function percent($value, $total): float
    {
        if ($total <= 0) {
            return 0;
        }

        return round(($value / $total) * 100, 2);
    }
}