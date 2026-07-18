@extends('layouts.admin')
@section('title', 'Manajemen Data Atlet / Murid')

@section('content')
<div class="card card-custom bg-white p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1 font-weight-bold">Daftar Atlet Superseed Academy</h5>
            <p class="text-muted small mb-0">Kelola data murid, posisi bermain, hingga nomor WA orang tua.</p>
        </div>
        <a href="{{ route('admin.athletes.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Tambah Atlet Baru
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle border">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>Nama Atlet & Posisi</th>
                    <th class="text-center">No. Punggung</th>
                    <th>Tgl Lahir</th>
                    <th>Nomor WA (Ortu/Siswa)</th>
                    <th>Akun Wali Murid</th>
                    <th class="text-center" style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($athletes as $item)
                <tr>
                    <td class="text-center font-weight-bold">{{ $loop->iteration }}</td>
                    <td>
                        <div class="font-weight-bold text-dark">{{ $item->nama }}</div>
                        <span class="badge bg-secondary text-xs">{{ $item->posisi_bermain ?? 'Belum ditentukan' }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-dark fs-6">{{ $item->nomor_punggung ?? '-' }}</span>
                    </td>
                    <td class="small">{{ $item->tanggal_lahir ? \Carbon\Carbon::parse($item->tanggal_lahir)->format('d M Y') : '-' }}</td>
                    <td class="small">
                        <div><i class="bi bi-person text-muted"></i> Siswa: {{ $item->nomor_wa ?? '-' }}</div>
                        <div><i class="bi bi-people-fill text-success"></i> Ortu: <strong>{{ $item->nomor_wa_ortu }}</strong></div>
                    </td>
                    <td>
                        @if($item->user)
                            <span class="badge bg-info text-dark"><i class="bi bi-link-45deg"></i> {{ $item->user->name }}</span>
                        @else
                            <span class="badge bg-light text-muted border">Belum dikaitkan</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <form action="{{ route('admin.athletes.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data atlet ini?');">
                            <a href="{{ route('admin.athletes.edit', $item->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <i class="bi bi-folder2-open fs-2 d-block mb-1"></i>
                        Belum ada data atlet yang terdaftar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-3">
        {{ $athletes->links() }}
    </div>
</div>
@endsection