<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class CandidatesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithCustomStartCell, WithEvents
{
    protected $candidates;
    protected string $periodName;
    protected string $eventDate;
    protected int $rowNumber = 0;

    public function __construct(Collection $candidates, string $periodName = '', string $eventDate = '')
    {
        $this->candidates = $candidates;
        $this->periodName = $periodName;
        $this->eventDate  = $eventDate;
    }

    public function collection(): \Illuminate\Support\Enumerable
    {
        return $this->candidates;
    }

    /** Data table starts at row 5 (rows 1-4 are title rows) */
    public function startCell(): string
    {
        return 'A5';
    }

    public function map($candidate): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $candidate->full_name,
            $candidate->nim,
            $candidate->birth_place . ', ' . $candidate->birth_date->format('d/m/Y'),
            $candidate->admission_path,
            $candidate->father_name,
            $candidate->mother_name,
        ];
    }

    public function headings(): array
    {
        return [
            'No.',
            'Nama Lengkap',
            'NIM',
            'Tempat, Tanggal Lahir',
            'Jalur Masuk',
            'Nama Ayah',
            'Nama Ibu',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Row 1: Main title
                $sheet->setCellValue('A1', 'DAFTAR PESERTA SUMPAH PROFESI');
                $sheet->mergeCells('A1:G1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                ]);

                // Row 2: Period name
                $sheet->setCellValue('A2', 'Periode: ' . $this->periodName);
                $sheet->mergeCells('A2:G2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                ]);

                // Row 3: Event date
                $sheet->setCellValue('A3', 'Tanggal Kegiatan: ' . $this->eventDate);
                $sheet->mergeCells('A3:G3');
                $sheet->getStyle('A3')->applyFromArray([
                    'font'      => ['size' => 10],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                ]);

                // Row 4: empty separator
                $sheet->setCellValue('A4', '');

                // Style heading row (row 5)
                $sheet->getStyle('A5:G5')->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => 'FF4E73DF']],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                ]);

                // Border around data
                $lastRow = 5 + $this->rowNumber;
                if ($lastRow > 5) {
                    $sheet->getStyle("A5:G{$lastRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color'       => ['argb' => 'FFD1D5DB'],
                            ],
                        ],
                    ]);
                }
            },
        ];
    }
}
