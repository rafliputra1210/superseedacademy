@extends('layouts.admin')
@section('title', 'Edit Prestasi')

@section('content')
<div class="card card-custom bg-white p-4 max-w-4xl shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h5 class="mb-0 font-weight-bold">Form Edit Prestasi</h5>
        <a href="{{ route('admin.achievements.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger small">
            <ul class="mb-0">@foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach</ul>
        </div>
    @endif

    <form action="{{ route('admin.achievements.update', $achievement->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label font-weight-bold">Judul Prestasi <span class="text-danger">*</span></label>
                <input type="text" name="judul" class="form-control font-weight-bold fs-6" value="{{ old('judul', $achievement->judul) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-weight-bold">Tanggal Diraih <span class="text-danger">*</span></label>
                <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $achievement->tanggal) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Tingkat Prestasi</label>
                <select name="tingkat" class="form-select">
                    <option value="">-- Pilih Tingkat --</option>
                    <option value="Internasional" {{ $achievement->tingkat == 'Internasional' ? 'selected' : '' }}>Internasional</option>
                    <option value="Nasional" {{ $achievement->tingkat == 'Nasional' ? 'selected' : '' }}>Nasional</option>
                    <option value="Provinsi" {{ $achievement->tingkat == 'Provinsi' ? 'selected' : '' }}>Provinsi</option>
                    <option value="Kabupaten/Kota" {{ $achievement->tingkat == 'Kabupaten/Kota' ? 'selected' : '' }}>Kabupaten/Kota</option>
                    <option value="Antar Klub/SSB" {{ $achievement->tingkat == 'Antar Klub/SSB' ? 'selected' : '' }}>Antar Klub/SSB</option>
                    <option value="Lainnya" {{ $achievement->tingkat == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Foto / Bukti Prestasi (Opsional)</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
                <div class="form-text text-xs">Biarkan kosong jika tidak ingin mengubah foto.</div>
                @if($achievement->foto)
                    <div class="mt-2">
                        <img src="{{ storage_img_url($achievement->foto) }}" alt="Foto" class="img-thumbnail" style="max-height: 100px;">
                    </div>
                @endif
            </div>
            <div class="col-md-12">
                <label class="form-label font-weight-bold">Deskripsi Tambahan (Opsional)</label>
                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $achievement->deskripsi) }}</textarea>
            </div>
            <div class="col-md-12">
                <div class="form-check form-switch p-3 bg-light rounded border">
                    <input class="form-check-input ms-0 me-3" type="checkbox" name="is_active" id="is_active" value="1" {{ $achievement->is_active ? 'checked' : '' }}>
                    <label class="form-check-label font-weight-bold text-success" for="is_active">🟢 Tampilkan di Halaman Publik</label>
                </div>
            </div>
        </div>

        <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
            <a href="{{ route('admin.achievements.index') }}" class="btn btn-light border px-4">Batal</a>
            <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow"><i class="bi bi-save-fill me-1"></i> Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
