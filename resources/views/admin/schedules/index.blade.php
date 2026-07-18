@extends('layouts.admin')
@section('title', 'Manajemen Jadwal Latihan')

@section('content')
<div class="row g-4">
    <div class="col-md-4">
        <div class="card card-custom bg-white p-4">
            <h6 class="font-weight-bold mb-3 border-bottom pb-2"><i class="bi bi-clock-history text-primary me-2"></i>Tambah Jadwal Latihan</h6>
            <form action="{{ route('admin.schedules.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small font-weight-bold">Kelompok Usia <span class="text-danger">*</span></label>
                    <select name="kelompok_usia" class="form-select form-select-sm" required>
                        <option value="">-- Pilih Kelompok Usia --</option>
                        @foreach($kelompokUmurList as $ku)
                            <option value="{{ $ku }}">{{ $ku }}</option>
                        @endforeach
                    </select>
                    @if($kelompokUmurList->isEmpty())
                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Belum ada data kelompok umur di database atlet.</small>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="form-label small font-weight-bold">Kelompok Latihan</label>
                    <select name="kelompok_latihan" class="form-select form-select-sm">
                        <option value="">-- Pilih Kelompok Latihan --</option>
                        @foreach($kelompokLatihanList as $kl)
                            <option value="{{ $kl }}">{{ $kl }}</option>
                        @endforeach
                    </select>
                    @if($kelompokLatihanList->isEmpty())
                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Belum ada data kelompok latihan di database atlet.</small>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="form-label small font-weight-bold">Hari Latihan <span class="text-danger">*</span></label>
                    <input type="text" name="hari" class="form-control form-control-sm" placeholder="Contoh: Selasa & Kamis" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small font-weight-bold">Waktu / Jam <span class="text-danger">*</span></label>
                    <input type="text" name="waktu" class="form-control form-control-sm" placeholder="Contoh: 15.30 - 17.00 WIB" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small font-weight-bold">Lokasi Lapangan <span class="text-danger">*</span></label>
                    <input type="text" name="lokasi" class="form-control form-control-sm" placeholder="Contoh: Stadion Mini Superseed" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small font-weight-bold">Pelatih Penanggung Jawab</label>
                    <select name="coach_id" class="form-select form-select-sm">
                        <option value="">-- Pilih Coach --</option>
                        @foreach($coaches as $coach)
                            <option value="{{ $coach->id }}">{{ $coach->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-save me-1"></i> Simpan & Sinkronkan</button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card card-custom bg-white p-4">
            <h6 class="font-weight-bold mb-3 border-bottom pb-2">Daftar Jadwal Latihan Aktif</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle border text-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Kelompok Usia</th>
                            <th>Kelompok Latihan</th>
                            <th>Hari & Waktu</th>
                            <th>Lokasi</th>
                            <th>Pelatih Utama</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $item)
                        <tr>
                            <td class="font-weight-bold text-dark">{{ $item->kelompok_usia }}</td>
                            <td>
                                @if($item->kelompok_latihan)
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 fw-semibold">
                                        <i class="bi bi-people-fill me-1"></i>{{ $item->kelompok_latihan }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <div><i class="bi bi-calendar3 text-muted me-1"></i> {{ $item->hari }}</div>
                                <small class="text-success font-weight-bold"><i class="bi bi-clock me-1"></i> {{ $item->waktu }}</small>
                            </td>
                            <td class="small">{{ $item->lokasi }}</td>
                            <td>
                                @if($item->coach)
                                    <span class="badge bg-info text-dark">{{ $item->coach->nama }}</span>
                                @else
                                    <span class="badge bg-light text-muted border">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.schedules.edit', $item->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.schedules.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus jadwal ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada jadwal latihan. Silahkan tambahkan di form sebelah kiri.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection