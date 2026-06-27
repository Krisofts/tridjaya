<?php

namespace App\Sales\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class SalesAnalyticsService
{
    private const TTL_SUMMARY = 300;
    private const TTL_CHART   = 300;
    private const TTL_FINCO   = 300;
    private const TTL_BRANCH  = 120;
    private const TTL_PRODUCT = 300;

    // =========================================================================
    // Cache Keys
    // =========================================================================

    private function summaryKey(int $month, int $year): string
    {
        return "sales.analytics.summary.{$year}.{$month}";
    }

    private function chartKey(int $year): string
    {
        return "sales.analytics.chart.{$year}";
    }

    private function fincoKey(int $day, int $month, int $year): string
    {
        return "sales.analytics.finco.{$year}.{$month}.{$day}";
    }

    private function branchKey(int $month, int $year): string
    {
        return "sales.analytics.branch.{$year}.{$month}";
    }

    private function productKey(int $day, int $month, int $year): string
    {
        return "sales.analytics.product.{$year}.{$month}.{$day}";
    }

    // =========================================================================
    // Public API
    // =========================================================================

    /**
     * Summary KPI bulan berjalan vs bulan lalu.
     * SP: GetSalesAnalyticsSummary @Day, @Month, @Year
     */
    public function getSummary(int $day = 0, int $month = 0, int $year = 0): array
    {
        if ($day   === 0) $day   = (int) now()->format('d');
        if ($month === 0) $month = (int) now()->format('m');
        if ($year  === 0) $year  = (int) now()->format('Y');

        return Cache::remember(
            $this->summaryKey($month, $year),
            self::TTL_SUMMARY,
            fn() => $this->fetchSummary($day, $month, $year),
        );
    }

    /**
     * Revenue dan unit per bulan dalam satu tahun.
     * SP: GetSalesMonthlyChart @Year
     */
    public function getMonthlyChart(int $year = 0): array
    {
        if ($year === 0) $year = (int) now()->format('Y');

        return Cache::remember(
            $this->chartKey($year),
            self::TTL_CHART,
            fn() => $this->fetchMonthlyChart($year),
        );
    }

    /**
     * Top finco berdasarkan unit.
     * SP: GetSalesAnalyticsTopFinco @Day, @Month, @Year, @Top
     */
    public function getTopFinco(int $day = 0, int $month = 0, int $year = 0, int $top = 5): array
    {
        if ($day   === 0) $day   = (int) now()->format('d');
        if ($month === 0) $month = (int) now()->format('m');
        if ($year  === 0) $year  = (int) now()->format('Y');

        return Cache::remember(
            $this->fincoKey($day, $month, $year),
            self::TTL_FINCO,
            fn() => $this->fetchTopFinco($day, $month, $year, $top),
        );
    }

    /**
     * Top produk terlaris.
     * SP: GetTopSellingProducts @Day, @Month, @Year
     */
    public function getTopProducts(int $day = 0, int $month = 0, int $year = 0): array
    {
        if ($day   === 0) $day   = (int) now()->format('d');
        if ($month === 0) $month = (int) now()->format('m');
        if ($year  === 0) $year  = (int) now()->format('Y');

        return Cache::remember(
            $this->productKey($day, $month, $year),
            self::TTL_PRODUCT,
            fn() => $this->fetchTopProducts($day, $month, $year),
        );
    }

    /**
     * Ranking cabang — reuse SP GetRankingBranch dari SalesService.
     */
    public function getBranchRanking(int $month = 0, int $year = 0): array
    {
        if ($month === 0) $month = (int) now()->format('m');
        if ($year  === 0) $year  = (int) now()->format('Y');

        return Cache::remember(
            $this->branchKey($month, $year),
            self::TTL_BRANCH,
            fn() => $this->fetchBranchRanking($month, $year),
        );
    }

    /**
     * Paksa refresh semua cache.
     */
    public function flushCache(int $day = 0, int $month = 0, int $year = 0): void
    {
        if ($day   === 0) $day   = (int) now()->format('d');
        if ($month === 0) $month = (int) now()->format('m');
        if ($year  === 0) $year  = (int) now()->format('Y');
        Cache::forget($this->summaryKey($month, $year));
        Cache::forget($this->chartKey($year));
        Cache::forget($this->fincoKey($day, $month, $year));
        Cache::forget($this->branchKey($month, $year));
        Cache::forget($this->productKey($day, $month, $year));
    }

    // =========================================================================
    // Private Fetchers
    // =========================================================================

