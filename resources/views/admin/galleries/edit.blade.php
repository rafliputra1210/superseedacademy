@extends('layouts.admin')
@section('title', 'Edit Galeri')

@section('content')
<div class="card card-custom bg-white p-4 max-w-4xl shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h5 class="mb-0 font-weight-bold">Form Edit Galeri</h5>
        <a href="{{ route('admin.galleries.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger small">
            <ul class="mb-0">@foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach</ul>
        </div>
    @endif

    <form action="{{ route('admin.galleries.update', $gallery->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label font-weight-bold">Judul Foto / Kegiatan <span class="text-danger">*</span></label>
                <input type="text" name="judul" class="form-control font-weight-bold fs-6" value="{{ old('judul', $gallery->judul) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-weight-bold">Tanggal Kegiatan <span class="text-danger">*</span></label>
                <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $gallery->tanggal) }}" required>
            </div>
            <div class="col-md-12">
                <label class="form-label font-weight-bold">Foto</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
                <div class="form-text text-xs">Biarkan kosong jika tidak ingin mengubah foto. Format: JPG, PNG, WEBP.</div>
                @if($gallery->foto)
                    <div class="mt-2">
                        <img src="{{ storage_img_url($gallery->foto) }}" alt="Foto" class="img-thumbnail" style="max-height: 100px;">
                    </div>
                @endif
            </div>
            <div class="col-md-12">
                <label class="form-label font-weight-bold">Deskripsi (Opsional)</label>
                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $gallery->deskripsi) }}</textarea>
            </div>
            <div class="col-md-12">
                <div class="form-check form-switch p-3 bg-light rounded border">
                    <input class="form-check-input ms-0 me-3" type="checkbox" name="is_active" id="is_active" value="1" {{ $gallery->is_active ? 'checked' : '' }}>
                    <label class="form-check-label font-weight-bold text-success" for="is_active">🟢 Tampilkan di Galeri Publik</label>
                </div>
            </div>
        </div>

        <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
            <a href="{{ route('admin.galleries.index') }}" class="btn btn-light border px-4">Batal</a>
            <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow"><i class="bi bi-save-fill me-1"></i> Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
