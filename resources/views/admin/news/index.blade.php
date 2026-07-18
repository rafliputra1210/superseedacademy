@extends('layouts.admin')
@section('title', 'Manajemen Berita & Liputan')

@section('content')
<div class="card card-custom bg-white p-4 shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h5 class="mb-1 font-weight-bold text-dark"><i class="bi bi-newspaper text-primary me-2"></i>Berita & Artikel Akademi</h5>
            <p class="text-muted small mb-0">Kelola liputan turnamen, artikel kepelatihan, atau berita prestasi Superseed Academy.</p>
        </div>
        <a href="{{ route('admin.news.create') }}" class="btn btn-primary btn-sm font-weight-bold px-3 shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Tulis Berita Baru
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle border text-sm">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;" class="text-center">#</th>
                    <th style="width: 100px;">Foto</th>
                    <th>Judul Berita & Kategori</th>
                    <th style="width: 140px;">Tanggal Tayang</th>
                    <th class="text-center" style="width: 120px;">Status</th>
                    <th class="text-center" style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($news as $item)
                <tr>
                    <td class="text-center font-weight-bold">{{ $loop->iteration }}</td>
                    <td>
                        @if($item->foto)
                            <img src="{{ storage_img_url($item->foto) }}" class="rounded shadow-sm" width="80" height="50" style="object-fit: cover;" alt="Foto Berita">
                        @else
                            <div class="bg-secondary bg-opacity-25 rounded d-flex align-items-center justify-content-center text-muted" style="width: 80px; height: 50px;">
                                <i class="bi bi-image fs-5"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-primary bg-opacity-10 text-primary mb-1 font-weight-bold">{{ $item->kategori }}</span>
                        <h6 class="font-weight-bold text-dark mb-0">{{ $item->judul }}</h6>
                        <small class="text-muted">{{ Str::limit(strip_tags($item->konten), 70) }}</small>
                    </td>
                    <td class="small text-nowrap"><i class="bi bi-calendar3 text-muted me-1"></i> {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                    <td class="text-center">
                        @if($item->is_active)
                            <span class="badge bg-success px-2 py-1"><i class="bi bi-eye-fill me-1"></i> Tayang</span>
                        @else
                            <span class="badge bg-secondary px-2 py-1"><i class="bi bi-eye-slash-fill me-1"></i> Draft / Arsip</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <form action="{{ route('admin.news.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus berita ini?');">
                            <a href="{{ route('landing.news.detail', $item->slug) }}" target="_blank" class="btn btn-sm btn-outline-info me-1" title="Lihat di Web Publik"><i class="bi bi-box-arrow-up-right"></i></a>
                            <a href="{{ route('admin.news.edit', $item) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit Berita"><i class="bi bi-pencil-square"></i></a>
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada berita yang diterbitkan oleh Admin.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $news->links() }}</div>
</div>
@endsection