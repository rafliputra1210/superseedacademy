@extends('layouts.admin')
@section('title', 'Manajemen Asesmen Pemain')

@section('content')
<div class="card card-custom bg-white p-4 shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h5 class="mb-1 font-weight-bold text-dark"><i class="bi bi-card-checklist text-success me-2"></i>Daftar Asesmen Pemain</h5>
            <p class="text-muted small mb-0">Catatan Form Asesmen Teknis, Taktikal, Fisik, dan Observasi Lapangan.</p>
        </div>
        <a href="{{ route('admin.reports.create') }}" class="btn btn-primary btn-sm font-weight-bold px-3">
            <i class="bi bi-plus-circle me-1"></i> Input Asesmen Baru
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle border text-sm">
            <thead class="table-light">
                <tr>
                    <th>Tgl Asesmen</th>
                    <th>Nama Pemain & Posisi</th>
                    <th>No. Asesmen / Asesor</th>
                    <th>Rangkuman Observasi Lapangan</th>
                    <th class="text-center" style="width: 80px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $item)
                <tr>
                    <td class="font-weight-bold text-nowrap"><i class="bi bi-calendar-check text-muted me-1"></i> {{ \Carbon\Carbon::parse($item->tanggal_asesmen)->format('d M Y') }}</td>
                    <td>
                        <strong class="text-dark d-block">
                            {{ $item->athlete->nama ?? 'Siswa Dihapus' }}
                            @if($item->is_archived)
                                <span class="badge bg-warning text-dark text-xxs ms-1">Arsip</span>
                            @endif
                        </strong>
                        <span class="badge bg-secondary text-xs">{{ $item->athlete->posisi_bermain ?? '-' }}</span>
                    </td>
                    <td>
                        <div class="text-xs text-muted mb-1">No: <span class="text-dark">{{ $item->nomor_asesmen ?: '-' }}</span></div>
                        <div class="text-xs text-muted">Asesor: <strong class="text-primary">{{ $item->asesor_nama }}</strong></div>
                    </td>
                    <td>
                        <div class="small"><strong>📈 Strengths:</strong> {{ Str::limit($item->kekuatan_utama ?: '-', 35) }}</div>
                        <div class="small text-danger"><strong>📉 Weaknesses:</strong> {{ Str::limit($item->area_peningkatan ?: '-', 35) }}</div>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <form action="{{ route('admin.reports.toggle-archive', $item->id) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $item->is_archived ? 'btn-outline-success' : 'btn-outline-warning' }}" title="{{ $item->is_archived ? 'Buka Arsip' : 'Arsipkan' }}">
                                    <i class="bi {{ $item->is_archived ? 'bi-archive-fill' : 'bi-archive' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.reports.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus asesmen ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada form asesmen yang diinput.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $reports->links() }}</div>
</div>
@endsection