    private function fetchSummary(int $day, int $month, int $year): array
    {
        try {
            $result = DB::connection('sqlsrv')->select(
                'EXEC GetSalesAnalyticsSummary @Day = ?, @Month = ?, @Year = ?',
                [$day, $month, $year],
            );

            if (empty($result)) return $this->emptySummary();

            $r = $this->normalize($result[0]);

            return [
                'total_revenue'      => (float) ($r['total_revenue']      ?? 0),
                'total_transactions' => (int)   ($r['total_transactions'] ?? 0),
                'total_units'        => (int)   ($r['total_units']        ?? 0),
                'asp'                => (float) ($r['asp']                ?? 0),
                'revenue_growth'     => (float) ($r['revenue_growth']     ?? 0),
                'transaction_growth' => (float) ($r['transaction_growth'] ?? 0),
                'unit_growth'        => (float) ($r['unit_growth']        ?? 0),
                'asp_growth'         => (float) ($r['asp_growth']         ?? 0),
            ];
        } catch (Throwable $e) {
            Log::error('SalesAnalyticsService::fetchSummary', [
                'error' => $e->getMessage(),
                'day' => $day,
                'month' => $month,
                'year' => $year,
            ]);
            return $this->emptySummary();
        }
    }

    private function fetchMonthlyChart(int $year): array
    {
        try {
            $result = DB::connection('sqlsrv')->select(
                'EXEC GetSalesMonthlyChart @Year = ?',
                [$year],
            );

            return collect($result)->map(function ($row) {
                $r = $this->normalize($row);
                return [
                    'month'   => (string) ($r['month_label'] ?? ''),
                    'unit'    => (int)    ($r['unit']        ?? 0),
                    'revenue' => (float)  ($r['revenue']     ?? 0),
                ];
            })->toArray();
        } catch (Throwable $e) {
            Log::error('SalesAnalyticsService::fetchMonthlyChart', ['error' => $e->getMessage(), 'year' => $year]);
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            return collect($months)->map(fn($m) => ['month' => $m, 'unit' => 0, 'revenue' => 0])->toArray();
        }
    }

    private function fetchTopFinco(int $day, int $month, int $year, int $top): array
    {
        try {
            $result = DB::connection('sqlsrv')->select(
                'EXEC GetSalesAnalyticsTopFinco @Day = ?, @Month = ?, @Year = ?, @Top = 3',
                [$day, $month, $year, $top],
            );

            return collect($result)->map(function ($row) {
                $r = $this->normalize($row);
                return [
                    'name'    => (string) ($r['finco_name'] ?? ''),
                    'unit'    => (int)    ($r['unit']       ?? 0),
                    'amount'  => (float)  ($r['amount']     ?? 0),
                    'percent' => (float)  ($r['percent']    ?? 0),
                ];
            })->toArray();
        } catch (Throwable $e) {
            Log::error('SalesAnalyticsService::fetchTopFinco', ['error' => $e->getMessage()]);
            return [];
        }
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    private function fetchTopProducts(
        int $day,
        int $month,
        int $year,
        int $top = 10
    ): array {
        try {
            $result = DB::connection('sqlsrv')->select(
                'EXEC GetTopSellingProducts @Day = ?, @Month = ?, @Year = ?, @Top = ?',
                [$day, $month, $year, $top],
            );

            return collect($result)
                ->map(function ($row) {
                    $r = $this->normalize($row);

                    return [
                        'kode'     => (string) ($r['kode'] ?? ''),
                        'name'     => (string) ($r['barang'] ?? ''),
                        'category' => (string) ($r['kategori'] ?? ''),
                        'unit'     => (int) ($r['totalterjual'] ?? 0),
                    ];
                })
                ->toArray();
        } catch (Throwable $e) {
            Log::error('SalesAnalyticsService::fetchTopProducts', [
                'error' => $e->getMessage(),
                'day'   => $day,
                'month' => $month,
                'year'  => $year,
                'top'   => $top,
            ]);

            return [];
        }
    }

    private function fetchBranchRanking(int $month, int $year): array
    {
        try {
            $result = DB::connection('sqlsrv')->select(
                'EXEC GetRankingBranch @Month = ?, @Year = ?',
                [$month, $year],
            );

            return collect($result)
                ->map(function ($row) {
                    $r = $this->normalize($row);
                    return [
                        'ranking'      => (int)    ($r['ranking']      ?? 0),
                        'branch_name'  => (string) ($r['branch_name']  ?? ''),
                        'total_amount' => (float)  ($r['total_amount'] ?? 0),
                    ];
                })
                ->toArray();
        } catch (Throwable $e) {
            Log::error('SalesAnalyticsService::fetchBranchRanking', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function normalize(mixed $row): array
    {
        return array_change_key_case(
            is_array($row) ? $row : (array) $row,
            CASE_LOWER,
        );
    }

    private function emptySummary(): array
    {
        return [
            'total_revenue'      => 0.0,
            'total_transactions' => 0,
            'total_units'        => 0,
            'asp'                => 0.0,
            'revenue_growth'     => 0.0,
            'transaction_growth' => 0.0,
            'unit_growth'        => 0.0,
            'asp_growth'         => 0.0,
        ];
    }
}
