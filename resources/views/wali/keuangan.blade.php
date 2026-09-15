@extends('layouts.wali')

@section('content')
<div class="card card-custom p-4 mb-4 text-white hover-elevate" style="background: linear-gradient(135deg, var(--brand-green) 0%, #064e3b 100%) !important; border: none;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <span class="text-uppercase text-xs tracking-wider opacity-75 font-weight-bold" style="letter-spacing: 1px;">Transparansi Keuangan</span>
            <h4 class="mb-0 mt-1 fw-bold">Laporan Uang Kas Superseed Academy</h4>
        </div>
        <div class="text-end">
            <span class="d-block text-xs opacity-75">Saldo Akhir Kas Akademi Saat Ini:</span>
            <h2 class="mb-0 fw-bold text-warning">Rp {{ number_format($saldoSekarang, 0, ',', '.') }}</h2>
        </div>
    </div>
</div>

<!-- Ringkasan Kas -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card card-custom p-4 bg-white border-start border-4 d-flex flex-row justify-content-between align-items-center hover-elevate" style="border-left-color: #10b981 !important;">
            <div>
                <span class="text-xs text-muted fw-bold text-uppercase" style="letter-spacing: 0.5px;">Total Seluruh Pemasukan</span>
                <h4 class="mb-0 fw-bold mt-1" style="color: #10b981;">+ Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h4>
            </div>
            <i class="bi bi-arrow-down-left-circle-fill fs-1 opacity-25" style="color: #10b981;"></i>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-custom p-4 bg-white border-start border-4 d-flex flex-row justify-content-between align-items-center hover-elevate" style="border-left-color: #ef4444 !important;">
            <div>
                <span class="text-xs text-muted fw-bold text-uppercase" style="letter-spacing: 0.5px;">Total Seluruh Pengeluaran</span>
                <h4 class="mb-0 fw-bold mt-1" style="color: #ef4444;">- Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h4>
            </div>
            <i class="bi bi-arrow-up-right-circle-fill fs-1 opacity-25" style="color: #ef4444;"></i>
        </div>
    </div>
</div>

