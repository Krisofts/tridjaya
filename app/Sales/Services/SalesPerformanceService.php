<?php

namespace App\Sales\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class SalesPerformanceService
{
    private const TTL_PERFORMANCE = 120;  // 2 menit
    private const TTL_DAILY       = 60;   // 1 menit — realtime
    private const TTL_BRANCH      = 120;  // 2 menit
    private const TTL_FINCO       = 120;  // 2 menit

    // =========================================================================
    // Cache Keys
    // =========================================================================

    private function performanceKey(int $month, int $year, int $kodeJabatan): string
    {
        return "sales.performance.{$year}.{$month}.{$kodeJabatan}";
    }

    private function dailyKey(string $salesName, int $month, int $year): string
    {
        return 'sales.performance.daily.' . Str::slug($salesName) . ".{$year}.{$month}";
    }

    private function branchKey(int $month, int $year, string $periodType): string
    {
        return "sales.performance.branch.{$year}.{$month}.{$periodType}";
    }

    private function fincoKey(int $month, int $year): string
    {
        return "sales.performance.finco.{$year}.{$month}";
    }

    // =========================================================================
    // Public API
    // =========================================================================

    /**
     * Performa semua sales bulan berjalan.
     * SP: GetSalesPerformance @Month, @Year, @KodeJabatan
     */
    public function getSalesPerformance(int $month = 0, int $year = 0, int $kodeJabatan = 3): array
    {
        if ($month === 0) $month = (int) now()->format('m');
        if ($year  === 0) $year  = (int) now()->format('Y');

        return Cache::remember(
            $this->performanceKey($month, $year, $kodeJabatan),
            self::TTL_PERFORMANCE,
            fn () => $this->fetchSalesPerformance($month, $year, $kodeJabatan),
        );
    }

    /**
     * Data penjualan harian satu sales dari tgl 1 s/d kemarin.
     * SP: GetSalesDailyPerformance @SalesName, @Month, @Year
     */
    public function getDailyPerformance(string $salesName, int $month = 0, int $year = 0): array
    {
        if ($month === 0) $month = (int) now()->format('m');
        if ($year  === 0) $year  = (int) now()->format('Y');

        return Cache::remember(
            $this->dailyKey($salesName, $month, $year),
            self::TTL_DAILY,
            fn () => $this->fetchDailyPerformance($salesName, $month, $year),
        );
    }

    /**
     * Performa per cabang/dealer bulan berjalan.
     * SP: getBranchPerformance @Month, @Year, @PeriodType
     */
    public function getBranchPerformance(int $month = 0, int $year = 0, string $periodType = 'MONTHLY'): array
    {
        if ($month === 0) $month = (int) now()->format('m');
        if ($year  === 0) $year  = (int) now()->format('Y');

        return Cache::remember(
            $this->branchKey($month, $year, $periodType),
            self::TTL_BRANCH,
            fn () => $this->fetchBranchPerformance($month, $year, $periodType),
        );
    }

    /**
     * Performa per finco/leasing bulan berjalan.
     * SP: GetFincoPerformance @Month, @Year
     */
    public function getFincoPerformance(int $month = 0, int $year = 0): array
    {
        if ($month === 0) $month = (int) now()->format('m');
        if ($year  === 0) $year  = (int) now()->format('Y');

        return Cache::remember(
            $this->fincoKey($month, $year),
            self::TTL_FINCO,
            fn () => $this->fetchFincoPerformance($month, $year),
        );
    }

    /**
     * Paksa refresh semua cache.
     */
    public function flushCache(int $kodeJabatan = 3, string $salesName = ''): void
    {
        $month = (int) now()->format('m');
        $year  = (int) now()->format('Y');

        Cache::forget($this->performanceKey($month, $year, $kodeJabatan));
        Cache::forget($this->branchKey($month, $year, 'MONTHLY'));
        Cache::forget($this->fincoKey($month, $year));

        if ($salesName !== '') {
            Cache::forget($this->dailyKey($salesName, $month, $year));
        }
    }

    // =========================================================================
    // Private Fetchers
    // =========================================================================

    private function fetchSalesPerformance(int $month, int $year, int $kodeJabatan): array
    {
        try {
            $result = DB::connection('sqlsrv')->select(
                'EXEC GetSalesPerformance @Month = ?, @Year = ?, @KodeJabatan = ?',
                [$month, $year, $kodeJabatan],
            );

            return collect($result)
                ->map(function ($row) {
                    $r = $this->normalize($row);

                    return [
                        'sales_name'              => (string) ($r['sales_name']              ?? ''),
                        'unit_today'              => (int)    ($r['unit_today']              ?? 0),
                        'unit_last_month'         => (int)    ($r['unit_last_month']         ?? 0),
                        'unit_current_month'      => (int)    ($r['unit_current_month']      ?? 0),
                        'amount_last_month'       => (float)  ($r['amount_last_month']       ?? 0),
                        'amount_current_month'    => (float)  ($r['amount_current_month']    ?? 0),
                        'incentive_last_month'    => (float)  ($r['incentive_last_month']    ?? 0),
                        'incentive_current_month' => (float)  ($r['incentive_current_month'] ?? 0),
                        'growth_unit_percent'     => (float)  ($r['growth_unit_percent']     ?? 0),
                        'growth_unit_diff'        => (float)  ($r['growth_unit_diff']        ?? 0),
                        'growth_amount_percent'   => (float)  ($r['growth_amount_percent']   ?? 0),
                        'growth_amount_diff'      => (float)  ($r['growth_amount_diff']      ?? 0),
                        'target_unit'             => (int)    ($r['target_unit']             ?? 0),
                        'target_amount'           => (float)  ($r['target_amount']           ?? 0),
                        'achievement_percent'     => (float)  ($r['achievement_percent']     ?? 0),
                        'difference_to_target'    => (float)  ($r['difference_to_target']    ?? 0),
                    ];
                })
                ->toArray();

        } catch (Throwable $e) {
            Log::error('SalesPerformanceService::fetchSalesPerformance', [
                'error'        => $e->getMessage(),
                'month'        => $month,
                'year'         => $year,
                'kodeJabatan'  => $kodeJabatan,
            ]);
            return [];
        }
    }

