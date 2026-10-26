@extends('layouts.admin')

@section('title', 'Manajemen Akun Asesor')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
        <div>
            <h4 class="fw-bold text-brand-navy mb-1">
                <i class="bi bi-person-badge text-primary me-2"></i>Manajemen Akun Asesor
            </h4>
            <p class="text-secondary small mb-0">
                Kelola akun asesor untuk penginputan nilai observasi dan penilaian performa atlet di lapangan.
            </p>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-exclamation-octagon-fill me-2 fs-5"></i>
                <strong>Terjadi kesalahan saat memproses data:</strong>
            </div>
            <ul class="mb-0 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Form Tambah Asesor Baru -->
        <div class="col-lg-4">
            <div class="card card-custom shadow-sm border-0 sticky-top" style="top: 80px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-brand-navy">
                        <i class="bi bi-person-plus-fill text-primary me-2"></i>Tambah Asesor Baru
                    </h6>
                </div>
                <div class="card-body p-3 p-md-4">
                    <form action="{{ route('admin.asesor_manager.store') }}" method="POST">
                        @csrf

                        <!-- Nama Lengkap -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">Nama Lengkap Asesor <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="name" 
                                   class="form-control form-control-sm @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" 
                                   required 
                                   placeholder="Contoh: Budi Santoso, S.Pd">
                            @error('name') 
                                <small class="text-danger d-block mt-1">{{ $message }}</small> 
                            @enderror
                        </div>

                        <!-- Username Login -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">Username Login <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-at"></i></span>
                                <input type="text" 
                                       name="username" 
                                       class="form-control @error('username') is-invalid @enderror" 
                                       value="{{ old('username') }}" 
                                       required 
                                       placeholder="Contoh: budi_asesor">
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                Hanya huruf, angka, dan underscore (_).
                            </small>
                            @error('username') 
                                <small class="text-danger d-block mt-1">{{ $message }}</small> 
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">Password <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <input type="password" 
                                       name="password" 
                                       id="passwordInput"
                                       class="form-control @error('password') is-invalid @enderror" 
                                       required 
                                       placeholder="Minimal 8 karakter">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('passwordInput', 'togglePassIcon')">
                                    <i class="bi bi-eye" id="togglePassIcon"></i>
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                Minimal 8 karakter kombinasi huruf & angka.
                            </small>
                            @error('password') 
                                <small class="text-danger d-block mt-1">{{ $message }}</small> 
                            @enderror
                        </div>

                        <!-- Konfirmasi Password -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-dark">Konfirmasi Password <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="passwordConfirmInput"
                                       class="form-control @error('password_confirmation') is-invalid @enderror" 
                                       required 
                                       placeholder="Ulangi password di atas">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('passwordConfirmInput', 'toggleConfirmIcon')">
                                    <i class="bi bi-eye" id="toggleConfirmIcon"></i>
                                </button>
                            </div>
                            @error('password_confirmation') 
                                <small class="text-danger d-block mt-1">{{ $message }}</small> 
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm w-100 py-2 fw-semibold shadow-sm">
                            <i class="bi bi-check2-circle me-1"></i> Buat Akun Asesor
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabel Daftar Akun Asesor -->
        <div class="col-lg-8">
            <div class="card card-custom shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-brand-navy">
                        <i class="bi bi-people text-primary me-2"></i>Daftar Akun Asesor
                    </h6>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1">
                        {{ count($asesors) }} Asesor
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-secondary small">
                                    <th class="ps-3" style="width: 50px;">No</th>
                                    <th>Nama Asesor</th>
                                    <th>Username Login</th>
                                    <th>Terdaftar</th>
                                    <th class="text-end pe-3" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($asesors as $index => $asesor)
                                <tr>
                                    <td class="ps-3 text-muted small">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 34px; height: 34px; font-size: 0.85rem;">
                                                {{ strtoupper(substr($asesor->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <span class="fw-semibold text-dark d-block">{{ $asesor->name }}</span>
                                                <small class="text-muted">Role: <span class="badge bg-secondary-subtle text-secondary">Asesor</span></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <code class="text-primary bg-light px-2 py-1 rounded small">
                                            {{ $asesor->username }}
                                        </code>
                                    </td>
                                    <td class="text-muted small">
                                        {{ $asesor->created_at ? $asesor->created_at->format('d M Y') : '-' }}
                                    </td>
                                    <td class="text-end pe-3">
                                        <form action="{{ route('admin.asesor_manager.destroy', $asesor->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus akun asesor ({{ $asesor->name }})? Akun ini tidak akan dapat login lagi.')"
                                                    title="Hapus Akun Asesor">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-person-x fs-1 text-secondary opacity-50 d-block mb-2"></i>
                                            <span class="fw-medium">Belum ada akun asesor yang dibuat.</span>
                                            <p class="small text-secondary mb-0 mt-1">Gunakan formulir di sebelah kiri untuk membuat akun asesor baru.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (!input || !icon) return;

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
@endsection