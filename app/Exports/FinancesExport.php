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

        return [
            $this->rowNumber,
            \Carbon\Carbon::parse($finance->tanggal)->format('d/m/Y'),
            $finance->jenis == 'pemasukan' ? 'Pemasukan' : 'Pengeluaran',
            $finance->kategori . ($finance->keterangan ? ' - ' . $finance->keterangan : ''),
            $finance->athlete ? $finance->athlete->nama : '-',
            $finance->status_bayar == 'lunas' ? 'Lunas' : 'Belum Lunas',
            $finance->bulan_tagihan ?: '-',
            $finance->nominal,
            $finance->saldo_akhir,
        ];
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
