<?php

namespace App\Exports;

use App\Models\Athlete;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AthletesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private $rowNumber = 0;

    public function collection()
    {
        return Athlete::with('user')->orderBy('nama')->get();
    }

    // 1. UPDATE HEADINGS: Tambahkan Kelompok Umur dan Kelompok Latihan
    public function headings(): array
    {
        return [
            'No.',
            'Nama Lengkap Siswa',
            'Kelompok Umur',       // <-- Kolom Baru (Contoh: U-12)
            'Kelompok Latihan',    // <-- Kolom Baru (Contoh: Kelas Prestasi)
            'Nomor Punggung',
            'Posisi Bermain',
            'Tanggal Lahir',
            'Nomor WA Orang Tua',
            'Nomor WA Siswa',
            'Alamat Domisili',
            'Username Akun Wali',  // Info akses login orang tua
            'Tanggal Terdaftar'
        ];
    }

    // 2. UPDATE MAPPING: Memetakan isi data per baris sesuai urutan headings
    public function map($athlete): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $this->sanitizeFormula($athlete->nama),
            $this->sanitizeFormula($athlete->kelompok_umur ?? 'U-12'),
            $this->sanitizeFormula($athlete->kelompok_latihan ?? 'Kelas Reguler'),
            $this->sanitizeFormula((string)($athlete->nomor_punggung ?? '-')),
            $this->sanitizeFormula($athlete->posisi_bermain ?? 'Belum ditentukan'),
            $athlete->tanggal_lahir ? \Carbon\Carbon::parse($athlete->tanggal_lahir)->format('Y-m-d') : '-',
            $this->sanitizeFormula((string)$athlete->nomor_wa_ortu),
            $this->sanitizeFormula((string)($athlete->nomor_wa ?: '-')),
            $this->sanitizeFormula((string)($athlete->alamat ?: '-')),
            $this->sanitizeFormula($athlete->user ? $athlete->user->username : 'Belum Ada Akun'),
            \Carbon\Carbon::parse($athlete->created_at)->format('Y-m-d'),
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

    // 3. STYLING HEADER: Warna hijau khas Superseed Academy
    public function styles(Worksheet $sheet)
    {
        return [
            1    => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => '064E3B']]
            ],
        ];
    }
}