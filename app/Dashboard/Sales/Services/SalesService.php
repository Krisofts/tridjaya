<?php

namespace App\Dashboard\Sales\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SalesService
{
    /**
     * Today vs Yesterday
     */
    public function getTodaySale(): array
    {
        return Cache::remember('dashboard.sales.today_summary', 60, function () {
            $result = DB::connection('sqlsrv')
                ->select("EXEC GetTodaySale");

            $data = (array) collect($result)->first();

            return [
                'transaction' => [
                    'today' => (int) ($data['transaction_count_today'] ?? 0),
                    'yesterday' => (int) ($data['transaction_count_yesterday'] ?? 0),
                    'growth_pct' => (float) ($data['transaction_count_growth_pct'] ?? 0),
                ],

                'unit' => [
                    'today' => (int) ($data['unit_sold_today'] ?? 0),
                    'yesterday' => (int) ($data['unit_sold_yesterday'] ?? 0),
                    'growth_pct' => (float) ($data['unit_sold_growth_pct'] ?? 0),
                ],

                'revenue' => [
                    'today' => (int) ($data['revenue_today'] ?? 0),
                    'yesterday' => (int) ($data['revenue_yesterday'] ?? 0),
                    'growth_pct' => (float) ($data['revenue_growth_pct'] ?? 0),
                ],

                'avg_transaction' => (int) ($data['avg_sales_per_transaction'] ?? 0),
            ];
        });
    }

    /**
     * Monthly Revenue Target
     */
    public function getMonthlyRevenueTarget(): array
    {
        return Cache::remember('dashboard.sales.monthly_target', 300, function () {

            $result = DB::connection('sqlsrv')
                ->select("EXEC GetMonthlyRevenueTargetSummary");

            $data = (array) collect($result)->first();

            return [
                'actual' => (int) ($data['revenue_actual'] ?? 0),
                'target' => (int) ($data['revenue_target'] ?? 0),

                'remaining_revenue' => (int) ($data['remaining_revenue'] ?? 0),
                'remaining_days' => (int) ($data['remaining_days'] ?? 0),

                'needed_per_day' => (int) ($data['needed_revenue_per_day'] ?? 0),

                'achievement_pct' => (float) ($data['achievement_pct'] ?? 0),

                'status' => $data['status'] ?? 'UNKNOWN',
            ];
        });
    }
}