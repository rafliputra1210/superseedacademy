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
    private array $filters = [];

    public function __construct($filters = null)
    {
        if (is_string($filters)) {
            $this->filters = ['bulan' => $filters];
        } elseif (is_array($filters)) {
            $this->filters = $filters;
        }
    }

    public function collection()
    {
        $query = Finance::query()->with('athlete');
        
        $bulan = $this->filters['bulan'] ?? null;
        if ($bulan && $bulan !== 'semua') {
            $indonesianMonths = [
                'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
                'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
                'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
            ];
            $parts = explode(' ', trim($bulan));
            if (count($parts) >= 2 && isset($indonesianMonths[$parts[0]])) {
                $m = sprintf('%02d', $indonesianMonths[$parts[0]]);
                $y = $parts[1];
                $startDate = "{$y}-{$m}-01";
                $endDate = \Carbon\Carbon::parse($startDate)->endOfMonth()->toDateString();

                $query->where(function($q) use ($bulan, $startDate, $endDate) {
                    $q->where('bulan_tagihan', $bulan)
                      ->orWhereBetween('tanggal', [$startDate, $endDate]);
                });
            } else {
                $query->where('bulan_tagihan', $bulan);
            }
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('kategori', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('athlete', function($q2) use ($search) {
                      $q2->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($this->filters['jenis'])) {
            $query->where('jenis', $this->filters['jenis']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status_bayar', $this->filters['status']);
        }

        if (!empty($this->filters['metode'])) {
            $query->where('metode_pembayaran', $this->filters['metode']);
        }

        return $query->orderBy('tanggal', 'asc')->orderBy('id', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'No.',
            'Tanggal',
            'Jenis Arus',
            'Metode Pembayaran',
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

        $metode = '-';
        if ($finance->metode_pembayaran === 'cash') {
            $metode = 'Cash (Tunai)';
        } elseif ($finance->metode_pembayaran === 'transfer') {
            $metode = 'Transfer Bank' . ($finance->nama_pengirim_transfer ? ' (a.n. ' . $finance->nama_pengirim_transfer . ')' : '');
        }

        return [
            $this->rowNumber,
            \Carbon\Carbon::parse($finance->tanggal)->format('d/m/Y'),
            $finance->jenis == 'pemasukan' ? 'Pemasukan' : 'Pengeluaran',
            $this->sanitizeFormula($metode),
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
