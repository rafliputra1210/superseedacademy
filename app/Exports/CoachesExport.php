<?php

namespace App\Exports;

use App\Models\Coach;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CoachesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private $rowNumber = 0;

    public function collection()
    {
        return Coach::orderBy('nama')->get();
    }

    public function headings(): array
    {
        return [
            'No.',
            'Nama Lengkap Coach',
            'Status Lisensi (berlisensi / tidak_berlisensi)',
            'Detail Lisensi (Opsional)',
            'Nomor WhatsApp',
            'Referensi Pengalaman Melatih',
            'Alamat Domisili',
            'Tanggal Bergabung'
        ];
    }

    public function map($coach): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $this->sanitizeFormula($coach->nama),
            $this->sanitizeFormula(strtoupper($coach->status_lisensi)),
            $this->sanitizeFormula($coach->detail_lisensi ?: 'Tidak Ada / Asisten'),
            $this->sanitizeFormula((string)($coach->nomor_wa ?: '-')),
            $this->sanitizeFormula((string)($coach->referensi ?: '-')),
            $this->sanitizeFormula((string)($coach->alamat ?: '-')),
            \Carbon\Carbon::parse($coach->created_at)->format('d/m/Y'),
        ];
    }

    /**
     * Sanitasi pencegahan Formula Injection (CWE-1236) pada file Excel/CSV.
     */
    private function sanitizeFormula(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        $triggers = ['=', '+', '-', '@', "\t", "\r"];
        if (in_array(substr($value, 0, 1), $triggers, true)) {
            return "'" . $value;
        }

        return $value;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => '047857']]
            ],
        ];
    }
}