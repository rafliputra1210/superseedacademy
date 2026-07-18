@extends('layouts.admin')
@section('title', 'Tulis Berita Baru')

@section('content')
<div class="card card-custom bg-white p-4 max-w-4xl shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h5 class="mb-0 font-weight-bold">Form Penulisan Berita & Artikel</h5>
        <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger small">
            <ul class="mb-0">@foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach</ul>
        </div>
    @endif

    <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label font-weight-bold">Judul Berita <span class="text-danger">*</span></label>
                <input type="text" name="judul" class="form-control font-weight-bold fs-6" value="{{ old('judul') }}" required placeholder="Contoh: Superseed Academy U-15 Lolos ke Final Soeratin Cup 2026">
            </div>
            <div class="col-md-4">
                <label class="form-label font-weight-bold">Kategori Artikel <span class="text-danger">*</span></label>
                <select name="kategori" class="form-select font-weight-bold text-primary" required>
                    <option value="Turnamen & Pertandingan">🏆 Turnamen & Pertandingan</option>
                    <option value="Kegiatan Akademi">⚽ Kegiatan Akademi</option>
                    <option value="Tips & Nutrisi Atlet">🍏 Tips & Nutrisi Atlet</option>
                    <option value="Profil Bintang">🌟 Profil Bintang Siswa</option>
                    <option value="Info Kepelatihan">👨‍🏫 Info Kepelatihan</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Tanggal Tayang <span class="text-danger">*</span></label>
                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Foto / Banner Berita (Opsional)</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
                <div class="form-text text-xs">Format: JPG, PNG, WEBP (Maksimal 2 MB).</div>
            </div>
            <div class="col-md-12">
                <label class="form-label font-weight-bold">Isi Konten Berita <span class="text-danger">*</span></label>
                <textarea name="konten" class="form-control" rows="8" required placeholder="Tuliskan liputan atau artikel secara rinci di sini... (Anda dapat membagi menjadi paragraf dengan menekan Enter)">{{ old('konten') }}</textarea>
            </div>
            <div class="col-md-12">
                <div class="form-check form-switch p-3 bg-light rounded border">
                    <input class="form-check-input ms-0 me-3" type="checkbox" name="is_active" id="is_active" value="1" checked>
                    <label class="form-check-label font-weight-bold text-success" for="is_active">🟢 Langsung Tayangkan Berita Ini di Web Publik</label>
                </div>
            </div>
        </div>

        <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
            <a href="{{ route('admin.news.index') }}" class="btn btn-light border px-4">Batal</a>
            <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow"><i class="bi bi-send-fill me-1"></i> Terbitkan Berita</button>
        </div>
    </form>
</div>
@endsection