    private function fetchDailyPerformance(string $salesName, int $month, int $year): array
    {
        try {
            $result = DB::connection('sqlsrv')->select(
                'EXEC GetSalesDailyPerformance @SalesName = ?, @Month = ?, @Year = ?',
                [$salesName, $month, $year],
            );

            return collect($result)
                ->map(function ($row) {
                    $r = $this->normalize($row);

                    return [
                        'date'   => (string) ($r['daily_date'] ?? ''),
                        'unit'   => (int)    ($r['unit_sold']  ?? 0),
                        'amount' => (float)  ($r['amount']     ?? 0),
                    ];
                })
                ->toArray();

        } catch (Throwable $e) {
            Log::error('SalesPerformanceService::fetchDailyPerformance', [
                'error'      => $e->getMessage(),
                'sales_name' => $salesName,
            ]);
            return [];
        }
    }

    private function fetchBranchPerformance(int $month, int $year, string $periodType): array
    {
        try {
            $result = DB::connection('sqlsrv')->select(
                'EXEC getBranchPerformance @Month = ?, @Year = ?, @PeriodType = ?',
                [$month, $year, $periodType],
            );

            return collect($result)
                ->map(function ($row) {
                    $r = $this->normalize($row);

                    return [
                        'dealer_name'            => (string) ($r['dealer_name']            ?? ''),
                        'period_type'            => (string) ($r['period_type']            ?? 'MONTHLY'),
                        'today_unit'             => (int)    ($r['today_unit']             ?? 0),
                        'today_amount'           => (float)  ($r['today_amount']           ?? 0),
                        'unit_last_month'        => (int)    ($r['last_month_unit']        ?? 0),
                        'amount_last_month'      => (float)  ($r['last_month_amount']      ?? 0),
                        'unit_current_month'     => (int)    ($r['current_month_unit']     ?? 0),
                        'amount_current_month'   => (float)  ($r['current_month_amount']   ?? 0),
                        'growth_unit_percent'    => (float)  ($r['growth_unit_percent']    ?? 0),
                        'growth_unit_diff'       => (float)  ($r['growth_unit_difference'] ?? 0),
                        'growth_amount_percent'  => (float)  ($r['growth_amount_percent']  ?? 0),
                        'growth_amount_diff'     => (float)  ($r['growth_amount_difference'] ?? 0),
                        'target_unit'            => (int)    ($r['target_unit']            ?? 0),
                        'target_amount'          => (float)  ($r['target_amount']          ?? 0),
                        'achievement_percent'    => (float)  ($r['achievement_percent']    ?? 0),
                        'difference_to_target'   => (float)  ($r['difference_to_target']   ?? 0),
                    ];
                })
                ->toArray();

        } catch (Throwable $e) {
            Log::error('SalesPerformanceService::fetchBranchPerformance', [
                'error' => $e->getMessage(),
                'month' => $month,
                'year'  => $year,
            ]);
            return [];
        }
    }

    private function fetchFincoPerformance(int $month, int $year): array
    {
        try {
            $result = DB::connection('sqlsrv')->select(
                'EXEC GetFincoPerformance @Month = ?, @Year = ?',
                [$month, $year],
            );

            return collect($result)
                ->map(function ($row) {
                    $r = $this->normalize($row);

                    return [
                        'finco_name'           => (string) ($r['finco_name'] ?? $r['kodeleasing'] ?? $r['kode_leasing'] ?? ''),
                        'unit_today'           => (int)    ($r['unit_today']           ?? 0),
                        'amount_today'         => (float)  ($r['amount_today']         ?? 0),
                        'unit_current_month'   => (int)    ($r['unit_current_month']   ?? 0),
                        'amount_current_month' => (float)  ($r['amount_current_month'] ?? 0),
                        'unit_last_month'      => (int)    ($r['unit_last_month']      ?? 0),
                        'amount_last_month'    => (float)  ($r['amount_last_month']    ?? 0),
                        'growth_percent'       => (float)  ($r['growth_percent']       ?? 0),
                        'diff_growth'          => (float)  ($r['diff_growth']          ?? 0),
                    ];
                })
                ->toArray();

        } catch (Throwable $e) {
            Log::error('SalesPerformanceService::fetchFincoPerformance', [
                'error' => $e->getMessage(),
                'month' => $month,
                'year'  => $year,
            ]);
            return [];
        }
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    private function normalize(mixed $row): array
    {
        return array_change_key_case(
            is_array($row) ? $row : (array) $row,
            CASE_LOWER,
        );
    }
}