@extends('layouts.admin')
@section('title', 'Manajemen Uang Kas & Keuangan')

@section('content')
<div class="row g-3 mb-4">
    @if($filterBulanAktif)
    <div class="col-12">
        <div class="alert alert-info border-0 shadow-sm py-2 px-3 mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-funnel-fill"></i>
            <span class="small fw-bold">Menampilkan ringkasan keuangan periode: <strong>{{ $filterBulanAktif }}</strong></span>
            <a href="{{ route('admin.finances.index') }}" class="ms-auto btn btn-sm btn-outline-info py-0"><i class="bi bi-x"></i> Lihat Semua</a>
        </div>
    </div>
    @endif
    <!-- 1. Saldo Awal -->
    <div class="col-md-3">
        <div class="card card-custom bg-warning bg-gradient text-dark p-3 shadow-sm border-0 h-100">
            <span class="text-xs text-uppercase opacity-75 font-weight-bold"><i class="bi bi-wallet2 me-1"></i> Saldo Awal{{ $filterBulanAktif ? ' (Bawaan)' : '' }}</span>
            <h4 class="mb-2 mt-1 font-weight-bold">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</h4>
            <div class="border-top border-dark border-opacity-25 pt-2 mt-auto opacity-75 text-xs font-weight-bold">
                <i class="bi bi-arrow-left-right me-1"></i> Saldo Akhir Bulan Lalu
            </div>
        </div>
    </div>
    <!-- 2. Pemasukan Lunas -->
    <div class="col-md-3">
        <div class="card card-custom bg-success text-white p-3 shadow-sm border-0 h-100">
            <span class="text-xs text-uppercase opacity-75 font-weight-bold"><i class="bi bi-box-arrow-in-down me-1"></i> Pemasukan Lunas</span>
            <h4 class="mb-2 mt-1 font-weight-bold">+ Rp {{ number_format($totalPemasukanLunas, 0, ',', '.') }}</h4>
            <div class="border-top border-light pt-2 mt-auto opacity-75 text-xs font-weight-bold">
                <i class="bi bi-hourglass-split me-1"></i> Piutang: Rp {{ number_format($totalPemasukanBelumLunas, 0, ',', '.') }}
            </div>
        </div>
    </div>
    <!-- 3. Total Pengeluaran -->
    <div class="col-md-3">
        <div class="card card-custom bg-danger text-white p-3 shadow-sm border-0 h-100">
            <span class="text-xs text-uppercase opacity-75 font-weight-bold"><i class="bi bi-box-arrow-up-right me-1"></i> Total Pengeluaran</span>
            <h4 class="mb-2 mt-1 font-weight-bold">- Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h4>
            <div class="border-top border-light pt-2 mt-auto opacity-75 text-xs font-weight-bold">
                <i class="bi bi-calendar-event me-1"></i> {{ $filterBulanAktif ? 'Murni '.$filterBulanAktif : 'Murni Periode Ini' }}
            </div>
        </div>
    </div>
    <!-- 4. Saldo Akhir Kas -->
    <div class="col-md-3">
        <div class="card card-custom bg-primary text-white p-3 shadow-sm border-0 h-100">
            <span class="text-xs text-uppercase opacity-75 font-weight-bold"><i class="bi bi-cash-stack me-1"></i> Saldo Akhir Kas</span>
            <h4 class="mb-2 mt-1 font-weight-bold">Rp {{ number_format($saldoSekarang, 0, ',', '.') }}</h4>
            <div class="border-top border-light pt-2 mt-auto opacity-75 text-xs font-weight-bold">
                <i class="bi bi-calculator me-1"></i> Saldo Awal + Masuk - Keluar
            </div>
        </div>
    </div>
</div>

