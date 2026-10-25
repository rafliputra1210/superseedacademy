@extends('layouts.admin')

@section('title', 'Kategori Penilaian Raport')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
        <div>
            <h4 class="fw-bold text-brand-navy mb-1">
                <i class="bi bi-sliders2 text-brand-blue me-2"></i>Kategori Penilaian Raport
            </h4>
            <p class="text-secondary small mb-0">
                Atur aspek atau kategori penilaian yang akan diisi oleh Asesor dan direkap pada Raport Murid.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali ke Rekap Raport</span>
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Form Tambah Kategori -->
        <div class="col-lg-4">
            <div class="card card-custom shadow-sm border-0 sticky-top" style="top: 80px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-brand-navy">
                        <i class="bi bi-plus-circle text-primary me-2"></i>Tambah Kategori Baru
                    </h6>
                </div>
                <div class="card-body p-3 p-md-4">
                    <form action="{{ route('admin.assessment_categories.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control form-control-sm @error('nama') is-invalid @enderror" 
                                   placeholder="Contoh: Disiplin & Sikap" value="{{ old('nama') }}" required>
                            @error('nama')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">Deskripsi / Indikator Penilaian</label>
                            <textarea name="deskripsi" class="form-control form-control-sm @error('deskripsi') is-invalid @enderror" 
                                      rows="3" placeholder="Contoh: Kehadiran tepat waktu, kepatuhan instruksi, sportivitas...">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                            <div class="form-text text-muted" style="font-size: 0.75rem;">
                                Menjadi panduan asesor saat memberikan nilai skala 0 - 100.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">Urutan Tampil</label>
                            <input type="number" name="urutan" class="form-control form-control-sm" 
                                   value="{{ old('urutan', ($categories->max('urutan') ?? 0) + 1) }}" min="1">
                        </div>

                        <div class="mb-4 form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActiveCheck" value="1" checked>
                            <label class="form-check-label small fw-semibold text-dark" for="isActiveCheck">
                                Aktifkan kategori ini langsung
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-save"></i>
                            <span>Simpan Kategori</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabel Daftar Kategori -->
        <div class="col-lg-8">
            <div class="card card-custom shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-brand-navy">Daftar Kategori Penilaian Aktif</h6>
                        <small class="text-muted">Total: {{ $categories->count() }} kategori terdaftar</small>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background-color: #f8fafc; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">
                                <tr>
                                    <th class="ps-4 py-3" style="width: 70px;">Urutan</th>
                                    <th class="py-3">Kategori & Aspek</th>
                                    <th class="py-3 text-center" style="width: 120px;">Status</th>
                                    <th class="pe-4 py-3 text-end" style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge bg-light text-dark border px-2 py-1 font-monospace">
                                            #{{ $category->urutan }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-6">{{ $category->nama }}</div>
                                        <div class="text-muted small mt-0.5">
                                            {{ $category->deskripsi ?: 'Tidak ada keterangan indikator.' }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.assessment_categories.toggle', $category->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm border-0 p-0" title="Klik untuk ubah status">
                                                @if($category->is_active)
                                                    <span class="badge rounded-pill px-2.5 py-1" style="background-color: #dcfce7; color: #15803d; font-weight: 600;">
                                                        <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                                    </span>
                                                @else
                                                    <span class="badge rounded-pill px-2.5 py-1" style="background-color: #fee2e2; color: #b91c1c; font-weight: 600;">
                                                        <i class="bi bi-x-circle-fill me-1"></i> Non-Aktif
                                                    </span>
                                                @endif
                                            </button>
                                        </form>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-inline-flex gap-1">
                                            <!-- Edit Button -->
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editModal{{ $category->id }}"
                                                    title="Edit Kategori">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <!-- Delete Button -->
                                            <form action="{{ route('admin.assessment_categories.destroy', $category->id) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Hapus kategori penilaian {{ $category->nama }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Kategori">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Modal Edit -->
                                        <div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header border-bottom">
                                                        <h6 class="modal-title fw-bold text-brand-navy">
                                                            <i class="bi bi-pencil-square text-primary me-2"></i>Edit Kategori Penilaian
                                                        </h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('admin.assessment_categories.update', $category->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body p-4">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold text-dark small">Nama Kategori <span class="text-danger">*</span></label>
                                                                <input type="text" name="nama" class="form-control" value="{{ $category->nama }}" required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold text-dark small">Deskripsi / Indikator</label>
                                                                <textarea name="deskripsi" class="form-control" rows="3">{{ $category->deskripsi }}</textarea>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold text-dark small">Urutan Tampil</label>
                                                                <input type="number" name="urutan" class="form-control" value="{{ $category->urutan }}" min="1">
                                                            </div>

                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" name="is_active" id="editActive{{ $category->id }}" value="1" {{ $category->is_active ? 'checked' : '' }}>
                                                                <label class="form-check-label small fw-semibold" for="editActive{{ $category->id }}">
                                                                    Status Kategori Aktif
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-top bg-light py-2">
                                                            <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-sm btn-primary">Simpan Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                        Belum ada kategori penilaian. Silakan buat di form sebelah kiri.
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
</div>
@endsection
