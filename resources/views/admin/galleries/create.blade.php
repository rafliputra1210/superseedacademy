@extends('layouts.admin')
@section('title', 'Tambah Galeri')

@section('content')
<div class="card card-custom bg-white p-4 max-w-4xl shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h5 class="mb-0 font-weight-bold">Form Tambah Galeri</h5>
        <a href="{{ route('admin.galleries.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger small">
            <ul class="mb-0">@foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach</ul>
        </div>
    @endif

    <form action="{{ route('admin.galleries.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label font-weight-bold">Judul Foto / Kegiatan <span class="text-danger">*</span></label>
                <input type="text" name="judul" class="form-control font-weight-bold fs-6" value="{{ old('judul') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-weight-bold">Tanggal Kegiatan <span class="text-danger">*</span></label>
                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-12">
                <label class="form-label font-weight-bold">Foto <span class="text-danger">*</span></label>
                <input type="file" name="foto" class="form-control" accept="image/*" required>
                <div class="form-text text-xs">Format: JPG, PNG, WEBP (Maksimal 2 MB).</div>
            </div>
            <div class="col-md-12">
                <label class="form-label font-weight-bold">Deskripsi (Opsional)</label>
                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi') }}</textarea>
            </div>
            <div class="col-md-12">
                <div class="form-check form-switch p-3 bg-light rounded border">
                    <input class="form-check-input ms-0 me-3" type="checkbox" name="is_active" id="is_active" value="1" checked>
                    <label class="form-check-label font-weight-bold text-success" for="is_active">🟢 Tampilkan di Galeri Publik</label>
                </div>
            </div>
        </div>

        <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
            <a href="{{ route('admin.galleries.index') }}" class="btn btn-light border px-4">Batal</a>
            <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow"><i class="bi bi-save-fill me-1"></i> Simpan Galeri</button>
        </div>
    </form>
</div>
@endsection
