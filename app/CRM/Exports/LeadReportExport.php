<?php

namespace App\CRM\Exports;

use App\CRM\Models\CrmLead;
use App\CRM\Services\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class LeadReportExport implements
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
        return 'Laporan Lead';
    }

    public function collection()
    {
        return CrmLead::query()
            ->with(['pipeline', 'stage', 'source', 'assignedUser', 'lostReason'])
            ->whereBetween('created_at', [
                $this->filters['date_from'] . ' 00:00:00',
                $this->filters['date_to']   . ' 23:59:59',
            ])
            ->when($this->filters['pipeline_id'], fn ($q) => $q->where('pipeline_id', $this->filters['pipeline_id']))
            ->when($this->filters['source_id'],   fn ($q) => $q->where('source_id',   $this->filters['source_id']))
            ->when($this->filters['assigned_to'], fn ($q) => $q->where('assigned_to', $this->filters['assigned_to']))
            ->when($this->filters['status'],      fn ($q) => $q->where('status',      $this->filters['status']))
            ->orderByDesc('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'Nomor HP',
            'Pipeline',
            'Stage',
            'Sumber',
            'Sales',
            'Status',
            'Estimasi Nilai',
            'Probabilitas',
            'Follow-up',
            'Alasan Lost',
            'Tanggal Buat',
            'Tanggal Tutup',
        ];
    }

    public function map($lead): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $lead->name,
            $lead->phone,
            $lead->pipeline->name ?? '—',
            $lead->stage->name    ?? '—',
            $lead->source->name   ?? '—',
            $lead->assignedUser->name ?? '—',
            $lead->statusLabel(),
            $lead->estimated_value,
            $lead->probability . '%',
            $lead->next_follow_up_at?->format('d/m/Y H:i') ?? '—',
            $lead->lostReason->name ?? '—',
            $lead->created_at->format('d/m/Y H:i'),
            $lead->closed_at?->format('d/m/Y H:i') ?? '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2563EB']],
            ],
        ];
    }
}