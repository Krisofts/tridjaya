<?php

namespace App\Dashboard\Sales\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class SalesService
{
    private const TTL_TODAY          = 60;   // 1 menit  — realtime
    private const TTL_MONTHLY        = 300;  // 5 menit
    private const TTL_RANKING_SALES  = 120;  // 2 menit
    private const TTL_RANKING_BRANCH = 120;  // 2 menit
    private const TTL_DAILY_DEALER   = 60;   // 1 menit  — realtime

    // =========================================================================
    // Cache Keys
    // =========================================================================

    private function todayKey(): string
    {
        return 'dashboard.sales.today.' . today()->toDateString();
    }

    private function sparklineKey(): string
    {
        return 'dashboard.sales.sparkline.' . today()->toDateString();
    }

    private function monthlyKey(): string
    {
        return 'dashboard.sales.monthly_target.' . now()->format('Y-m');
    }

    private function salesRankingKey(int $month, int $year, int $kodeJabatan): string
    {
        return "dashboard.sales.ranking.sales.{$year}.{$month}.{$kodeJabatan}";
    }

    private function branchRankingKey(int $month, int $year): string
    {
        return "dashboard.sales.ranking.branch.{$year}.{$month}";
    }

    private function dailyDealerKey(): string
    {
        return 'dashboard.sales.daily_dealer.' . today()->toDateString();
    }

    private function allSalesRankingKey(int $month, int $year, int $kodeJabatan): string
    {
        return "dashboard.sales.ranking.sales.all.{$year}.{$month}.{$kodeJabatan}";
    }

    // =========================================================================
    // Public API
    // =========================================================================

    /**
     * Penjualan hari ini vs kemarin.
     * SP: GetTodaySale
     */
    public function getTodaySale(): array
    {
        return Cache::remember($this->todayKey(), self::TTL_TODAY, fn () => $this->fetchTodaySale());
    }

    /**
     * Sparkline revenue 7 hari terakhir.
     * SP: GetDailySalesLast7Days
     */
    public function getSparkline(): array
    {
        return Cache::remember($this->sparklineKey(), self::TTL_TODAY, fn () => $this->fetchSparkline());
    }

    /**
     * Target & pencapaian revenue bulan berjalan.
     * SP: GetMonthlyRevenueTargetSummary
     */
    public function getMonthlyRevenueTarget(): array
    {
        return Cache::remember($this->monthlyKey(), self::TTL_MONTHLY, fn () => $this->fetchMonthlyTarget());
    }

    /**
     * Top 10 ranking sales berdasarkan unit bulan berjalan.
     * SP: GetSalesRanking
     */
    public function getSalesRanking(int $kodeJabatan, ?int $month = null, ?int $year = null): array
    {
        $month ??= (int) now()->format('m');
        $year  ??= (int) now()->format('Y');

        return Cache::remember(
            $this->salesRankingKey($month, $year, $kodeJabatan),
            self::TTL_RANKING_SALES,
            fn () => $this->fetchSalesRanking($kodeJabatan, $month, $year)
        );
    }

    /**
     * Semua sales ranking tanpa limit — untuk expand "lihat semua".
     * SP: GetSalesRanking @Limit = 9999
     */
    public function getAllSalesRanking(int $kodeJabatan, ?int $month = null, ?int $year = null): array
    {
        $month ??= (int) now()->format('m');
        $year  ??= (int) now()->format('Y');

        return Cache::remember(
            $this->allSalesRankingKey($month, $year, $kodeJabatan),
            self::TTL_RANKING_SALES,
            fn () => $this->fetchAllSalesRanking($kodeJabatan, $month, $year)
        );
    }

    /**
     * Ranking cabang berdasarkan amount realtime bulan berjalan.
     * SP: GetRankingBranch
     */
    public function getBranchRanking(?int $month = null, ?int $year = null): array
    {
        $month ??= (int) now()->format('m');
        $year  ??= (int) now()->format('Y');

        return Cache::remember(
            $this->branchRankingKey($month, $year),
            self::TTL_RANKING_BRANCH,
            fn () => $this->fetchBranchRanking($month, $year)
        );
    }

    /**
     * Target harian per dealer — revenue hari ini vs target harian.
     * SP: GetDailyTargetDealer
     */
    public function getDailyDealerTarget(?int $month = null, ?int $year = null): array
    {
        $month ??= (int) now()->format('m');
        $year  ??= (int) now()->format('Y');

        return Cache::remember(
            $this->dailyDealerKey(),
            self::TTL_DAILY_DEALER,
            fn () => $this->fetchDailyDealerTarget()
        );
    }

