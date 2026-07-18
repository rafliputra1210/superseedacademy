@extends('layouts.admin')
@section('title', 'Buat Pengumuman Baru')

@section('content')
<div class="card card-custom bg-white p-4 max-w-xl shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h5 class="mb-0 font-weight-bold">Tulis Pengumuman Baru</h5>
        <a href="{{ route('admin.announcements.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <form action="{{ route('admin.announcements.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label font-weight-bold">Judul Pengumuman <span class="text-danger">*</span></label>
                <input type="text" name="judul" class="form-control font-weight-bold" required placeholder="Contoh: Libur Latihan Hari Raya / Seleksi Tim Soeratin">
            </div>
            <div class="col-md-4">
                <label class="form-label font-weight-bold">Tanggal Tayang <span class="text-danger">*</span></label>
                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-12">
                <label class="form-label font-weight-bold">Isi Pesan Pengumuman <span class="text-danger">*</span></label>
                <textarea name="konten" class="form-control" rows="5" required placeholder="Tuliskan detail pengumuman secara jelas di sini..."></textarea>
            </div>
            <div class="col-md-12">
                <div class="form-check form-switch p-3 bg-light rounded border">
                    <input class="form-check-input ms-0 me-3" type="checkbox" name="is_active" id="is_active" value="1" checked>
                    <label class="form-check-label font-weight-bold text-success" for="is_active">🟢 Langsung Tayangkan di Portal Wali Murid</label>
                </div>
            </div>
        </div>
        <div class="mt-4 pt-3 border-top d-flex justify-content-end">
            <button type="submit" class="btn btn-primary px-4 font-weight-bold"><i class="bi bi-send-fill me-1"></i> Terbitkan Pengumuman</button>
        </div>
    </form>
</div>
@endsection