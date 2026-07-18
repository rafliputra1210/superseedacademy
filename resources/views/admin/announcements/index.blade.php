@extends('layouts.admin')
@section('title', 'Manajemen Pengumuman')

@section('content')
<div class="card card-custom bg-white p-4 shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h5 class="mb-1 font-weight-bold text-dark"><i class="bi bi-megaphone-fill text-warning me-2"></i>Pusat Pengumuman Akademi</h5>
            <p class="text-muted small mb-0">Pesan yang diterbitkan di sini akan dapat dibaca oleh seluruh Wali Murid.</p>
        </div>
        <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary btn-sm font-weight-bold px-3">
            <i class="bi bi-plus-circle me-1"></i> Buat Pengumuman Baru
        </a>
    </div>

    <div class="row g-3">
        @forelse($announcements as $item)
        <div class="col-md-6">
            <div class="card border h-100 p-4 shadow-sm rounded-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-secondary' }} px-3 py-1">
                        {{ $item->is_active ? '🟢 Tayang di Portal' : '⚪ Di-nonaktifkan' }}
                    </span>
                    <small class="text-muted"><i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</small>
                </div>
                <h5 class="font-weight-bold text-dark mt-2 mb-2">{{ $item->judul }}</h5>
                <p class="text-muted small mb-4 flex-grow-1" style="white-space: pre-line; line-height: 1.6;">{{ Str::limit($item->konten, 160) }}</p>
                
                <div class="border-top pt-3 text-end">
                    <form action="{{ route('admin.announcements.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pengumuman ini?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i> Hapus</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5 text-muted">
            <i class="bi bi-bell-slash fs-1 d-block mb-2 text-secondary opacity-50"></i>
            Belum ada pengumuman yang dibuat oleh Admin.
        </div>
        @endforelse
    </div>
    <div class="mt-4">{{ $announcements->links() }}</div>
</div>
@endsection