<?php

namespace App\CRM\Exports;

use App\CRM\Services\ReportService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SalesPerformanceExport implements
    FromArray,
    WithHeadings,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    public function __construct(
        private readonly array $filters,
    ) {}

    public function title(): string
    {
        return 'Performa Sales';
    }

    public function array(): array
    {
        $service = app(ReportService::class);
        $data    = $service->salesPerformanceReport($this->filters);
        $rows    = [];
        $no      = 1;

        foreach ($data['bySales'] as $row) {
            $rows[] = [
                $no++,
                $row['name'],
                $row['total'],
                $row['open'],
                $row['won'],
                $row['lost'],
                $row['conv_rate'] . '%',
                'Rp ' . number_format($row['won_value'], 0, ',', '.'),
                $row['activities'],
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Sales',
            'Total Lead',
            'Open',
            'Won',
            'Lost',
            'Konversi',
            'Nilai Won',
            'Total Aktivitas',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF16A34A']],
            ],
        ];
    }
}