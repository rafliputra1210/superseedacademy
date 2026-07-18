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
            $coach->nama,
            strtoupper($coach->status_lisensi),
            $coach->detail_lisensi ?: 'Tidak Ada / Asisten',
            $coach->nomor_wa ?: '-',
            $coach->referensi ?: '-',
            $coach->alamat ?: '-',
            \Carbon\Carbon::parse($coach->created_at)->format('d/m/Y'),
        ];
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