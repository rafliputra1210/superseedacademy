<?php

namespace App\Exports;

use App\Models\Athlete;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class BiographyTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    public function headings(): array
    {
        return [
            'nis_atlet',
            'nama_atlet',
            'kategori_kelas',
            'tanggal_bergabung',
            'posisi',
            'asal_sekolah',
            'nomor_punggung',
            'kaki_dominan',
            'tinggi_cm',
            'berat_kg'
        ];
    }

    public function array(): array
    {
        // Ambil daftar seluruh siswa atlet yang belum memiliki dokumen biografi
        $athletes = Athlete::doesntHave('biography')->orderBy('kelompok_umur')->orderBy('nama')->get();
        $rows = [];

        if ($athletes->isNotEmpty()) {
            foreach ($athletes as $idx => $atlet) {
                $nis = (string)($atlet->nomor_induk ?: $atlet->kode_barcode ?: '');
                
                // Deteksi otomatis kode kategori kelas dari data kelompok umur atlet
                $ku = strtoupper(trim((string)$atlet->kelompok_umur));
                $kodeKelas = '02'; // default U-10
                if (str_contains($ku, 'BORN') || str_contains($ku, 'NEW')) {
                    $kodeKelas = '01';
                } elseif (str_contains($ku, '10')) {
                    $kodeKelas = '02';
                } elseif (str_contains($ku, '13')) {
                    $kodeKelas = '03';
                } elseif (str_contains($ku, '16')) {
                    $kodeKelas = '04';
                }

                $rows[] = [
                    $nis,
                    $atlet->nama,
                    $kodeKelas,
                    date('Y-m-d'),
                    $atlet->posisi_bermain ?: 'Flank',
                    '', // asal_sekolah dapat diisikan oleh admin
                    (string)($atlet->nomor_punggung ?: ''),
                    'Kanan',
                    '',
                    ''
                ];
            }
        } else {
            // Contoh data acuan jika seluruh atlet terdaftar sudah memiliki biografi
            $rows = [
                ['00123', 'Ahmad Pratama', '02', '2026-01-15', 'Flank', 'SMPN 1 Jakarta', '10', 'Kanan', '165', '55'],
                ['00124', 'Budi Santoso', '03', '2026-02-10', 'Anchor', 'MTsN 2 Surabaya', '7', 'Kanan', '170', '60'],
                ['00125', 'Dimas Arya', '01', '2026-03-01', 'Kiper', 'SDN 3 Bandung', '1', 'Keduanya', '158', '50'],
            ];
        }

        return $rows;
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT, // nis_atlet (teks agar angka 0 di depan tidak hilang)
            'B' => NumberFormat::FORMAT_TEXT, // nama_atlet
            'C' => NumberFormat::FORMAT_TEXT, // kategori_kelas (01, 02, dsb.)
            'D' => NumberFormat::FORMAT_TEXT, // tanggal_bergabung (YYYY-MM-DD)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0A192F'] // Superseed navy header
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ]
            ],
        ];
    }
}
