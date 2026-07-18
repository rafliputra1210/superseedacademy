@extends('layouts.wali')

@section('content')
<div class="card card-custom p-4 mb-4 text-white hover-elevate" style="background: linear-gradient(135deg, var(--brand-navy) 0%, #1e293b 100%) !important; border: none;">
    <h5 class="fw-bold mb-1"><i class="bi bi-megaphone-fill text-brand-gold me-2"></i>Pusat Informasi & Pengumuman</h5>
    <p class="text-white-50 small mb-0" style="letter-spacing: 0.5px;">Ikuti terus informasi jadwal latihan, libur hari raya, dan agenda turnamen Superseed Academy.</p>
</div>

<div class="row g-4">
    @forelse($announcements as $info)
    <div class="col-12">
        <div class="card card-custom p-4 border-start border-4 hover-elevate" style="border-left-color: var(--brand-green) !important;">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <span class="badge bg-brand-light text-brand-green px-3 py-2 fw-bold border border-success-subtle rounded-pill">Informasi Resmi Akademi</span>
                <span class="text-muted small fw-medium"><i class="bi bi-calendar3 me-1"></i> Diterbitkan: {{ \Carbon\Carbon::parse($info->tanggal)->format('d F Y') }}</span>
            </div>
            <h5 class="fw-bold text-brand-navy mt-1">{{ $info->judul }}</h5>
            <p class="text-secondary mt-2 mb-0" style="white-space: pre-line; line-height: 1.6;">{{ $info->konten }}</p>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 bg-white rounded card-custom text-muted">
        <i class="bi bi-bell-slash fs-1 d-block mb-2 text-secondary"></i>
        Belum ada pengumuman yang diterbitkan oleh akademi.
    </div>
    @endforelse
</div>

<div class="mt-4">{{ $announcements->links() }}</div>
@endsection