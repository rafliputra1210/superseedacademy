@extends('layouts.admin')
@section('title', 'Manajemen Data Coach & Pelatih')

@section('content')
<div class="card card-custom bg-white p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h5 class="mb-1 font-weight-bold">Daftar Coach Superseed Academy</h5>
            <p class="text-muted small mb-0">Kelola status lisensi, referensi kepelatihan, dan nomor kontak pelatih.</p>
        </div>
        
        <!-- Kelompok Tombol Aksi -->
        <div class="d-flex gap-2">
            <!-- TOMBOL EKSPOR EXCEL -->
            <a href="{{ route('admin.coaches.export') }}" class="btn btn-success btn-sm font-weight-bold d-flex align-items-center gap-1 shadow-sm">
                <i class="bi bi-file-earmark-excel-fill fs-6"></i> Ekspor Excel (.xlsx)
            </a>

            <!-- TOMBOL IMPOR EXCEL -->
            <button type="button" class="btn btn-warning btn-sm font-weight-bold text-dark d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalImportCoach">
                <i class="bi bi-file-earmark-arrow-up-fill fs-6"></i> Upload / Impor Excel
            </button>

            <!-- TOMBOL TAMBAH DATA -->
            <a href="{{ route('admin.coaches.create') }}" class="btn btn-primary btn-sm font-weight-bold d-flex align-items-center gap-1 shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Tambah Coach Baru
            </a>
        </div>
    </div>

<!-- MODAL POPUP UPLOAD EXCEL COACH (Taruh di paling bawah sebelum endsection) -->
<div class="modal fade" id="modalImportCoach" tabindex="-1" aria-labelledby="modalImportCoachLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title font-weight-bold" id="modalImportCoachLabel"><i class="bi bi-cloud-upload-fill me-2"></i>Upload File Excel Coach</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.coaches.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Pilih File Excel dari Komputer <span class="text-danger">*</span></label>
                        <input type="file" name="file_excel" class="form-control" accept=".xlsx, .xls, .csv" required>
                    </div>

                    <div class="p-3 bg-light rounded border text-xs">
                        <span class="font-weight-bold d-block mb-1 text-dark">Tips Impor Data:</span>
                        Gunakan tombol <strong>Ekspor Excel</strong> terlebih dahulu untuk mengunduh template format tabel yang benar.<br>
                        Anda juga bisa menggunakan kolom header berikut:<br>
                        <code class="text-primary font-weight-bold">nama_lengkap_coach</code>, 
                        <code class="text-dark">status_lisensi</code>, 
                        <code class="text-dark">detail_lisensi</code>, 
                        <code class="text-dark">nomor_whatsapp</code>, 
                        <code class="text-dark">referensi_pengalaman_melatih</code>, 
                        <code class="text-dark">alamat_domisili</code>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning font-weight-bold text-dark btn-sm px-4"><i class="bi bi-cloud-arrow-up-fill me-1"></i> Impor Data Coach</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <div class="table-responsive">
        <table class="table table-hover align-middle border">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>Nama Coach</th>
                    <th>Status & Lisensi</th>
                    <th>Referensi / Pengalaman</th>
                    <th>Kontak WA</th>
                    <th class="text-center" style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coaches as $item)
                <tr>
                    <td class="text-center font-weight-bold">{{ $loop->iteration }}</td>
                    <td>
                        <div class="d-flex items-center">
                            @if($item->foto)
                                <img src="{{ asset($item->foto) }}" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                            @else
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;"><i class="bi bi-person"></i></div>
                            @endif
                            <div>
                                <div class="font-weight-bold text-dark">{{ $item->nama }}</div>
                                <span class="text-muted small">{{ Str::limit($item->alamat, 25) }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($item->status_lisensi == 'berlisensi')
                            <span class="badge bg-success"><i class="bi bi-patch-check-fill me-1"></i> Berlisensi</span>
                            <div class="small font-weight-bold mt-1 text-dark">{{ $item->detail_lisensi ?? 'Lisensi Resmi' }}</div>
                        @else
                            <span class="badge bg-secondary">Tidak Berlisensi / Asisten</span>
                        @endif
                    </td>
                    <td>
                        <p class="small text-muted mb-0" style="max-width: 250px; white-space: normal;">
                            {{ $item->referensi ?: '-' }}
                        </p>
                    </td>
                    <td>
                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', $item->nomor_wa) }}" target="_blank" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-whatsapp"></i> {{ $item->nomor_wa ?: '-' }}
                        </a>
                    </td>
                    <td class="text-center">
                        <form action="{{ route('admin.coaches.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data coach ini?');">
                            <a href="{{ route('admin.coaches.edit', $item->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data coach yang terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $coaches->links() }}</div>
</div>
@endsection