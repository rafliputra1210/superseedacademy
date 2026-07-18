@extends('layouts.admin')
@section('title', 'Tambah Data Coach Baru')

@section('content')
<div class="card card-custom bg-white p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h5 class="mb-0">Form Input Data Coach</h5>
        <a href="{{ route('admin.coaches.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <form action="{{ route('admin.coaches.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Nama Lengkap Coach <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required placeholder="Contoh: Coach Ahmad Santoso">
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Nomor WhatsApp <span class="text-danger">*</span></label>
                <input type="text" name="nomor_wa" class="form-control" value="{{ old('nomor_wa') }}" required placeholder="08123456789">
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Status Lisensi <span class="text-danger">*</span></label>
                <select name="status_lisensi" id="status_lisensi" class="form-select" required>
                    <option value="berlisensi" {{ old('status_lisensi') == 'berlisensi' ? 'selected' : '' }}>Berlisensi</option>
                    <option value="tidak_berlisensi" {{ old('status_lisensi') == 'tidak_berlisensi' ? 'selected' : '' }}>Tidak Berlisensi (Asisten)</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Detail Lisensi (Jika Berlisensi)</label>
                <input type="text" name="detail_lisensi" class="form-control" value="{{ old('detail_lisensi') }}" placeholder="Contoh: AFC B / PSSI D / Diploma Kepelatihan">
            </div>
            <div class="col-md-12">
                <label class="form-label font-weight-bold">Referensi & Pengalaman Melatih</label>
                <textarea name="referensi" class="form-control" rows="3" placeholder="Contoh: Mantan pemain Liga 2, Pelatih kepala tim Popda 2024...">{{ old('referensi') }}</textarea>
                <div class="form-text">Jelaskan latar belakang, sertifikasi, atau referensi pengalaman coach.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Foto Profil Coach (Opsional)</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Alamat Domisili</label>
                <input type="text" name="alamat" class="form-control" value="{{ old('alamat') }}" placeholder="Kota Malang...">
            </div>
        </div>

        <div class="mt-4 pt-3 border-top d-flex justify-content-end">
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Simpan Data Coach</button>
        </div>
    </form>
</div>
@endsection