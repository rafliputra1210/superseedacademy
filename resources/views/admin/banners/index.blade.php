@extends('layouts.admin')
@section('title', 'Pengaturan Banner')

@section('content')
<div class="row g-4">
    <!-- Form Tambah Banner -->
    <div class="col-lg-4">
        <div class="card card-custom">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold text-brand-navy"><i class="bi bi-cloud-arrow-up text-brand-blue me-2"></i>Unggah Banner Baru</h6>
            </div>
            <div class="card-body p-4">
                @if(\App\Models\Banner::count() >= 5)
                    <div class="alert alert-warning border-0 bg-warning-subtle text-warning-emphasis">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Kapasitas maksimal (5 banner) telah tercapai. Hapus banner lama terlebih dahulu untuk menambah yang baru.
                    </div>
                @else
                    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary small">File Gambar (JPG, PNG)</label>
                            <input type="file" name="image" class="form-control" accept="image/png, image/jpeg, image/jpg" required>
                            <div class="form-text text-muted" style="font-size: 0.75rem;">Rekomendasi rasio gambar: 16:9 (Landscape) dengan ukuran maksimal 2MB.</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold" style="background-color: var(--brand-blue); border-color: var(--brand-blue);">
                            Unggah Banner
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Daftar Banner -->
    <div class="col-lg-8">
        <div class="card card-custom">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-brand-navy"><i class="bi bi-images text-brand-blue me-2"></i>Daftar Banner Beranda</h6>
                <span class="badge bg-brand-light text-brand-blue">{{ $banners->count() }} / 5 Banner</span>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-secondary fw-semibold small">Gambar</th>
                                <th class="text-secondary fw-semibold small text-center">Status</th>
                                <th class="text-secondary fw-semibold small text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($banners as $banner)
                            <tr>
                                <td style="width: 60%;">
                                    <div class="rounded overflow-hidden border shadow-sm bg-light d-flex align-items-center justify-content-center" style="width: 100%; height: 120px;">
                                        <img src="{{ storage_img_url($banner->image_path) }}" alt="Banner" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    </div>
                                </td>
                                <td class="text-center" style="width: 20%;">
                                    <form action="{{ route('admin.banners.toggle', $banner->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $banner->is_active ? 'btn-success' : 'btn-secondary' }} rounded-pill px-3 shadow-sm" style="font-size: 0.75rem; font-weight: 600;">
                                            @if($banner->is_active)
                                                <i class="bi bi-check-circle me-1"></i> Aktif
                                            @else
                                                <i class="bi bi-x-circle me-1"></i> Non-Aktif
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td class="text-center" style="width: 20%;">
                                    <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus banner ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5 text-secondary">
                                    <i class="bi bi-image fs-1 d-block mb-3 opacity-25"></i>
                                    Belum ada banner yang diunggah.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
