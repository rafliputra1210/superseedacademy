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
            $athlete->nama,
            $athlete->kelompok_umur ?? 'U-12',            // <-- Mengambil data Kelompok Umur
            $athlete->kelompok_latihan ?? 'Kelas Reguler', // <-- Mengambil data Kelompok Latihan
            $athlete->nomor_punggung,
            $athlete->posisi_bermain ?? 'Belum ditentukan',
            $athlete->tanggal_lahir ? \Carbon\Carbon::parse($athlete->tanggal_lahir)->format('Y-m-d') : '-',
            $athlete->nomor_wa_ortu,
            $athlete->nomor_wa ?: '-',
            $athlete->alamat ?: '-',
            $athlete->user ? $athlete->user->username : 'Belum Ada Akun',
            \Carbon\Carbon::parse($athlete->created_at)->format('Y-m-d'),
        ];
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