    /**
     * Paksa refresh semua cache dashboard sales.
     */
    public function flushCache(?int $kodeJabatan = null): void
    {
        Cache::forget($this->todayKey());
        Cache::forget($this->sparklineKey());
        Cache::forget($this->monthlyKey());
        Cache::forget($this->dailyDealerKey());
        Cache::forget($this->branchRankingKey(
            (int) now()->format('m'),
            (int) now()->format('Y'),
        ));

        if ($kodeJabatan !== null) {
            Cache::forget($this->salesRankingKey(
                (int) now()->format('m'),
                (int) now()->format('Y'),
                $kodeJabatan,
            ));
            Cache::forget($this->allSalesRankingKey(
                (int) now()->format('m'),
                (int) now()->format('Y'),
                $kodeJabatan,
            ));
        }
    }

    // =========================================================================
    // Private Fetchers
    // =========================================================================

    private function fetchTodaySale(): array
    {
        try {
            $data = $this->firstRow(
                DB::connection('sqlsrv')->select('EXEC GetTodaySale')
            );

            $txToday       = (int)   ($data['transaction_count_today']    ?? 0);
            $txYesterday   = (int)   ($data['transaction_count_yesterday'] ?? 0);
            $unitToday     = (int)   ($data['unit_sold_today']             ?? 0);
            $unitYesterday = (int)   ($data['unit_sold_yesterday']         ?? 0);
            $revToday      = (float) ($data['revenue_today']               ?? 0);
            $revYesterday  = (float) ($data['revenue_yesterday']           ?? 0);

            return [
                'transaction' => [
                    'today'      => $txToday,
                    'yesterday'  => $txYesterday,
                    'growth_pct' => $this->growthPct($txToday, $txYesterday, $data, 'transaction_count_growth_pct'),
                ],
                'unit' => [
                    'today'      => $unitToday,
                    'yesterday'  => $unitYesterday,
                    'growth_pct' => $this->growthPct($unitToday, $unitYesterday, $data, 'unit_sold_growth_pct'),
                ],
                'revenue' => [
                    'today'      => $revToday,
                    'yesterday'  => $revYesterday,
                    'growth_pct' => $this->growthPct($revToday, $revYesterday, $data, 'revenue_growth_pct'),
                ],
                'avg_transaction' => (float) ($data['avg_sales_per_transaction'] ?? 0),
            ];

        } catch (Throwable $e) {
            Log::error('SalesService::fetchTodaySale failed', ['error' => $e->getMessage()]);
            return $this->emptyTodaySale();
        }
    }