<div class="card card-custom bg-white p-4 shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h5 class="mb-1 font-weight-bold text-dark"><i class="bi bi-wallet2 text-success me-2"></i>Buku Kas Superseed Academy</h5>
            <p class="text-muted small mb-0">Catatan alur pemasukan dan pengeluaran uang kas secara transparan.</p>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <form id="bulkDeleteForm" action="{{ route('admin.finances.bulkDelete') }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
            <button type="button" class="btn btn-danger btn-sm font-weight-bold px-3 text-white shadow-sm d-none" id="btnBulkDelete" onclick="confirmBulkDelete()">
                <i class="bi bi-trash-fill me-1"></i> Hapus Terpilih
            </button>
            
            <a href="{{ route('admin.finances.print', request()->all()) }}" target="_blank" class="btn btn-secondary btn-sm font-weight-bold px-3 shadow-sm">
                <i class="bi bi-printer-fill me-1"></i> Print
            </a>
            
            <a href="{{ route('admin.finances.export', request()->all()) }}" class="btn btn-success btn-sm font-weight-bold px-3 shadow-sm">
                <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Excel
            </a>
            
            <button type="button" class="btn btn-warning btn-sm font-weight-bold px-3 text-dark shadow-sm" data-bs-toggle="modal" data-bs-target="#modalGenerateBulk">
                <i class="bi bi-lightning-charge-fill me-1"></i> Buat Tagihan Massal
            </button>
            <a href="{{ route('admin.finances.create') }}" class="btn btn-primary btn-sm font-weight-bold px-3 shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Catat Transaksi Baru
            </a>
        </div>
    </div>

    <!-- Kotak Pencarian & Filter Terpusat -->
    <div class="mb-4 bg-light p-3 rounded border border-light">
        <form action="{{ route('admin.finances.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
            <div class="input-group input-group-sm flex-grow-1 shadow-sm" style="min-width: 250px;">
                <span class="input-group-text bg-white border-secondary"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-secondary" placeholder="Cari nama siswa, kategori, keterangan..." value="{{ request('search') }}">
            </div>
            
            <select name="jenis" class="form-select form-select-sm w-auto border-secondary shadow-sm">
                <option value="">Semua Jenis</option>
                <option value="pemasukan" {{ request('jenis') == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                <option value="pengeluaran" {{ request('jenis') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
            </select>

            <select name="status" class="form-select form-select-sm w-auto border-secondary shadow-sm">
                <option value="">Semua Status</option>
                <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                <option value="belum_lunas" {{ request('status') == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
            </select>
            
            <select name="metode" class="form-select form-select-sm w-auto border-secondary shadow-sm">
                <option value="">Semua Metode</option>
                <option value="cash" {{ request('metode') == 'cash' ? 'selected' : '' }}>💵 Cash (Tunai)</option>
                <option value="transfer" {{ request('metode') == 'transfer' ? 'selected' : '' }}>🏦 Transfer Bank</option>
            </select>
            
            <select name="bulan" id="filterBulan" class="form-select form-select-sm border-secondary shadow-sm" style="min-width: 150px;">
                <option value="" {{ (!request('bulan')) ? 'selected' : '' }}>Bulan Ini ({{ $filterBulanAktif ?: 'Otomatis' }})</option>
                <option value="semua" {{ request('bulan') == 'semua' ? 'selected' : '' }}>-- Semua Bulan (Keseluruhan) --</option>
                @foreach($listBulan as $bln)
                    <option value="{{ $bln }}" {{ request('bulan') == $bln ? 'selected' : '' }}>{{ $bln }}</option>
                @endforeach
            </select>
            
            <div class="input-group input-group-sm shadow-sm" style="width: auto;">
                <span class="input-group-text bg-white border-secondary"><i class="bi bi-list-ol text-muted"></i></span>
                <select name="per_page" class="form-select border-secondary text-dark" style="min-width: 80px;">
                    <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15 Baris</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Baris</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Baris</option>
                    <option value="150" {{ request('per_page') == 150 ? 'selected' : '' }}>150 Baris</option>
                    <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200 Baris</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm"><i class="bi bi-funnel-fill me-1"></i> Terapkan</button>
            @if(request('search') || request('jenis') || request('status') || request('metode') || request('bulan') || (request('per_page') && request('per_page') != 15))
                <a href="{{ route('admin.finances.index') }}" class="btn btn-outline-danger btn-sm shadow-sm" title="Reset Semua Filter"><i class="bi bi-x-circle me-1"></i> Reset</a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle border text-sm">
            <thead class="table-light">
                <tr>
                    <th style="width: 40px;" class="text-center">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                    </th>
                    <th style="width: 120px;">Tanggal</th>
                    <th>Kategori & Keterangan</th>
                    <th class="text-center" style="width: 140px;">Jenis Arus</th>
                    <th class="text-end" style="width: 160px;">Nominal (Rp)</th>
                    <th class="text-end" style="width: 160px;">Saldo Akhir</th>
                    <th class="text-center" style="width: 80px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($finances as $item)
<tr>
    <td class="text-center align-middle">
        <input class="form-check-input row-checkbox" type="checkbox" value="{{ $item->id }}">
    </td>
    <td class="small text-nowrap"><i class="bi bi-calendar3 text-muted me-1"></i> {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
    <td>
        <strong class="text-dark d-block">{{ $item->kategori }}</strong>
        
        @if($item->athlete)
            <div class="mt-1 d-inline-flex align-items-center gap-1 bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-0.5 rounded text-xs font-weight-bold">
                <i class="bi bi-person-fill"></i> {{ $item->athlete->nama }} (Bulan: {{ $item->bulan_tagihan }})
            </div>
        @endif
        
        <span class="text-muted small d-block mt-0.5">{{ $item->keterangan ?: '-' }}</span>
    </td>
    <td class="text-center">
        @if($item->jenis == 'pemasukan')
            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1"><i class="bi bi-arrow-down-left me-1"></i>Pemasukan</span>
        @else
            <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1"><i class="bi bi-arrow-up-right me-1"></i>Pengeluaran</span>
        @endif
        {{-- Badge Metode Pembayaran --}}
        @if($item->metode_pembayaran == 'cash')
            <span class="badge bg-secondary bg-opacity-10 text-secondary border mt-1 d-block"><i class="bi bi-cash-stack me-1"></i>Cash</span>
        @elseif($item->metode_pembayaran == 'transfer')
            <span class="badge bg-primary bg-opacity-10 text-primary border mt-1 d-block" title="{{ $item->nama_pengirim_transfer ? 'Atas Nama: '.$item->nama_pengirim_transfer : '' }}">
                <i class="bi bi-bank me-1"></i>Transfer
                @if($item->nama_pengirim_transfer)
                    <small class="d-block text-truncate fw-semibold mt-0.5" style="max-width: 120px;">a.n. {{ $item->nama_pengirim_transfer }}</small>
                @endif
            </span>
        @endif
    </td>
    <td class="text-end font-weight-bold text-nowrap {{ $item->jenis == 'pemasukan' ? 'text-success' : 'text-danger' }}">
        {{ $item->jenis == 'pemasukan' ? '+' : '-' }} Rp {{ number_format($item->nominal, 0, ',', '.') }}
        {{-- Badge status lunas/belum lunas --}}
        @if($item->jenis == 'pemasukan')
            @if($item->status_bayar == 'lunas')
                <span class="badge bg-success d-block mt-1 fw-normal"><i class="bi bi-check-circle me-1"></i>Lunas</span>
            @else
                <span class="badge bg-warning text-dark d-block mt-1 fw-normal"><i class="bi bi-hourglass-split me-1"></i>Belum Lunas</span>
            @endif
        @endif
    </td>
    <td class="text-end font-weight-bold text-dark bg-light text-nowrap">
        Rp {{ number_format($item->saldo_akhir, 0, ',', '.') }}
    </td>
    <td class="text-center">
        <a href="{{ route('admin.finances.edit', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
        <form action="{{ route('admin.finances.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus riwayat transaksi ini?');">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
        </form>
    </td>
</tr>
@empty
<tr><td colspan="7" class="text-center py-5 text-muted">Belum ada transaksi kas yang dicatat.</td></tr>
@endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $finances->links() }}</div>
</div>

<!-- Modal Generate Bulk Tagihan -->
<div class="modal fade" id="modalGenerateBulk" tabindex="-1" aria-labelledby="modalGenerateBulkLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.finances.generate-bulk') }}" method="POST">
                @csrf
                <div class="modal-header bg-warning text-dark border-0">
                    <h5 class="modal-title font-weight-bold" id="modalGenerateBulkLabel"><i class="bi bi-lightning-charge-fill me-2"></i>Buat Tagihan Kas Massal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">Fitur ini akan otomatis membuat tagihan "Belum Lunas" untuk <strong>seluruh siswa</strong> pada bulan yang dipilih. Tagihan ini nantinya akan muncul di Portal Wali untuk mereka bayar.</p>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold text-dark">Nominal Tagihan (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text font-weight-bold bg-light">Rp</span>
                            <input type="number" name="nominal" class="form-control font-weight-bold text-success fs-5" required placeholder="50000" min="1" value="50000">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold text-dark">Untuk Bulan Tagihan <span class="text-danger">*</span></label>
                        <select name="bulan_tagihan" class="form-select border-warning font-weight-bold" required>
                            @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $bulan)
                                <option value="{{ $bulan }} {{ date('Y') }}" {{ $bulan == 'Juli' ? 'selected' : '' }}>{{ $bulan }} {{ date('Y') }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold text-dark">Tanggal Jatuh Tempo <span class="text-muted font-weight-normal">(Opsional)</span></label>
                        <input type="date" name="tanggal_jatuh_tempo" class="form-control">
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning font-weight-bold px-4 text-dark"><i class="bi bi-check2-circle me-1"></i> Generate Tagihan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const selectAll = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const btnBulkDelete = document.getElementById('btnBulkDelete');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');

    function updateBulkDeleteButton() {
        const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
        if (checkedCount > 0) {
            btnBulkDelete.classList.remove('d-none');
            btnBulkDelete.innerHTML = `<i class="bi bi-trash-fill me-1"></i> Hapus Terpilih (${checkedCount})`;
        } else {
            btnBulkDelete.classList.add('d-none');
        }
    }

    if(selectAll) {
        selectAll.addEventListener('change', function() {
            rowCheckboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateBulkDeleteButton();
        });
    }

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const allChecked = document.querySelectorAll('.row-checkbox:checked').length === rowCheckboxes.length;
            selectAll.checked = allChecked;
            updateBulkDeleteButton();
        });
    });

    function confirmBulkDelete() {
        const checkedIds = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
        if (checkedIds.length === 0) return;

        if (confirm(`Apakah Anda yakin ingin menghapus ${checkedIds.length} transaksi yang dipilih?`)) {
            checkedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                bulkDeleteForm.appendChild(input);
            });
            bulkDeleteForm.submit();
        }
    }
</script>
@endpush

@endsection