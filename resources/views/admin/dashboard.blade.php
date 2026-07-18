@extends('layouts.admin')
@section('title', 'Dashboard Statistik')

@section('content')
<div class="row g-4 mb-4">
    <!-- Stat Card 1 -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-4 h-100 d-flex flex-row justify-content-between align-items-center">
            <div>
                <h6 class="text-uppercase mb-2 text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Atlet Terdaftar</h6>
                <h2 class="mb-0 fw-black text-brand-navy">{{ \App\Models\Athlete::count() }} <span class="fs-6 text-secondary fw-normal">Siswa</span></h2>
            </div>
            <div class="bg-brand-light text-brand-blue rounded p-3 d-flex align-items-center justify-content-center">
                <i class="bi bi-people-fill fs-3"></i>
            </div>
        </div>
    </div>
    
    <!-- Stat Card 2 -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-4 h-100 d-flex flex-row justify-content-between align-items-center">
            <div>
                <h6 class="text-uppercase mb-2 text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Coach</h6>
                <h2 class="mb-0 fw-black text-brand-navy">{{ \App\Models\Coach::count() }} <span class="fs-6 text-secondary fw-normal">Pelatih</span></h2>
            </div>
            <div class="bg-brand-light text-brand-blue rounded p-3 d-flex align-items-center justify-content-center">
                <i class="bi bi-person-badge-fill fs-3"></i>
            </div>
        </div>
    </div>
    
    <!-- Stat Card 3 -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-4 h-100 d-flex flex-row justify-content-between align-items-center">
            <div>
                <h6 class="text-uppercase mb-2 text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Saldo Uang Kas</h6>
                @php
                    $pemasukan = \App\Models\Finance::where('jenis', 'pemasukan')->sum('nominal');
                    $pengeluaran = \App\Models\Finance::where('jenis', 'pengeluaran')->sum('nominal');
                    $totalSaldo = $pemasukan - $pengeluaran;
                @endphp
                <h4 class="mb-0 fw-black text-brand-navy">Rp {{ number_format($totalSaldo, 0, ',', '.') }}</h4>
            </div>
            <div class="bg-brand-light text-brand-blue rounded p-3 d-flex align-items-center justify-content-center">
                <i class="bi bi-wallet2 fs-3"></i>
            </div>
        </div>
    </div>
    
    <!-- Stat Card 4 -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-4 h-100 d-flex flex-row justify-content-between align-items-center">
            <div>
                <h6 class="text-uppercase mb-2 text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Pengumuman Aktif</h6>
                <h2 class="mb-0 fw-black text-brand-navy">{{ \App\Models\Announcement::where('is_active', true)->count() }}</h2>
            </div>
            <div class="bg-brand-light text-brand-blue rounded p-3 d-flex align-items-center justify-content-center">
                <i class="bi bi-megaphone-fill fs-3"></i>
            </div>
        </div>
    </div>
</div>

<!-- Welcome Banner -->
<div class="card card-custom overflow-hidden border-0 shadow-sm">
    <div class="row g-0">
        <div class="col-md-8 p-4 p-md-5 d-flex flex-column justify-content-center">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-brand-blue text-white rounded p-2 me-3 d-inline-flex">
                    <i class="bi bi-stars fs-5"></i>
                </div>
                <h4 class="mb-0 fw-bold text-brand-navy">Selamat Datang di Portal Admin!</h4>
            </div>
            <p class="text-secondary mb-4 leading-relaxed" style="font-size: 0.95rem;">
                Sistem Informasi Manajemen Super Seed Academy. Gunakan menu navigasi di sebelah kiri untuk mengelola <strong>Data Atlet</strong>, profil pelatih berlisensi, pencatatan absensi, hingga pengelolaan keuangan dan raport siswa secara terpadu.
            </p>
            <div>
                <a href="{{ route('admin.athletes.index') }}" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm" style="background-color: #0066FF; border-color: #0066FF;">
                    Kelola Data Atlet <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
        <div class="col-md-4 bg-light d-none d-md-flex align-items-center justify-content-center" style="background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 20px 20px;">
            <i class="bi bi-shield-check text-white" style="font-size: 8rem; text-shadow: 0 10px 30px rgba(0, 102, 255, 0.2); color: #0066FF !important;"></i>
        </div>
    </div>
</div>
@endsection