    private function fetchSparkline(): array
    {
        try {
            $result = DB::connection('sqlsrv')->select('EXEC GetDailySalesLast7Days');

            return collect($result)
                ->map(fn ($row) => (float) ((array) $row)['revenue'])
                ->values()
                ->toArray();

        } catch (Throwable $e) {
            Log::error('SalesService::fetchSparkline failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function fetchMonthlyTarget(): array
    {
        try {
            $data = $this->firstRow(
                DB::connection('sqlsrv')->select('EXEC GetMonthlyRevenueTargetSummary')
            );

            $actual = (float) ($data['revenue_actual'] ?? 0);
            $target = (float) ($data['revenue_target'] ?? 0);

            $achievementPct = $target > 0
                ? round(min(($actual / $target) * 100, 999.99), 2)
                : 0.0;

            return [
                'actual'            => $actual,
                'target'            => $target,
                'remaining_revenue' => (float) ($data['remaining_revenue']     ?? max(0.0, $target - $actual)),
                'remaining_days'    => (int)   ($data['remaining_days']         ?? 0),
                'needed_per_day'    => (float) ($data['needed_revenue_per_day'] ?? 0),
                'achievement_pct'   => (float) ($data['achievement_pct']        ?? $achievementPct),
                'status'            => $this->resolveStatus($data, $achievementPct),
            ];

        } catch (Throwable $e) {
            Log::error('SalesService::fetchMonthlyTarget failed', ['error' => $e->getMessage()]);
            return $this->emptyMonthlyTarget();
        }
    }

    private function fetchSalesRanking(int $kodeJabatan, int $month, int $year): array
    {
        try {
            $result = DB::connection('sqlsrv')->select(
                'EXEC GetSalesRanking @Month = ?, @Year = ?, @KodeJabatan = ?',
                [$month, $year, $kodeJabatan]
            );

            return collect($result)
                ->map(function ($row) {
                    $r = array_change_key_case((array) $row, CASE_LOWER);
                    return [
                        'ranking'              => (int)    ($r['ranking']              ?? 0),
                        'sales_name'           => (string) ($r['sales_name']           ?? ''),
                        'unit_today'           => (int)    ($r['unit_today']           ?? 0),
                        'unit_current_month'   => (int)    ($r['unit_current_month']   ?? 0),
                        'transactions'         => (int)    ($r['transactions']         ?? 0),
                        'amount_current_month' => (float)  ($r['amount_current_month'] ?? 0),
                    ];
                })
                ->toArray();

        } catch (Throwable $e) {
            Log::error('SalesService::fetchSalesRanking failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function fetchAllSalesRanking(int $kodeJabatan, int $month, int $year): array
    {
        try {
            $result = DB::connection('sqlsrv')->select(
                'EXEC GetAllSalesRanking @Month = ?, @Year = ?, @KodeJabatan = ?',
                [$month, $year, $kodeJabatan]
            );

            return collect($result)
                ->map(function ($row) {
                    $r = array_change_key_case((array) $row, CASE_LOWER);
                    return [
                        'ranking'              => (int)    ($r['ranking']              ?? 0),
                        'sales_name'           => (string) ($r['sales_name']           ?? ''),
                        'unit_today'           => (int)    ($r['unit_today']           ?? 0),
                        'unit_current_month'   => (int)    ($r['unit_current_month']   ?? 0),
                        'transactions'         => (int)    ($r['transactions']         ?? 0),
                        'amount_current_month' => (float)  ($r['amount_current_month'] ?? 0),
                    ];
                })
                ->toArray();

        } catch (Throwable $e) {
            Log::error('SalesService::fetchAllSalesRanking failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function fetchBranchRanking(int $month, int $year): array
    {
        try {
            $result = DB::connection('sqlsrv')->select(
                'EXEC GetRankingBranch @Month = ?, @Year = ?',
                [$month, $year]
            );

            return collect($result)
                ->map(function ($row) {
                    $r = array_change_key_case((array) $row, CASE_LOWER);
                    return [
                        'ranking'      => (int)    ($r['ranking']      ?? 0),
                        'branch_name'  => (string) ($r['branch_name']  ?? ''),
                        'total_amount' => (float)  ($r['total_amount'] ?? 0),
                    ];
                })
                ->toArray();

        } catch (Throwable $e) {
            Log::error('SalesService::fetchBranchRanking failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function fetchDailyDealerTarget(): array
    {
        try {
            $result = DB::connection('sqlsrv')->select('EXEC GetDailyTargetDealer');

            return collect($result)
                ->map(function ($row) {
                    $r = array_change_key_case((array) $row, CASE_LOWER);
                    return [
                        'dealer_name'           => (string) ($r['dealer_name']           ?? ''),
                        'today_unit'            => (int)    ($r['today_unit']            ?? 0),
                        'today_amount'          => (float)  ($r['today_amount']          ?? 0),
                        'target_unit_daily'     => (float)  ($r['target_unit_daily']     ?? 0),
                        'target_amount_daily'   => (float)  ($r['target_amount_daily']   ?? 0),
                        'achievement_pct_daily' => (float)  ($r['achievement_pct_daily'] ?? 0),
                        'status'                => (string) ($r['status']                ?? 'UNKNOWN'),
                    ];
                })
                ->toArray();

        } catch (Throwable $e) {
            Log::error('SalesService::fetchDailyDealerTarget failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Ambil baris pertama dari result SP.
     * Normalize key ke lowercase — sqlsrv kadang return 'Status' bukan 'status'.
     */
    private function firstRow(array $result): array
    {
        $first = collect($result)->first();

        if ($first === null) {
            return [];
        }

        return array_change_key_case(
            is_array($first) ? $first : (array) $first,
            CASE_LOWER
        );
    }

    /**
     * Hitung growth % — prioritas dari SP, fallback hitung sendiri.
     */
    private function growthPct(float|int $current, float|int $previous, array $data, string $spKey): float
    {
        if (isset($data[$spKey]) && $data[$spKey] !== null) {
            return round((float) $data[$spKey], 2);
        }

        if ($previous == 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 2);
    }

    /**
     * Normalize status dari SP dengan backward compatibility.
     */
    private function resolveStatus(array $data, float $achievementPct): string
    {
        $spStatus = strtoupper(trim($data['status'] ?? ''));

        $legacyMap = [
            'CRITICAL' => 'BEHIND',
            'WARNING'  => 'AT_RISK',
        ];

        if (isset($legacyMap[$spStatus])) {
            return $legacyMap[$spStatus];
        }

        $validStatuses = ['ACHIEVED', 'ON_TRACK', 'AT_RISK', 'BEHIND', 'NO_TARGET'];

        if (in_array($spStatus, $validStatuses, true)) {
            return $spStatus;
        }

        return match (true) {
            $achievementPct >= 100 => 'ACHIEVED',
            $achievementPct >= 75  => 'ON_TRACK',
            $achievementPct >= 50  => 'AT_RISK',
            default                => 'BEHIND',
        };
    }

    // =========================================================================
    // Empty Fallbacks
    // =========================================================================

    private function emptyTodaySale(): array
    {
        $empty = ['today' => 0, 'yesterday' => 0, 'growth_pct' => 0.0];

        return [
            'transaction'     => $empty,
            'unit'            => $empty,
            'revenue'         => $empty,
            'avg_transaction' => 0.0,
        ];
    }

    private function emptyMonthlyTarget(): array
    {
        return [
            'actual'            => 0.0,
            'target'            => 0.0,
            'remaining_revenue' => 0.0,
            'remaining_days'    => 0,
            'needed_per_day'    => 0.0,
            'achievement_pct'   => 0.0,
            'status'            => 'UNKNOWN',
        ];
    }
}