@extends('layouts.admin')
@section('title', 'Edit Data Coach')

@section('content')
<div class="card card-custom bg-white p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h5 class="mb-0 font-weight-bold">Edit Data Coach: <span class="text-primary">{{ $coach->nama }}</span></h5>
            <p class="text-muted small mb-0">Perbarui informasi kontak, status lisensi, atau referensi kepelatihan.</p>
        </div>
        <a href="{{ route('admin.coaches.index') }}" class="btn btn-sm btn-light border">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <!-- Tampilkan Error Validasi Jika Ada -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong class="d-block mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal Memperbarui Data!</strong>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.coaches.update', $coach->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <!-- Nama Lengkap -->
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Nama Lengkap Coach <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama', $coach->nama) }}" required placeholder="Contoh: Coach Ahmad Santoso">
            </div>

            <!-- Nomor WA -->
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Nomor WhatsApp <span class="text-danger">*</span></label>
                <input type="text" name="nomor_wa" class="form-control" value="{{ old('nomor_wa', $coach->nomor_wa) }}" required placeholder="081234567890">
            </div>

            <!-- Status Lisensi -->
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Status Lisensi <span class="text-danger">*</span></label>
                <select name="status_lisensi" id="status_lisensi" class="form-select" required>
                    <option value="berlisensi" {{ old('status_lisensi', $coach->status_lisensi) == 'berlisensi' ? 'selected' : '' }}>Berlisensi</option>
                    <option value="tidak_berlisensi" {{ old('status_lisensi', $coach->status_lisensi) == 'tidak_berlisensi' ? 'selected' : '' }}>Tidak Berlisensi (Asisten)</option>
                </select>
            </div>

            <!-- Detail Lisensi -->
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Detail Lisensi (Jika Berlisensi)</label>
                <input type="text" name="detail_lisensi" class="form-control" value="{{ old('detail_lisensi', $coach->detail_lisensi) }}" placeholder="Contoh: AFC B / PSSI D / Diploma Kepelatihan">
                <div class="form-text text-muted small">Kosongkan jika berstatus tidak berlisensi.</div>
            </div>

            <!-- Referensi & Pengalaman -->
            <div class="col-md-12">
                <label class="form-label font-weight-bold">Referensi & Pengalaman Melatih</label>
                <textarea name="referensi" class="form-control" rows="3" placeholder="Contoh: Mantan pemain Liga 2, Pelatih kepala tim Popda 2024...">{{ old('referensi', $coach->referensi) }}</textarea>
                <div class="form-text text-muted small">Jelaskan latar belakang, sertifikasi, atau referensi pengalaman coach.</div>
            </div>

            <!-- Alamat Domisili -->
            <div class="col-md-12">
                <label class="form-label font-weight-bold">Alamat Domisili</label>
                <input type="text" name="alamat" class="form-control" value="{{ old('alamat', $coach->alamat) }}" placeholder="Contoh: Jl. Raya Malang No. 45, Jawa Timur">
            </div>

            <!-- Foto Profil & Pratinjau Foto Lama -->
            <div class="col-md-12 border-top pt-3 mt-4">
                <label class="form-label font-weight-bold d-block">Foto Profil Coach</label>
                <div class="d-flex items-center gap-3 bg-light p-3 rounded border">
                    <div class="text-center">
                        @if($coach->foto)
                            <img src="{{ asset($coach->foto) }}" class="rounded shadow-sm" width="80" height="80" style="object-fit: cover;" alt="Foto Coach">
                            <span class="d-block text-xs text-muted mt-1">Foto Saat Ini</span>
                        @else
                            <div class="bg-secondary text-white rounded d-flex items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="bi bi-person-fill fs-1"></i>
                            </div>
                            <span class="d-block text-xs text-muted mt-1">Belum Ada Foto</span>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <input type="file" name="foto" class="form-control" accept="image/*">
                        <div class="form-text text-muted small mt-1">
                            Biarkan kosong jika tidak ingin mengubah foto profil. Format yang didukung: JPG, JPEG, PNG (Maksimal 2 MB).
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
            <a href="{{ route('admin.coaches.index') }}" class="btn btn-light border px-4">Batal</a>
            <button type="submit" class="btn btn-warning px-4 font-weight-bold">
                <i class="bi bi-save me-1"></i> Perbarui Data Coach
            </button>
        </div>
    </form>
</div>
@endsection