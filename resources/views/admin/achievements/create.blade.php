@extends('layouts.admin')
@section('title', 'Tambah Prestasi')

@section('content')
<div class="card card-custom bg-white p-4 max-w-4xl shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h5 class="mb-0 font-weight-bold">Form Tambah Prestasi</h5>
        <a href="{{ route('admin.achievements.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger small">
            <ul class="mb-0">@foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach</ul>
        </div>
    @endif

    <form action="{{ route('admin.achievements.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label font-weight-bold">Judul Prestasi <span class="text-danger">*</span></label>
                <input type="text" name="judul" class="form-control font-weight-bold fs-6" value="{{ old('judul') }}" required placeholder="Contoh: Juara 1 Turnamen Soeratin U-15">
            </div>
            <div class="col-md-4">
                <label class="form-label font-weight-bold">Tanggal Diraih <span class="text-danger">*</span></label>
                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Tingkat Prestasi</label>
                <select name="tingkat" class="form-select">
                    <option value="">-- Pilih Tingkat --</option>
                    <option value="Internasional">Internasional</option>
                    <option value="Nasional">Nasional</option>
                    <option value="Provinsi">Provinsi</option>
                    <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                    <option value="Antar Klub/SSB">Antar Klub/SSB</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Foto / Bukti Prestasi (Opsional)</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
                <div class="form-text text-xs">Format: JPG, PNG, WEBP. Bisa berupa foto tim dengan piala.</div>
            </div>
            <div class="col-md-12">
                <label class="form-label font-weight-bold">Deskripsi Tambahan (Opsional)</label>
                <textarea name="deskripsi" class="form-control" rows="3" placeholder="Ceritakan sedikit tentang perjalanan meraih prestasi ini...">{{ old('deskripsi') }}</textarea>
            </div>
            <div class="col-md-12">
                <div class="form-check form-switch p-3 bg-light rounded border">
                    <input class="form-check-input ms-0 me-3" type="checkbox" name="is_active" id="is_active" value="1" checked>
                    <label class="form-check-label font-weight-bold text-success" for="is_active">🟢 Tampilkan di Halaman Publik</label>
                </div>
            </div>
        </div>

        <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
            <a href="{{ route('admin.achievements.index') }}" class="btn btn-light border px-4">Batal</a>
            <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow"><i class="bi bi-save-fill me-1"></i> Simpan Prestasi</button>
        </div>
    </form>
</div>
@endsection
