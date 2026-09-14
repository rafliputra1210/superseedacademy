@extends('layouts.admin')
@section('title', 'Sistem Absensi Murid')

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
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-circle me-1"></i> Simpan Absensi</button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card card-custom bg-white p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="font-weight-bold mb-0"><i class="bi bi-filter-square me-1 text-primary"></i>Filter & Rekap Kehadiran</h6>
            </div>

            <!-- Form Filter Kalender Dari Tanggal Sampai Tanggal -->
            <form action="{{ route('admin.attendances.index') }}" method="GET" class="p-3 bg-light rounded-3 border mb-4">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="row g-2 align-items-end">
                    <div class="col-6 col-md-3">
                        <label class="form-label small font-weight-bold text-muted mb-1">
                            <i class="bi bi-calendar-event me-1 text-primary"></i>Dari Tanggal
                        </label>
                        <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" value="{{ request('start_date') ?? request('dari_tanggal') }}">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label small font-weight-bold text-muted mb-1">
                            <i class="bi bi-calendar-event-fill me-1 text-primary"></i>Sampai Tanggal
                        </label>
                        <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" value="{{ request('end_date') ?? request('sampai_tanggal') }}">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label small font-weight-bold text-muted mb-1">
                            <i class="bi bi-list-ol me-1 text-primary"></i>Tampilkan Baris
                        </label>
                        <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 Baris / Halaman</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Baris / Halaman</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Baris / Halaman</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Baris / Halaman</option>
                            <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Tampilkan Semua</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3 d-flex gap-1 mt-2 mt-md-0">
                        <button type="submit" class="btn btn-sm btn-primary flex-grow-1 font-weight-bold">
                            <i class="bi bi-search me-1"></i> Filter
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="setThisMonth()" title="Set Filter Bulan Ini">
                            Bulan Ini
                        </button>
                        @if(request('start_date') || request('end_date') || request('tanggal') || request('status') || request('per_page'))
                            <a href="{{ route('admin.attendances.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Semua Filter">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>

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

            @php
                $queryParams = array_filter([
                    'start_date' => request('start_date') ?? request('dari_tanggal'),
                    'end_date'   => request('end_date') ?? request('sampai_tanggal'),
                    'tanggal'    => request('tanggal'),
                    'per_page'   => request('per_page'),
                ]);
            @endphp

            <!-- Ringkasan Stat Absensi -->
            <div class="mb-4 p-3 bg-light rounded-3 border">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small font-weight-bold text-uppercase text-muted">
                        <i class="bi bi-pie-chart-fill me-1 text-primary"></i> Ringkasan Kehadiran
                        @if(request('start_date') && request('end_date'))
                            <span class="badge bg-primary text-white ms-1">{{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}</span>
                        @elseif(request('start_date'))
                            <span class="badge bg-primary text-white ms-1">Mulai {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }}</span>
                        @elseif(request('end_date'))
                            <span class="badge bg-primary text-white ms-1">s/d {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}</span>
                        @elseif(request('tanggal'))
                            <span class="badge bg-primary text-white ms-1">{{ \Carbon\Carbon::parse(request('tanggal'))->format('d/m/Y') }}</span>
                        @else
                            <span class="badge bg-secondary text-white ms-1">Semua Tanggal</span>
                        @endif
                        @if(request('status'))
                            <span class="badge bg-dark text-white ms-1">Status: {{ ucfirst(request('status')) }}</span>
                        @endif
                    </span>
                    <span class="small text-muted">Total: <strong>{{ $rekap['total'] }}</strong></span>
                </div>
                <div class="row g-2">
                    <!-- Card Hadir -->
                    <div class="col-6 col-sm-3">
                        <div class="p-2.5 px-3 rounded-3 bg-success bg-opacity-10 border {{ request('status') == 'hadir' ? 'border-success border-2 shadow-sm' : 'border-success border-opacity-25' }}">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-success small font-weight-bold"><i class="bi bi-person-check-fill me-1"></i>Hadir</span>
                                <a href="{{ route('admin.attendances.index', array_merge($queryParams, ['status' => request('status') == 'hadir' ? null : 'hadir'])) }}" 
                                   class="btn btn-sm {{ request('status') == 'hadir' ? 'btn-success text-white' : 'btn-light text-success border-0' }} p-0 px-2 py-0.5 rounded-2 shadow-sm" 
                                   title="{{ request('status') == 'hadir' ? 'Tampilkan semua status' : 'Lihat data murid Hadir' }}">
                                    <i class="bi {{ request('status') == 'hadir' ? 'bi-eye-slash-fill' : 'bi-eye-fill' }}"></i>
                                </a>
                            </div>
                            <div class="fs-4 font-weight-bold text-success text-center mt-1">{{ $rekap['hadir'] }}</div>
                        </div>
                    </div>
                    <!-- Card Izin -->
                    <div class="col-6 col-sm-3">
                        <div class="p-2.5 px-3 rounded-3 bg-info bg-opacity-10 border {{ request('status') == 'izin' ? 'border-info border-2 shadow-sm' : 'border-info border-opacity-25' }}">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-info small font-weight-bold"><i class="bi bi-envelope-paper-fill me-1"></i>Izin</span>
                                <a href="{{ route('admin.attendances.index', array_merge($queryParams, ['status' => request('status') == 'izin' ? null : 'izin'])) }}" 
                                   class="btn btn-sm {{ request('status') == 'izin' ? 'btn-info text-white' : 'btn-light text-info border-0' }} p-0 px-2 py-0.5 rounded-2 shadow-sm" 
                                   title="{{ request('status') == 'izin' ? 'Tampilkan semua status' : 'Lihat data murid Izin' }}">
                                    <i class="bi {{ request('status') == 'izin' ? 'bi-eye-slash-fill' : 'bi-eye-fill' }}"></i>
                                </a>
                            </div>
                            <div class="fs-4 font-weight-bold text-info text-center mt-1">{{ $rekap['izin'] }}</div>
                        </div>
                    </div>
                    <!-- Card Sakit -->
                    <div class="col-6 col-sm-3">
                        <div class="p-2.5 px-3 rounded-3 bg-warning bg-opacity-10 border {{ request('status') == 'sakit' ? 'border-warning border-2 shadow-sm' : 'border-warning border-opacity-25' }}">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-dark small font-weight-bold"><i class="bi bi-bandaid-fill me-1"></i>Sakit</span>
                                <a href="{{ route('admin.attendances.index', array_merge($queryParams, ['status' => request('status') == 'sakit' ? null : 'sakit'])) }}" 
                                   class="btn btn-sm {{ request('status') == 'sakit' ? 'btn-warning text-dark' : 'btn-light text-dark border-0' }} p-0 px-2 py-0.5 rounded-2 shadow-sm" 
                                   title="{{ request('status') == 'sakit' ? 'Tampilkan semua status' : 'Lihat data murid Sakit' }}">
                                    <i class="bi {{ request('status') == 'sakit' ? 'bi-eye-slash-fill' : 'bi-eye-fill' }}"></i>
                                </a>
                            </div>
                            <div class="fs-4 font-weight-bold text-dark text-center mt-1">{{ $rekap['sakit'] }}</div>
                        </div>
                    </div>
                    <!-- Card Alpha -->
                    <div class="col-6 col-sm-3">
                        <div class="p-2.5 px-3 rounded-3 bg-danger bg-opacity-10 border {{ (request('status') == 'alpa' || request('status') == 'alpha') ? 'border-danger border-2 shadow-sm' : 'border-danger border-opacity-25' }}">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-danger small font-weight-bold"><i class="bi bi-person-x-fill me-1"></i>Alpha</span>
                                <a href="{{ route('admin.attendances.index', array_merge($queryParams, ['status' => (request('status') == 'alpa' || request('status') == 'alpha') ? null : 'alpa'])) }}" 
                                   class="btn btn-sm {{ (request('status') == 'alpa' || request('status') == 'alpha') ? 'btn-danger text-white' : 'btn-light text-danger border-0' }} p-0 px-2 py-0.5 rounded-2 shadow-sm" 
                                   title="{{ (request('status') == 'alpa' || request('status') == 'alpha') ? 'Tampilkan semua status' : 'Lihat data murid Alpha' }}">
                                    <i class="bi {{ (request('status') == 'alpa' || request('status') == 'alpha') ? 'bi-eye-slash-fill' : 'bi-eye-fill' }}"></i>
                                </a>
                            </div>
                            <div class="fs-4 font-weight-bold text-danger text-center mt-1">{{ $rekap['alpa'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @if(request('status'))
            <div class="alert alert-info py-2 px-3 small d-flex justify-content-between align-items-center mb-3 rounded-3 border-info border-opacity-25">
                <span><i class="bi bi-funnel-fill me-1"></i> Menampilkan data murid status <strong>{{ ucfirst(request('status')) }}</strong></span>
                <a href="{{ route('admin.attendances.index', $queryParams) }}" class="text-decoration-none text-info font-weight-bold">
                    <i class="bi bi-x-circle-fill me-1"></i> Reset Filter Status
                </a>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle border text-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Atlet</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Foto Bukti</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $item)
                        <tr>
                            <td class="small text-nowrap"><i class="bi bi-calendar3 text-muted me-1"></i> {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                            <td class="font-weight-bold text-dark">{{ $item->athlete->nama ?? 'Atlet Dihapus' }}</td>
                            <td class="text-center">
                                @if($item->status == 'hadir') <span class="badge bg-success">Hadir</span>
                                @elseif($item->status == 'sakit') <span class="badge bg-warning text-dark">Sakit</span>
                                @elseif($item->status == 'izin') <span class="badge bg-info text-dark">Izin</span>
                                @else <span class="badge bg-danger">Alpa</span> @endif
                            </td>
                            <td class="text-center">
                                @if($item->foto_bukti)
                                    <a href="{{ asset($item->foto_bukti) }}" target="_blank" class="btn btn-sm btn-outline-info py-0 px-2 small" title="Lihat Foto Bukti">
                                        <i class="bi bi-image me-1"></i> Lihat Foto
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                <button type="button" class="btn btn-sm btn-warning text-dark font-weight-bold" data-bs-toggle="modal" data-bs-target="#editAttendanceModal{{ $item->id }}" title="Edit / Ubah Absensi">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <form action="{{ route('admin.attendances.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus absensi ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger ms-1" title="Hapus Absensi"><i class="bi bi-trash"></i></button>
                                </form>

                                <!-- Modal Edit Absensi #editAttendanceModal{{ $item->id }} -->
                                <div class="modal fade text-start" id="editAttendanceModal{{ $item->id }}" tabindex="-1" aria-labelledby="editAttendanceModalLabel{{ $item->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-primary text-white">
                                                <h6 class="modal-title font-weight-bold" id="editAttendanceModalLabel{{ $item->id }}">
                                                    <i class="bi bi-pencil-square me-2"></i>Ubah Data Absensi Murid
                                                </h6>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('admin.attendances.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4">
                                                    <div class="mb-3">
                                                        <label class="form-label small font-weight-bold">Nama Murid / Atlet</label>
                                                        <select name="athlete_id" class="form-select" required>
                                                            @foreach($athletes as $atlet)
                                                                <option value="{{ $atlet->id }}" {{ $item->athlete_id == $atlet->id ? 'selected' : '' }}>
                                                                    {{ $atlet->nama }} (U-{{ $atlet->nomor_punggung ?? 'XX' }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small font-weight-bold">Tanggal Latihan</label>
                                                        <input type="date" name="tanggal" class="form-control" value="{{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small font-weight-bold">Status Kehadiran</label>
                                                        <select name="status" class="form-select font-weight-bold" required>
                                                            <option value="hadir" {{ $item->status == 'hadir' ? 'selected' : '' }}>🟢 Hadir di Lapangan</option>
                                                            <option value="izin" {{ $item->status == 'izin' ? 'selected' : '' }}>🔵 Izin</option>
                                                            <option value="sakit" {{ $item->status == 'sakit' ? 'selected' : '' }}>🟡 Sakit</option>
                                                            <option value="alpa" {{ ($item->status == 'alpa' || $item->status == 'alpha') ? 'selected' : '' }}>🔴 Alpa (Tanpa Keterangan)</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small font-weight-bold">Foto Bukti / Kegiatan (Opsional)</label>
                                                        @if($item->foto_bukti)
                                                            <div class="mb-2">
                                                                <img src="{{ asset($item->foto_bukti) }}" alt="Foto Bukti" class="rounded border shadow-sm" style="max-height: 90px; object-fit: cover;">
                                                                <div class="small text-muted mt-1">Foto saat ini</div>
                                                            </div>
                                                        @endif
                                                        <input type="file" name="foto_bukti" class="form-control form-control-sm" accept="image/*">
                                                        <small class="text-muted">Biarkan kosong jika tidak merubah foto.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light px-4 py-2">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-sm btn-primary font-weight-bold px-3">
                                                        <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
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

<script>
function setThisMonth() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const lastDayNum = new Date(year, now.getMonth() + 1, 0).getDate();
    const lastDay = String(lastDayNum).padStart(2, '0');
    
    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');
    if (startInput && endInput) {
        startInput.value = `${year}-${month}-01`;
        endInput.value = `${year}-${month}-${lastDay}`;
    }
}
</script>
@endsection