@extends('layouts.wali')

@section('content')
<div class="row g-4">
    <!-- Kolom Kiri: Ringkasan Akun Wali Murid -->
    <div class="col-lg-4">
        <div class="card card-custom p-4 text-center border-top border-4 hover-elevate mb-4" style="border-top-color: var(--brand-green) !important;">
            <div class="position-relative d-inline-block mx-auto mb-3">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=047857&color=ffffff&size=200&bold=true" 
                     class="rounded-circle shadow-sm border border-4 border-white" 
                     width="110" height="110" alt="Avatar {{ $user->name }}">
            </div>

            <h5 class="fw-bold text-brand-navy mb-1">{{ $user->name }}</h5>
            <span class="badge bg-brand-light text-brand-green mb-3 px-3 py-1 fw-bold rounded-pill border border-success-subtle">
                <i class="bi bi-person-check-fill me-1"></i> Akun Wali Murid
            </span>

            <ul class="list-group list-group-flush text-start small mt-2">
                <li class="list-group-item px-0 d-flex justify-content-between align-items-center py-2">
                    <span class="text-secondary d-flex align-items-center gap-2">
                        <i class="bi bi-at text-brand-green fs-6"></i> Username Login
                    </span>
                    <strong class="text-brand-navy font-monospace">{{ $user->username }}</strong>
                </li>
                <li class="list-group-item px-0 d-flex justify-content-between align-items-center py-2">
                    <span class="text-secondary d-flex align-items-center gap-2">
                        <i class="bi bi-person-badge text-brand-green fs-6"></i> Status Role
                    </span>
                    <span class="badge bg-secondary-subtle text-secondary fw-semibold">Wali Murid</span>
                </li>
                <li class="list-group-item px-0 d-flex justify-content-between align-items-center py-2">
                    <span class="text-secondary d-flex align-items-center gap-2">
                        <i class="bi bi-people text-brand-green fs-6"></i> Anak Terdaftar
                    </span>
                    <strong class="text-brand-navy">{{ $myAthletes->count() }} Siswa</strong>
                </li>
            </ul>

            @if($myAthletes->count() > 0)
                <div class="mt-3 text-start">
                    <small class="text-muted fw-bold text-uppercase d-block mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Daftar Siswa Terhubung:</small>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($myAthletes as $item)
                            <span class="badge bg-light text-dark border px-2 py-1 small">
                                <i class="bi bi-check2 text-success me-1"></i>{{ $item->nama }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Kartu Tips Keamanan -->
        <div class="card card-custom p-4 bg-white border-start border-4 shadow-sm" style="border-left-color: #f59e0b !important;">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-shield-lock-fill text-warning fs-5"></i>
                <h6 class="mb-0 fw-bold text-brand-navy">Tips Keamanan Sandi</h6>
            </div>
            <p class="small text-secondary mb-2">
                Jangan gunakan tanggal lahir anak atau kata sandi yang mudah ditebak oleh orang lain.
            </p>
            <ul class="small text-secondary ps-3 mb-0">
                <li>Minimal 8 karakter.</li>
                <li>Kombinasikan huruf besar, huruf kecil, dan angka.</li>
                <li>Ganti sandi secara berkala untuk menjaga kerahasiaan data anak.</li>
            </ul>
        </div>
    </div>

    <!-- Kolom Kanan: Formulir Ubah Password -->
    <div class="col-lg-8">
        <div class="card card-custom p-4 p-md-5 border-top border-4 hover-elevate" style="border-top-color: #0f172a !important;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 pb-3 border-bottom">
                <div>
                    <h4 class="fw-bold text-brand-navy mb-1"><i class="bi bi-key-fill text-brand-green me-2"></i>Ubah Password Akun</h4>
                    <p class="text-secondary small mb-0">Perbarui kata sandi Anda untuk meningkatkan perlindungan data dan privasi akun.</p>
                </div>
                <span class="badge bg-warning bg-opacity-10 text-dark border px-3 py-2 rounded-pill">
                    <i class="bi bi-lock me-1"></i> Enkripsi Bcrypt Aman
                </span>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 rounded-3 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2 mb-1 fw-bold">
                        <i class="bi bi-exclamation-octagon-fill fs-5"></i> Periksa kembali isian form Anda:
                    </div>
                    <ul class="mb-0 ps-3 small">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('wali.profile.update-password') }}" method="POST">
                @csrf

                <!-- Password Saat Ini -->
                <div class="mb-4">
                    <label for="current_password" class="form-label fw-bold text-brand-navy small">
                        Password Saat Ini <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-secondary"></i></span>
                        <input type="password" 
                               name="current_password" 
                               id="current_password" 
                               class="form-control border-start-0 @error('current_password') is-invalid @enderror" 
                               placeholder="Masukkan password yang sedang digunakan saat ini" 
                               required>
                    </div>
                    <small class="text-muted" style="font-size: 0.78rem;">
                        Jika ini pertama kali Anda login dan belum pernah mengganti password, masukkan password bawaan (tanggal lahir siswa format <strong>DDMMYYYY</strong> atau <strong>superseed123</strong>).
                    </small>
                </div>

                <hr class="my-4 text-muted opacity-25">

                <!-- Password Baru -->
                <div class="mb-3">
                    <label for="password" class="form-label fw-bold text-brand-navy small">
                        Password Baru <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-lock-fill text-brand-green"></i></span>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="form-control border-start-0 @error('password') is-invalid @enderror" 
                               placeholder="Minimal 8 karakter baru" 
                               required>
                    </div>
                </div>

                <!-- Konfirmasi Password Baru -->
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label fw-bold text-brand-navy small">
                        Konfirmasi Password Baru <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-check2-circle text-brand-green"></i></span>
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               class="form-control border-start-0" 
                               placeholder="Ulangi password baru persis sama" 
                               required>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="d-flex justify-content-end gap-2 pt-2">
                    <button type="submit" class="btn btn-brand-green text-white px-4 py-2 fw-semibold rounded-3 shadow-sm hover-elevate" style="background-color: var(--brand-green);">
                        <i class="bi bi-save me-1"></i> Simpan Password Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
