<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Keuangan Superseed Academy</title>
    <!-- Include Bootstrap CSS (using CDN for print view is easiest, or local if needed) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body { font-size: 12pt; }
            .no-print { display: none !important; }
            .table th { background-color: #f8f9fa !important; -webkit-print-color-adjust: exact; }
            .bg-success { background-color: #198754 !important; -webkit-print-color-adjust: exact; color: white !important;}
            .bg-danger { background-color: #dc3545 !important; -webkit-print-color-adjust: exact; color: white !important;}
            .bg-primary { background-color: #0d6efd !important; -webkit-print-color-adjust: exact; color: white !important;}
        }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #fff; }
        .header-title { font-weight: bold; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
    </style>
</head>
<body onload="window.print()">
    <div class="container-fluid mt-4">
        
        <div class="d-flex justify-content-between align-items-end header-title">
            <div>
                <h2 class="mb-0">Laporan Keuangan & Buku Kas</h2>
                <h4 class="text-muted mb-0">Superseed Academy</h4>
            </div>
            <div class="text-end">
                <p class="mb-0"><strong>Filter Bulan:</strong> {{ request('bulan') ?: 'Semua Waktu' }}</p>
                <p class="mb-0"><strong>Dicetak pada:</strong> {{ \Carbon\Carbon::now()->format('d F Y, H:i') }}</p>
            </div>
        </div>

        <div class="row mb-4 text-center">
            <div class="col-3">
                <div class="p-2.5 bg-light rounded border">
                    <span class="text-uppercase text-muted small fw-bold">Saldo Awal</span>
                    <h5 class="mb-0 fw-bold text-dark">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</h5>
                </div>
            </div>
            <div class="col-3">
                <div class="p-2.5 bg-success text-white rounded border border-success">
                    <span class="text-uppercase small fw-bold">Pemasukan Lunas</span>
                    <h5 class="mb-0 fw-bold">+ Rp {{ number_format($totalPemasukanLunas, 0, ',', '.') }}</h5>
                </div>
            </div>
            <div class="col-3">
                <div class="p-2.5 bg-danger text-white rounded border border-danger">
                    <span class="text-uppercase small fw-bold">Total Pengeluaran</span>
                    <h5 class="mb-0 fw-bold">- Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h5>
                </div>
            </div>
            <div class="col-3">
                <div class="p-2.5 bg-primary text-white rounded border border-primary">
                    <span class="text-uppercase small fw-bold">Saldo Akhir</span>
                    <h5 class="mb-0 fw-bold">Rp {{ number_format($saldoSekarang, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-striped table-sm align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 100px;">Tanggal</th>
                    <th>Kategori & Keterangan</th>
                    <th style="width: 120px;">Jenis Arus</th>
                    <th style="width: 150px;">Nominal (Rp)</th>
                    <th style="width: 150px;">Saldo Akhir (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($finances as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    <td>
                        <strong>{{ $item->kategori }}</strong>
                        @if($item->athlete)
                            <br><small>{{ $item->athlete->nama }} (Bulan: {{ $item->bulan_tagihan }})</small>
                        @endif
                        @if($item->keterangan)
                            <br><small class="text-muted">{{ $item->keterangan }}</small>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($item->jenis == 'pemasukan')
                            <span class="text-success fw-bold">Pemasukan</span>
                        @else
                            <span class="text-danger fw-bold">Pengeluaran</span>
                        @endif
                    </td>
                    <td class="text-end fw-bold {{ $item->jenis == 'pemasukan' ? 'text-success' : 'text-danger' }}">
                        {{ $item->jenis == 'pemasukan' ? '+' : '-' }} {{ number_format($item->nominal, 0, ',', '.') }}
                    </td>
                    <td class="text-end fw-bold">
                        {{ number_format($item->saldo_akhir, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">Belum ada data transaksi keuangan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-5 row text-center">
            <div class="col-4 offset-8">
                <p class="mb-5">Mengetahui,</p>
                <br><br><br>
                <p class="fw-bold mb-0">( Administrator / Bendahara )</p>
                <p class="small text-muted">Superseed Academy</p>
            </div>
        </div>

        <div class="text-center mt-4 no-print">
            <button class="btn btn-secondary" onclick="window.close()">Tutup Jendela Ini</button>
            <button class="btn btn-primary" onclick="window.print()">Cetak Ulang</button>
        </div>
    </div>
</body>
</html>
