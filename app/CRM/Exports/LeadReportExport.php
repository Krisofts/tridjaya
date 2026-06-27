<?php

namespace App\CRM\Exports;

use App\CRM\Models\CrmLead;
use App\CRM\Models\CrmPipelineStage;
use App\User\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class LeadReportExport implements
    FromCollection, 
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithTitle
{
    public function __construct(
        protected string $dateFrom,
        protected string $dateTo,
        protected ?int   $userId = null,
    ) {}

    public function title(): string
    {
        return 'Laporan Leads';
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Sales',
            'Prospek Hari Ini',
            'Prospek Periode (' . $this->dateFrom . ' s/d ' . $this->dateTo . ')',
            'Total Semua Prospek',
            'Close Deal (Total)',
            'Close Deal (Periode)',
            'Close Rate (%)',
        ];
    }

    public function collection(): Collection
    {
        $wonStageIds = CrmPipelineStage::where('is_won', true)->pluck('id');

        return User::when($this->userId, fn ($q) => $q->where('id', $this->userId))
            ->orderBy('name')
            ->get()
            ->map(function (User $user, int $index) use ($wonStageIds) {
                $base = CrmLead::where('assigned_to', $user->id);

                return [
                    'no'            => $index + 1,
                    'name'          => $user->name,
                    'today_leads'   => (clone $base)->whereDate('created_at', now()->format('Y-m-d'))->count(),
                    'period_leads'  => (clone $base)->whereDate('created_at', '>=', $this->dateFrom)->whereDate('created_at', '<=', $this->dateTo)->count(),
                    'total_leads'   => (clone $base)->count(),
                    'closed_total'  => (clone $base)->whereIn('pipeline_stage_id', $wonStageIds)->count(),
                    'closed_period' => (clone $base)->whereIn('pipeline_stage_id', $wonStageIds)->whereDate('updated_at', '>=', $this->dateFrom)->whereDate('updated_at', '<=', $this->dateTo)->count(),
                    'close_rate'    => (clone $base)->count() > 0
                        ? round(((clone $base)->whereIn('pipeline_stage_id', $wonStageIds)->count() / (clone $base)->count()) * 100, 1) . '%'
                        : '0%',
                ];
            });
    }

    public function map($row): array
    {
        return array_values((array) $row);
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        return [
            // Header row
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF3B5BDB']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ],
            // Data rows
            "A2:H{$lastRow}" => [
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FFDDDDDD'],
                    ],
                ],
            ],
            // Number columns center
            "C2:H{$lastRow}" => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}