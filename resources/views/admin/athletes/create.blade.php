@extends('layouts.admin')
@section('title', 'Tambah Data Atlet & Akun Wali')

@section('content')
<div class="card card-custom bg-white p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h5 class="mb-0 font-weight-bold">Form Input Siswa & Pengaktifan Akun Otomatis</h5>
            <p class="text-muted small mb-0">Setiap data siswa yang ditambahkan akan otomatis dibuatkan akun login portal untuk orang tua/wali.</p>
        </div>
        <a href="{{ route('admin.athletes.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong class="d-block mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terdapat kesalahan input!</strong>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.athletes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- LAYOUT 2 KOLOM -->
        <div class="row g-4">
            
            <!-- KOLOM KIRI: BIODATA MURID -->
            <div class="col-lg-7 border-end-lg pe-lg-4">
                <h6 class="text-primary font-weight-bold mb-3"><i class="bi bi-person-fill me-2"></i>A. Biodata Siswa / Atlet</h6>
                
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label font-weight-bold">Nama Lengkap Siswa <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="input_nama" class="form-control" value="{{ old('nama') }}" required placeholder="Contoh: Rafli Putra" oninput="updateLivePreview()">
                        <div class="form-text text-xs">Akan otomatis diubah menjadi Username login (tanpa spasi).</div>
                    </div>
                    <div class="col-md-6">
            <label class="form-label font-weight-bold">Kelompok Umur <span class="text-danger">*</span></label>
            <input type="text" name="kelompok_umur" class="form-control border-primary" value="{{ old('kelompok_umur') }}" required placeholder="Contoh: U-12">
        </div>

        <!-- TAMBAHAN: KELOMPOK LATIHAN -->
        <div class="col-md-6">
            <label class="form-label font-weight-bold">Kelompok Latihan <span class="text-danger">*</span></label>
            <input type="text" name="kelompok_latihan" class="form-control border-success" value="{{ old('kelompok_latihan') }}" required placeholder="Contoh: Kelas Reguler">
        </div>
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold">Nomor Punggung</label>
                        <input type="number" name="nomor_punggung" class="form-control" value="{{ old('nomor_punggung') }}" placeholder="Contoh: 10">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_lahir" id="input_tgl" class="form-control" value="{{ old('tanggal_lahir') }}" required onchange="updateLivePreview()">
                        <div class="form-text text-xs text-success font-weight-bold">Otomatis menjadi Password (format DD-MM-YYYY).</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Posisi Bermain</label>
                        <select name="posisi_bermain" class="form-select">
                            <option value="">-- Pilih Posisi --</option>
                            @foreach(['Kiper', 'Flank Kanan', 'Flank Kiri', 'Pivot','Anchor', 'All Position'] as $posisi)
                                <option value="{{ $posisi }}" {{ old('posisi_bermain') == $posisi ? 'selected' : '' }}>{{ $posisi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Nomor WA Orang Tua/Wali <span class="text-danger">*</span></label>
                        <input type="text" name="nomor_wa_ortu" class="form-control" value="{{ old('nomor_wa_ortu') }}" required placeholder="">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Nomor WA Siswa (Opsional)</label>
                        <input type="text" name="nomor_wa" class="form-control" value="{{ old('nomor_wa') }}" placeholder="">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label font-weight-bold">Alamat Domisili</label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Jl. Raya No. 123...">{{ old('alamat') }}</textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label font-weight-bold">Foto Profil (Opsional)</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                        <div class="form-text text-xs">Format: JPG/PNG/WEBP. Maksimal 2MB.</div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: PENGAKTIFAN AKUN WALI (LANGSUNG AKTIF) -->
            <div class="col-lg-5">
                <div class="p-4 bg-light rounded-3 border h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="text-success font-weight-bold mb-0"><i class="bi bi-shield-lock-fill me-2"></i>B. Pengaktifan Akun Portal Wali</h6>
                            <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i> Aktif Otomatis</span>
                        </div>
                        
                        <!-- Input Nama Wali -->
                        <div class="mb-3">
                            <label class="form-label small font-weight-bold">Nama Orang Tua / Wali <span class="text-danger">*</span></label>
                            <input type="text" name="nama_wali" class="form-control font-weight-bold" value="{{ old('nama_wali') }}" required placeholder="Contoh: Budi">
                            <div class="form-text text-xs">Nama pemilik akun yang akan tampil saat login di portal.</div>
                        </div>

                        <!-- KOTAK LIVE PREVIEW -->
                        <div class="p-3 bg-white rounded border border-success border-opacity-50 mt-4 shadow-sm">
                            <span class="badge bg-success mb-3"><i class="bi bi-eye-fill me-1"></i> Live Preview Kredensial:</span>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2 text-sm">
                                <span class="text-muted small">Username Login:</span>
                                <code id="preview_user" class="fs-6 text-primary font-weight-bold">ketik.nama.murid...</code>
                            </div>
                            <div class="d-flex justify-content-between align-items-center text-sm">
                                <span class="text-muted small">Password (DD-MM-YYYY):</span>
                                <code id="preview_pass" class="fs-6 text-dark font-weight-bold">pilih.tgl.lahir...</code>
                            </div>
                            
                            <hr class="my-3">
                            <small class="text-muted text-xs d-block" style="line-height: 1.4;">
                                💡 Sistem otomatis mengubah nama murid menjadi lowercase tanpa spasi untuk <b>Username</b>, dan merubah tanggal lahir ke angka format <b>DD-MM-YYYY</b> untuk <b>Password</b>.
                            </small>
                        </div>
                    </div>

                    <!-- Tombol Simpan -->
                    <div class="pt-3 border-top mt-4">
                        <button type="submit" class="btn btn-primary w-100 py-2.5 font-weight-bold shadow">
                            <i class="bi bi-save me-1"></i> Simpan Siswa & Aktifkan Akun
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<!-- SCRIPT LIVE PREVIEW -->
<script>
    function updateLivePreview() {
        const nama = document.getElementById('input_nama').value;
        const tgl = document.getElementById('input_tgl').value;
        
        let username = nama.toLowerCase().replace(/[^a-z0-9]/g, '');
        document.getElementById('preview_user').innerText = username ? username : 'ketik.nama.murid...';
        
        // Generate Password format DDMMYYYY TANPA STRIP (Contoh: 04072026)
        if (tgl) {
            const parts = tgl.split('-'); 
            if (parts.length === 3) {
                // Gabungkan tanpa tanda penghubung
                document.getElementById('preview_pass').innerText = `${parts[2]}${parts[1]}${parts[0]}`;
            }
        } else {
            document.getElementById('preview_pass').innerText = 'pilih.tgl.lahir...';
        }
    }

    window.onload = function() {
        updateLivePreview();
    };
</script>
@endsection