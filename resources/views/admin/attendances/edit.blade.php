@extends('layouts.admin')
@section('title', 'Ubah Data Absensi Murid')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card card-custom bg-white p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                <h6 class="font-weight-bold mb-0"><i class="bi bi-pencil-square text-primary me-2"></i>Ubah Absensi Murid</h6>
                <a href="{{ route('admin.attendances.index') }}" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
            </div>
            <form action="{{ route('admin.attendances.update', $attendance->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label small font-weight-bold">Nama Murid / Atlet</label>
                    <select name="athlete_id" class="form-select searchable-select" required>
                        @foreach($athletes as $atlet)
                            <option value="{{ $atlet->id }}" {{ $attendance->athlete_id == $atlet->id ? 'selected' : '' }}>
                                {{ $atlet->nama }} (U-{{ $atlet->nomor_punggung ?? 'XX' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small font-weight-bold">Tanggal Latihan</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ \Carbon\Carbon::parse($attendance->tanggal)->format('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small font-weight-bold">Status Kehadiran</label>
                    <select name="status" class="form-select font-weight-bold" required>
                        <option value="hadir" {{ $attendance->status == 'hadir' ? 'selected' : '' }}>🟢 Hadir di Lapangan</option>
                        <option value="izin" {{ $attendance->status == 'izin' ? 'selected' : '' }}>🔵 Izin</option>
                        <option value="sakit" {{ $attendance->status == 'sakit' ? 'selected' : '' }}>🟡 Sakit</option>
                        <option value="alpa" {{ ($attendance->status == 'alpa' || $attendance->status == 'alpha') ? 'selected' : '' }}>🔴 Alpa (Tanpa Keterangan)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small font-weight-bold">Foto Bukti / Kegiatan (Opsional)</label>
                    @if($attendance->foto_bukti)
                        <div class="mb-2">
                            <img src="{{ asset($attendance->foto_bukti) }}" alt="Foto Bukti" class="rounded border shadow-sm" style="max-height: 100px; object-fit: cover;">
                            <div class="small text-muted mt-1">Foto saat ini</div>
                        </div>
                    @endif
                    <input type="file" name="foto_bukti" class="form-control form-control-sm" accept="image/*">
                    <small class="text-muted">Upload foto baru jika ingin mengganti.</small>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                    <a href="{{ route('admin.attendances.index') }}" class="btn btn-light w-100">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
