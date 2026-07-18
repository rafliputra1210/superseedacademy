@extends('layouts.wali')

@section('content')
<!-- Pilihan Ganti Anak (Jika Punya Lebih dari 1 Anak di Akademi) -->
@if($myAthletes->count() > 1)
<div class="mb-4 bg-brand-light border border-blue-200 p-4 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-3 shadow-sm">
    <div class="d-flex align-items-center">
        <i class="bi bi-people-fill fs-4 text-brand-blue me-3"></i>
        <span class="small fw-semibold text-brand-navy">Anda memiliki {{ $myAthletes->count() }} anak terdaftar. Pilih profil anak:</span>
    </div>
    <form action="{{ route('wali.dashboard') }}" method="GET" class="d-flex gap-2">
        <select name="child_id" class="form-select form-select-sm fw-medium border-primary-subtle text-brand-navy" onchange="this.form.submit()" style="min-width: 200px;">
            @foreach($myAthletes as $child)
                <option value="{{ $child->id }}" {{ ($athlete && $athlete->id == $child->id) ? 'selected' : '' }}>
                    {{ $child->nama }} (U-{{ $child->nomor_punggung ?? 'XX' }})
                </option>
            @endforeach
        </select>
    </form>
</div>
@endif

@if(!$athlete)
<div class="alert bg-white text-center p-5 card-custom border-top border-4" style="border-top-color: #f59e0b !important;">
    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
        <i class="bi bi-exclamation-triangle-fill fs-1 text-warning"></i>
    </div>
    <h5 class="fw-bold text-dark">Belum Ada Data Anak Terhubung</h5>
    <p class="text-secondary mb-0">Akun Anda belum dikaitkan dengan data atlet manapun oleh Admin. Silahkan hubungi sekretariat admin Superseed Academy.</p>
</div>
@else
<div class="row g-4">
    <!-- Kolom Kiri: Kartu Profil & Biodata Siswa -->
    <div class="col-md-5 col-lg-4">
        <div class="card card-custom p-4 text-center border-top border-4 hover-elevate" style="border-top-color: var(--brand-green) !important;">

            {{-- 1. FOTO AKADEMISI --}}
            <div class="position-relative d-inline-block mx-auto mb-3">
                @if($athlete->foto)
                    <img src="{{ storage_img_url($athlete->foto) }}" class="rounded-circle shadow-sm border border-4 border-white" width="130" height="130" style="object-fit: cover; background: #f8fafc;">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($athlete->nama) }}&background=d1fae5&color=047857&size=256" class="rounded-circle shadow-sm border border-4 border-white" width="130" height="130">
                @endif
            </div>

            {{-- 2. NAMA AKADEMISI --}}
            <h5 class="fw-bold text-brand-navy mb-1">{{ $athlete->nama }}</h5>
            <span class="badge bg-brand-light text-brand-green mb-3 px-3 py-1 fw-bold rounded-pill border border-success-subtle">
                Siswa Superseed
            </span>

            {{-- INFO DETAIL --}}
            <ul class="list-group list-group-flush text-start small mb-3">

                {{-- 3. TANGGAL LAHIR --}}
                <li class="list-group-item px-0 d-flex justify-content-between align-items-center py-2">
                    <span class="text-secondary d-flex align-items-center gap-1">
                        <i class="bi bi-calendar3 text-brand-green"></i> Tanggal Lahir
                    </span>
                    <strong class="text-brand-navy">
                        {{ $athlete->tanggal_lahir ? \Carbon\Carbon::parse($athlete->tanggal_lahir)->format('d M Y') : '-' }}
                    </strong>
                </li>

                {{-- 4. POSISI BERMAIN --}}
                <li class="list-group-item px-0 d-flex justify-content-between align-items-center py-2">
                    <span class="text-secondary d-flex align-items-center gap-1">
                        <i class="bi bi-person-badge text-brand-green"></i> Posisi Bermain
                    </span>
                    <strong class="text-brand-navy">{{ $athlete->posisi_bermain ?? '-' }}</strong>
                </li>

                {{-- 5. NOMOR PUNGGUNG --}}
                <li class="list-group-item px-0 d-flex justify-content-between align-items-center py-2">
                    <span class="text-secondary d-flex align-items-center gap-1">
                        <i class="bi bi-hash text-brand-green"></i> Nomor Punggung
                    </span>
                    <span class="badge fw-bold fs-6 px-3 py-1 rounded-pill" style="background-color: #d1fae5; color: #047857; border: 1px solid #6ee7b7;">
                        #{{ $athlete->nomor_punggung ?? '-' }}
                    </span>
                </li>

                {{-- 6. ALAMAT --}}
                <li class="list-group-item px-0 pt-2 pb-1">
                    <span class="text-secondary d-flex align-items-center gap-1 mb-1">
                        <i class="bi bi-geo-alt text-brand-green"></i> Alamat Domisili
                    </span>
                    <strong class="text-brand-navy d-block lh-sm ps-1">{{ $athlete->alamat ?: '-' }}</strong>
                </li>

            </ul>

            {{-- 7. TOMBOL ABSENSI & RAPORT --}}
            <div class="pt-3 border-top d-flex gap-2">
                <a href="{{ route('wali.absensi', ['child_id' => $athlete->id]) }}" class="btn btn-outline-success btn-sm w-50 fw-semibold rounded-3">
                    <i class="bi bi-calendar-check me-1"></i> Absensi
                </a>
                <a href="{{ route('wali.raport', ['child_id' => $athlete->id]) }}" class="btn btn-success btn-sm w-50 fw-semibold rounded-3 shadow-sm" style="background-color: var(--brand-green); border-color: var(--brand-green);">
                    <i class="bi bi-award-fill me-1"></i> Raport
                </a>
            </div>

        </div>

    </div>

    <!-- Kolom Kanan: Pengumuman Terbaru & Info Latihan -->
    <div class="col-md-7 col-lg-8">
        <div class="card card-custom p-4 h-100 hover-elevate">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <h6 class="fw-bold mb-0 text-brand-navy"><i class="bi bi-megaphone-fill text-brand-gold me-2"></i>Pengumuman Akademi</h6>
                <a href="{{ route('wali.pengumuman') }}" class="text-xs text-brand-green text-decoration-none fw-bold">Lihat Semua &rarr;</a>
            </div>

            <div class="d-flex flex-column gap-3">
                @forelse($announcements as $info)
                <div class="p-4 bg-white rounded-3 border shadow-sm transition hover-elevate" style="border-color: #e2e8f0; border-left: 4px solid var(--brand-green) !important;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-brand-light text-brand-green border border-success-subtle rounded-pill" style="font-size: 0.7rem;">Info Resmi</span>
                        <small class="text-secondary" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($info->tanggal)->format('d M Y') }}</small>
                    </div>
                    <h6 class="fw-bold text-brand-navy mt-1 mb-2">{{ $info->judul }}</h6>
                    <p class="text-secondary small mb-0 lh-base" style="white-space: pre-line;">{{ Str::limit($info->konten, 180) }}</p>
                </div>
                @empty
                <div class="text-center py-5 text-secondary bg-light rounded-3 border border-dashed">
                    <i class="bi bi-inbox fs-1 d-block mb-2 text-black-50 opacity-50"></i>
                    Belum ada pengumuman terbaru.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endif
@endsection