@extends('layouts.admin')
@section('title', 'Sistem Absensi Murid & Barcode')

@section('content')
<div class="row g-4">
    <div class="col-md-4">
        <div class="card card-custom bg-white p-4">
            <h6 class="font-weight-bold mb-3 border-bottom pb-2"><i class="bi bi-calendar-check text-primary me-2"></i>Catat Absensi Baru</h6>
            <form action="{{ route('admin.attendances.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label small font-weight-bold">Pilih Nama Murid / Atlet</label>
                    <select name="athlete_id" class="form-select searchable-select" required>
                        <option value="">-- Pilih Atlet --</option>
                        @foreach($athletes as $atlet)
                            <option value="{{ $atlet->id }}">{{ $atlet->nama }} (U-{{ $atlet->nomor_punggung ?? 'XX' }})</option>
                        @endforeach
                    </select>
                    </div>
                <div class="mb-3">
                    <label class="form-label small font-weight-bold">Tanggal Latihan</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small font-weight-bold">Status Kehadiran</label>
                    <select name="status" class="form-select" required>
                        <option value="hadir">Hadir di Lapangan</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="alpa">Alpa (Tanpa Keterangan)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small font-weight-bold">Foto Bukti / Kegiatan (Opsional)</label>
                    <input type="file" name="foto_bukti" class="form-control form-control-sm" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-upc-scan me-1"></i> Simpan & Generate Barcode</button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card card-custom bg-white p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="font-weight-bold mb-0">Riwayat Absensi & Output Barcode</h6>
                <form action="{{ route('admin.attendances.index') }}" method="GET" class="d-flex gap-2">
                    <input type="date" name="tanggal" class="form-control form-control-sm" value="{{ request('tanggal') }}">
                    <button type="submit" class="btn btn-sm btn-secondary">Filter</button>
                    @if(request('tanggal'))
                        <a href="{{ route('admin.attendances.index') }}" class="btn btn-sm btn-light">Reset</a>
                    @endif
                </form>
            </div>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h5 class="mb-1 font-weight-bold">Riwayat Absensi Atlet</h5>
        <p class="text-muted small mb-0">Pantau kehadiran harian atlet di setiap jadwal latihan.</p>
    </div>
    
    <div class="d-flex gap-2">
        <a href="{{ route('admin.attendances.scan') }}" class="btn btn-success btn-sm font-weight-bold d-flex align-items-center gap-1 shadow-sm px-3">
            <i class="bi bi-qr-code-scan fs-6"></i> Buka Kamera Scan Barcode
        </a>
    </div>
</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle border text-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Atlet</th>
                            <th class="text-center">Status</th>
                            <th>Kode Barcode</th>
                            <th class="text-center">Output Klien</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $item)
                        <tr>
                            <td class="small">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                            <td class="font-weight-bold">{{ $item->athlete->nama ?? 'Atlet Dihapus' }}</td>
                            <td class="text-center">
                                @if($item->status == 'hadir') <span class="badge bg-success">Hadir</span>
                                @elseif($item->status == 'sakit') <span class="badge bg-warning text-dark">Sakit</span>
                                @elseif($item->status == 'izin') <span class="badge bg-info text-dark">Izin</span>
                                @else <span class="badge bg-danger">Alpa</span> @endif
                            </td>
                            <td><code class="text-dark bg-light px-2 py-1 rounded border">{{ $item->kode_barcode }}</code></td>
                            <td class="text-center">
                                <a href="{{ route('admin.attendances.show', $item->id) }}" target="_blank" class="btn btn-sm btn-dark" title="Lihat Barcode & Foto">
                                    <i class="bi bi-printer-fill text-warning me-1"></i> Barcode & Foto
                                </a>
                                <form action="{{ route('admin.attendances.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus absensi ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0 ms-1"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat absensi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-2">{{ $attendances->links() }}</div>
        </div>
    </div>
</div>
@endsection