<!-- Filter Anak (Jika Lebih Dari 1) -->
@if(isset($athlete))
    @if($myAthletes->count() > 1)
    <div class="mb-4 bg-white p-3 rounded-4 shadow-sm d-flex align-items-center justify-content-between border flex-wrap gap-2 hover-elevate">
        <span class="small fw-semibold"><i class="bi bi-person-check-fill text-brand-green me-2"></i>Menampilkan Riwayat Pembayaran untuk: <strong>{{ $athlete->nama }}</strong></span>
        <form action="{{ route('wali.keuangan') }}" method="GET" class="d-flex gap-2">
            <select name="child_id" class="form-select form-select-sm fw-medium border-success-subtle text-brand-navy" onchange="this.form.submit()" style="min-width: 200px;">
                @foreach($myAthletes as $child)
                    <option value="{{ $child->id }}" {{ ($athlete->id == $child->id) ? 'selected' : '' }}>{{ $child->nama }}</option>
                @endforeach
            </select>
        </form>
    </div>
    @endif

    {{-- TIGA CARD RINGKASAN TAGIHAN SISWA --}}
    <div class="row g-3 mb-4">

        {{-- Card 1: Total Tagihan --}}
        <div class="col-md-4">
            <div class="card card-custom p-4 border-0 hover-elevate h-100" style="background: linear-gradient(135deg, #1e3a5f 0%, #0f2942 100%);">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-xs fw-bold text-uppercase opacity-75 text-white" style="letter-spacing: 0.8px;">Total Tagihan</span>
                        <p class="text-white opacity-60 small mb-0 mt-1">Seluruh tagihan tahun berjalan</p>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:rgba(255,255,255,0.12);">
                        <i class="bi bi-receipt text-white fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-white">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</h3>
                <small class="text-white opacity-50 mt-2 d-block">Kas bulanan + biaya lain atas nama {{ $athlete->nama }}</small>
            </div>
        </div>

        {{-- Card 2: Total Terbayar --}}
        <div class="col-md-4">
            <div class="card card-custom p-4 border-0 hover-elevate h-100" style="background: linear-gradient(135deg, #065f46 0%, #047857 100%);">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-xs fw-bold text-uppercase opacity-75 text-white" style="letter-spacing: 0.8px;">Total Terbayar</span>
                        <p class="text-white opacity-60 small mb-0 mt-1">Sudah diterima oleh akademi</p>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:rgba(255,255,255,0.15);">
                        <i class="bi bi-check-circle-fill text-white fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-white">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</h3>
                @if($totalTagihan > 0)
                    @php $persen = round(($totalTerbayar / $totalTagihan) * 100); @endphp
                    <div class="mt-2">
                        <div class="progress" style="height:5px;background:rgba(255,255,255,0.2);">
                            <div class="progress-bar bg-white" style="width:{{ $persen }}%;"></div>
                        </div>
                        <small class="text-white opacity-60 mt-1 d-block">{{ $persen }}% dari total tagihan</small>
                    </div>
                @endif
            </div>
        </div>

        {{-- Card 3: Belum Terbayar --}}
        <div class="col-md-4">
            <div class="card card-custom p-4 border-0 hover-elevate h-100" style="background: {{ $totalBelumBayar > 0 ? 'linear-gradient(135deg, #7f1d1d 0%, #b91c1c 100%)' : 'linear-gradient(135deg, #374151 0%, #4b5563 100%)' }};">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-xs fw-bold text-uppercase opacity-75 text-white" style="letter-spacing: 0.8px;">Belum Terbayar</span>
                        <p class="text-white opacity-60 small mb-0 mt-1">Tagihan yang belum dilunasi</p>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:rgba(255,255,255,0.15);">
                        <i class="bi bi-exclamation-triangle-fill text-white fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-white">Rp {{ number_format($totalBelumBayar, 0, ',', '.') }}</h3>
                <small class="text-white opacity-50 mt-2 d-block">
                    @if($totalBelumBayar > 0)
                        ⚠️ Harap segera dilunasi ke akademi
                    @else
                        ✅ Semua tagihan sudah lunas
                    @endif
                </small>
            </div>
        </div>

    </div>

    {{-- Tabel Riwayat Pembayaran Anak (Individu) --}}
    <div class="card card-custom p-4 mb-4 hover-elevate">
        <h6 class="fw-bold mb-3 text-brand-navy"><i class="bi bi-person-vcard text-brand-green me-2"></i>Riwayat Pembayaran: <span class="text-brand-green">{{ $athlete->nama }}</span></h6>

        <div class="table-responsive">
            <table class="table table-hover align-middle border text-sm">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori Pembayaran</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Nominal (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatPembayaran as $bayar)
                    <tr class="{{ $bayar->status_bayar === 'belum_lunas' ? 'table-warning' : '' }}">
                        <td class="small text-nowrap">{{ \Carbon\Carbon::parse($bayar->tanggal)->format('d M Y') }}</td>
                        <td>
                            <strong class="text-dark d-block">{{ $bayar->kategori }}</strong>
                            <span class="text-muted small">{{ $bayar->keterangan ?: '-' }}</span>
                            @if($bayar->status_bayar === 'belum_lunas' && $bayar->tanggal_jatuh_tempo)
                                <br><small class="text-danger fw-semibold"><i class="bi bi-calendar-x me-1"></i>Jatuh tempo: {{ \Carbon\Carbon::parse($bayar->tanggal_jatuh_tempo)->format('d M Y') }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($bayar->status_bayar === 'lunas')
                                <span class="badge bg-success px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i>Lunas</span>
                            @else
                                <span class="badge bg-danger px-2 py-1"><i class="bi bi-exclamation-circle-fill me-1"></i>Belum Lunas</span>
                            @endif
                        </td>
                        <td class="text-end font-weight-bold text-nowrap {{ $bayar->status_bayar === 'lunas' ? 'text-success' : 'text-danger' }}">
                            {{ $bayar->status_bayar === 'lunas' ? '+' : '' }} {{ number_format($bayar->nominal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada riwayat pembayaran untuk siswa ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

<!-- Tabel Transaksi Kas (Global) -->
<div class="card card-custom p-4">
    <h6 class="font-weight-bold mb-3 text-dark"><i class="bi bi-journal-text me-2 text-success"></i>Buku Riwayat Arus Kas (Terbuka & Transparan)</h6>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle border text-sm">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Kategori / Keterangan</th>
                    <th class="text-center">Jenis</th>
                    <th class="text-end">Nominal</th>
                    <th class="text-end">Saldo Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($finances as $kas)
                <tr>
                    <td class="small text-nowrap">{{ \Carbon\Carbon::parse($kas->tanggal)->format('d M Y') }}</td>
                    <td>
                        <strong class="text-dark d-block">{{ $kas->kategori }}</strong>
                        @php
                            $isMyChild = $kas->athlete_id && $myAthletes->pluck('id')->contains($kas->athlete_id);
                            $displayKeterangan = $kas->keterangan ?: '-';
                            if (!$isMyChild && $kas->athlete_id && !empty($kas->keterangan)) {
                                $displayKeterangan = preg_replace('/a\.n\.\s*.+$/i', 'a.n. Siswa Akademi (Privasi Terlindungi)', $kas->keterangan);
                            }
                        @endphp
                        <span class="text-muted small">{{ $displayKeterangan }}</span>
                    </td>
                    <td class="text-center">
                        @if($kas->jenis == 'pemasukan')
                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1"><i class="bi bi-arrow-down-left me-1"></i>Pemasukan</span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1"><i class="bi bi-arrow-up-right me-1"></i>Pengeluaran</span>
                        @endif
                    </td>
                    <td class="text-end font-weight-bold text-nowrap {{ $kas->jenis == 'pemasukan' ? 'text-success' : 'text-danger' }}">
                        {{ $kas->jenis == 'pemasukan' ? '+' : '-' }} Rp {{ number_format($kas->nominal, 0, ',', '.') }}
                    </td>
                    <td class="text-end font-weight-bold text-dark bg-light text-nowrap">
                        Rp {{ number_format($kas->saldo_akhir, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada catatan transaksi keuangan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $finances->links() }}</div>
</div>
@endsection