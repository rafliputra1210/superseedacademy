@extends('layouts.wali')
@section('title', 'Rekapitulasi Absensi Anak')

@section('content')
@if(!$athlete)
    <div class="alert alert-warning text-center p-5 card-custom">Belum ada data anak terhubung dengan akun Anda.</div>
@else

@if($myAthletes->count() > 1)
<div class="mb-4 bg-white p-3 rounded-4 shadow-sm d-flex align-items-center justify-content-between border flex-wrap gap-2 hover-elevate">
    <span class="small fw-semibold"><i class="bi bi-person-check-fill text-brand-green me-2"></i>Menampilkan Absensi Latihan untuk: <strong>{{ $athlete->nama }}</strong></span>
    <form action="{{ route('wali.absensi') }}" method="GET" class="d-flex gap-2">
        <select name="child_id" class="form-select form-select-sm border-success-subtle fw-medium" onchange="this.form.submit()">
            @foreach($myAthletes as $child)
                <option value="{{ $child->id }}" {{ ($athlete->id == $child->id) ? 'selected' : '' }}>{{ $child->nama }} ({{ $child->kelompok_umur ?? 'KU -' }})</option>
            @endforeach
        </select>
    </form>
</div>
@endif

<div class="card card-custom p-4 mb-4 hover-elevate">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <h6 class="fw-bold text-brand-navy mb-0"><i class="bi bi-calendar-check-fill text-brand-green me-2"></i>Rekapitulasi Kehadiran: <span class="text-brand-green">{{ $athlete->nama }}</span></h6>
        <span class="badge bg-brand-light text-brand-navy border">Posisi: {{ $athlete->posisi_bermain ?? 'Siswa' }}</span>
    </div>

    <div class="row g-3">
        <div class="col-6 col-md-3">
            <div class="p-3 bg-brand-green text-white text-center rounded-4 shadow-sm transition hover-elevate">
                <span class="text-xs text-uppercase opacity-75 fw-bold d-block" style="letter-spacing: 0.5px;">Hadir Latihan</span>
                <h2 class="mb-0 mt-1 fw-bold">{{ $rekapAbsen['hadir'] }} <span class="fs-6 fw-normal">Kali</span></h2>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 text-dark text-center rounded-4 shadow-sm transition hover-elevate" style="background-color: #38bdf8;">
                <span class="text-xs text-uppercase opacity-75 fw-bold d-block" style="letter-spacing: 0.5px;">Izin</span>
                <h2 class="mb-0 mt-1 fw-bold">{{ $rekapAbsen['izin'] }} <span class="fs-6 fw-normal">Kali</span></h2>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 text-dark text-center rounded-4 shadow-sm transition hover-elevate" style="background-color: var(--brand-gold);">
                <span class="text-xs text-uppercase opacity-75 fw-bold d-block" style="letter-spacing: 0.5px;">Sakit</span>
                <h2 class="mb-0 mt-1 fw-bold">{{ $rekapAbsen['sakit'] }} <span class="fs-6 fw-normal">Kali</span></h2>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 text-white text-center rounded-4 shadow-sm transition hover-elevate" style="background-color: #ef4444;">
                <span class="text-xs text-uppercase opacity-75 fw-bold d-block" style="letter-spacing: 0.5px;">Alpa (Tanpa Keterangan)</span>
                <h2 class="mb-0 mt-1 fw-bold">{{ $rekapAbsen['alpa'] }} <span class="fs-6 fw-normal">Kali</span></h2>
            </div>
        </div>
    </div>
</div>

<div class="card card-custom p-4 hover-elevate">
    <h6 class="fw-bold mb-3 text-brand-navy"><i class="bi bi-clock-history text-brand-green me-2"></i>Riwayat Kehadiran Harian</h6>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle border text-sm">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>Tanggal Latihan</th>
                    <th class="text-center">Status Kehadiran</th>
                    <th>Kode Barcode Bukti</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $absen)
                <tr>
                    <td class="text-center font-weight-bold">{{ $loop->iteration }}</td>
                    <td class="font-weight-bold text-dark">
                        <i class="bi bi-calendar3 text-muted me-2"></i>{{ \Carbon\Carbon::parse($absen->tanggal)->format('d F Y') }}
                    </td>
                    <td class="text-center">
                        @if($absen->status == 'hadir') <span class="badge bg-success px-3 py-1 font-weight-bold">Hadir di Lapangan</span>
                        @elseif($absen->status == 'izin') <span class="badge bg-info text-dark px-3 py-1 font-weight-bold">Izin</span>
                        @elseif($absen->status == 'sakit') <span class="badge bg-warning text-dark px-3 py-1 font-weight-bold">Sakit</span>
                        @else <span class="badge bg-danger px-3 py-1 font-weight-bold">Alpa</span> @endif
                    </td>
                    <td>
                        <code class="text-xs bg-light px-2 py-1 border rounded text-dark font-weight-bold">{{ $absen->kode_barcode }}</code>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-5 text-muted">
                        <i class="bi bi-folder2-open fs-2 d-block mb-1"></i>
                        Belum ada riwayat absensi yang dicatat oleh pelatih/admin.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $attendances->links() }}</div>
</div>
@endif
@endsection