@extends('layouts.admin') <!-- Sesuaikan dengan nama layout Anda -->

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Edit Raport: {{ $raport->athlete->nama ?? 'Atlet' }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.update', $raport->id) }}" method="POST">
                @csrf
                @method('PUT')

                <h6 class="fw-bold mt-3">E. Observasi Lapangan (Kualitatif)</h6>
                <div class="mb-3">
                    <label>Kekuatan Utama</label>
                    <textarea name="kekuatan_utama" class="form-control" rows="2">{{ $observasi['kekuatan_utama'] ?? '' }}</textarea>
                </div>
                <div class="mb-3">
                    <label>Area Peningkatan</label>
                    <textarea name="area_peningkatan" class="form-control" rows="2">{{ $observasi['area_peningkatan'] ?? '' }}</textarea>
                </div>
                <div class="mb-3">
                    <label>Catatan Perilaku</label>
                    <textarea name="catatan_perilaku" class="form-control" rows="2">{{ $observasi['catatan_perilaku'] ?? '' }}</textarea>
                </div>
                <div class="mb-3">
                    <label>Fokus Latihan</label>
                    <textarea name="fokus_latihan" class="form-control" rows="2">{{ $observasi['fokus_latihan'] ?? '' }}</textarea>
                </div>

                <hr>
                <h6 class="fw-bold">F. Kesimpulan / Rekomendasi Asesor</h6>
                <div class="mb-4">
                    <label>Catatan Pelatih</label>
                    <textarea name="catatan_pelatih" class="form-control" rows="4">{{ $raport->catatan_pelatih ?? '' }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection