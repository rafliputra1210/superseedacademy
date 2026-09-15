<?php

namespace App\Exports;

use App\Models\Finance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinancesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private int $rowNumber = 0;
    private ?string $bulan;

    public function __construct(?string $bulan = null)
    {
        $this->bulan = $bulan;
    }

    public function collection()
    {
        $query = Finance::query()->with('athlete');
        
        if ($this->bulan) {
            $query->where('bulan_tagihan', $this->bulan);
        }

        return $query->orderBy('tanggal', 'asc')->orderBy('id', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'No.',
            'Tanggal',
            'Jenis Arus',
            'Kategori & Keterangan',
            'Nama Siswa (Jika Ada)',
            'Status Bayar',
            'Bulan Tagihan',
            'Nominal (Rp)',
            'Saldo Akhir (Rp)'
        ];
    }

    public function map($finance): array
    {
        $this->rowNumber++;

        $keterangan = $finance->kategori . ($finance->keterangan ? ' - ' . $finance->keterangan : '');
        $namaSiswa = $finance->athlete ? $finance->athlete->nama : '-';

        return [
            $this->rowNumber,
            \Carbon\Carbon::parse($finance->tanggal)->format('d/m/Y'),
            $finance->jenis == 'pemasukan' ? 'Pemasukan' : 'Pengeluaran',
            $this->sanitizeFormula($keterangan),
            $this->sanitizeFormula($namaSiswa),
            $finance->status_bayar == 'lunas' ? 'Lunas' : 'Belum Lunas',
            $this->sanitizeFormula((string)($finance->bulan_tagihan ?: '-')),
            $finance->nominal,
            $finance->saldo_akhir,
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
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => '064E3B']] // Warna Hijau Superseed
            ],
        ];
    }
}
