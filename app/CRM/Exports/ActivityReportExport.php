<?php

namespace App\CRM\Exports;

use App\CRM\Models\CrmLeadActivity;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ActivityReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    public function __construct(
        private readonly array $filters,
    ) {}

    public function title(): string
    {
        return 'Laporan Aktivitas';
    }

    public function collection()
    {
        return CrmLeadActivity::query()
            ->with(['lead', 'type', 'result', 'user'])
            ->whereBetween('activity_at', [
                $this->filters['date_from'] . ' 00:00:00',
                $this->filters['date_to']   . ' 23:59:59',
            ])
            ->when($this->filters['assigned_to'], fn ($q) => $q->where('user_id', $this->filters['assigned_to']))
            ->whereHas('type', fn ($q) => $q->where('slug', '!=', 'sistem'))
            ->orderByDesc('activity_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Sales',
            'Lead',
            'Jenis Aktivitas',
            'Judul',
            'Hasil',
            'Terhubung',
            'Catatan',
            'Lokasi',
            'Waktu Aktivitas',
        ];
    }

    public function map($activity): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $activity->user->name        ?? '—',
            $activity->lead->name        ?? '—',
            $activity->type->name        ?? '—',
            $activity->title,
            $activity->result->name      ?? '—',
            $activity->is_contacted ? 'Ya' : 'Tidak',
            $activity->notes             ?? '—',
            $activity->location          ?? '—',
            $activity->activity_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF7C3AED']],
            ],
        ];
    }
}