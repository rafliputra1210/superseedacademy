@extends('layouts.admin')
@section('title', 'Edit Berita')

@section('content')
<div class="card card-custom bg-white p-4 max-w-4xl shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h5 class="mb-0 font-weight-bold">Form Edit Berita & Artikel</h5>
        <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger small">
            <ul class="mb-0">@foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach</ul>
        </div>
    @endif

    <form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label font-weight-bold">Judul Berita <span class="text-danger">*</span></label>
                <input type="text" name="judul" class="form-control font-weight-bold fs-6" value="{{ old('judul', $news->judul) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-weight-bold">Kategori Artikel <span class="text-danger">*</span></label>
                <select name="kategori" class="form-select font-weight-bold text-primary" required>
                    <option value="Turnamen & Pertandingan" {{ $news->kategori == 'Turnamen & Pertandingan' ? 'selected' : '' }}>🏆 Turnamen & Pertandingan</option>
                    <option value="Kegiatan Akademi" {{ $news->kategori == 'Kegiatan Akademi' ? 'selected' : '' }}>⚽ Kegiatan Akademi</option>
                    <option value="Tips & Nutrisi Atlet" {{ $news->kategori == 'Tips & Nutrisi Atlet' ? 'selected' : '' }}>🍏 Tips & Nutrisi Atlet</option>
                    <option value="Profil Bintang" {{ $news->kategori == 'Profil Bintang' ? 'selected' : '' }}>🌟 Profil Bintang Siswa</option>
                    <option value="Info Kepelatihan" {{ $news->kategori == 'Info Kepelatihan' ? 'selected' : '' }}>👨‍🏫 Info Kepelatihan</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Tanggal Tayang <span class="text-danger">*</span></label>
                <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $news->tanggal) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Foto / Banner Berita (Opsional)</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
                <div class="form-text text-xs">Biarkan kosong jika tidak ingin mengubah foto.</div>
                @if($news->foto)
                    <div class="mt-2">
                        <img src="{{ storage_img_url($news->foto) }}" alt="Foto" class="img-thumbnail" style="max-height: 100px;">
                    </div>
                @endif
            </div>
            <div class="col-md-12">
                <label class="form-label font-weight-bold">Isi Konten Berita <span class="text-danger">*</span></label>
                <textarea name="konten" class="form-control" rows="8" required>{{ old('konten', $news->konten) }}</textarea>
            </div>
            <div class="col-md-12">
                <div class="form-check form-switch p-3 bg-light rounded border">
                    <input class="form-check-input ms-0 me-3" type="checkbox" name="is_active" id="is_active" value="1" {{ $news->is_active ? 'checked' : '' }}>
                    <label class="form-check-label font-weight-bold text-success" for="is_active">🟢 Tayangkan Berita Ini di Web Publik</label>
                </div>
            </div>
        </div>

        <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
            <a href="{{ route('admin.news.index') }}" class="btn btn-light border px-4">Batal</a>
            <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow"><i class="bi bi-save-fill me-1"></i> Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
