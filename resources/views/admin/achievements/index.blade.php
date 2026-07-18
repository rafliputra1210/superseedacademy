@extends('layouts.admin')
@section('title', 'Manajemen Prestasi')

@section('content')
<div class="card card-custom bg-white p-4 shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h5 class="mb-0 font-weight-bold">Daftar Prestasi</h5>
        <a href="{{ route('admin.achievements.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Tambah Prestasi</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover align-middle border text-sm">
            <thead class="table-light">
                <tr>
                    <th>Foto/Ikon</th>
                    <th>Judul Prestasi</th>
                    <th>Tingkat</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($achievements as $item)
                <tr>
                    <td>
                        @if($item->foto)
                            <img src="{{ storage_img_url($item->foto) }}" alt="Foto" class="img-thumbnail" style="max-height: 50px;">
                        @else
                            <div class="bg-light d-inline-flex align-items-center justify-content-center rounded border" style="width:50px; height:50px;">
                                <i class="bi bi-trophy text-warning fs-4"></i>
                            </div>
                        @endif
                    </td>
                    <td class="font-weight-bold">{{ $item->judul }}</td>
                    <td>
                        <span class="badge bg-info text-dark">{{ $item->tingkat ?: '-' }}</span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}</td>
                    <td>
                        @if($item->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Draft</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.achievements.edit', $item->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.achievements.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus prestasi ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data prestasi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="d-flex justify-content-end mt-3">
        {{ $achievements->links() }}
    </div>
</div>
@endsection
