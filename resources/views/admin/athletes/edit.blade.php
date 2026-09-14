@extends('layouts.admin')
@section('title', 'Edit Data Atlet')

@section('content')
<div class="card card-custom bg-white p-4 max-w-4xl">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h5 class="mb-0 font-weight-bold">Edit Data Siswa: <span class="text-primary">{{ $athlete->nama }}</span></h5>
            <p class="text-muted small mb-0">Perbarui biodata siswa atau reset password akun login portal wali murid.</p>
        </div>
        <a href="{{ route('admin.athletes.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 small">@foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('admin.athletes.update', $athlete->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- BIODATA ATLET -->
        <h6 class="text-primary font-weight-bold mb-3"><i class="bi bi-person-fill me-2"></i>A. Biodata Siswa / Atlet</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-8">
                <label class="form-label font-weight-bold">Nama Lengkap Siswa <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama', $athlete->nama) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-weight-bold">Nomor Punggung</label>
                <input type="number" name="nomor_punggung" class="form-control" value="{{ old('nomor_punggung', $athlete->nomor_punggung) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Kelompok Umur <span class="text-danger">*</span></label>
                <input type="text" name="kelompok_umur" list="list_ku" class="form-control border-primary" value="{{ old('kelompok_umur', $athlete->kelompok_umur) }}" required placeholder="Contoh: U-12">
                <datalist id="list_ku">
                    @foreach($kelompokUmurList ?? [] as $ku)
                        <option value="{{ $ku }}"></option>
                    @endforeach
                </datalist>
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Kelompok Latihan <span class="text-danger">*</span></label>
                <input type="text" name="kelompok_latihan" list="list_kl" class="form-control border-success" value="{{ old('kelompok_latihan', $athlete->kelompok_latihan) }}" required placeholder="Contoh: Kelas Reguler">
                <datalist id="list_kl">
                    @foreach($kelompokLatihanList ?? [] as $kl)
                        <option value="{{ $kl }}"></option>
                    @endforeach
                </datalist>
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Posisi Bermain</label>
                <select name="posisi_bermain" class="form-select">
                    <option value="">-- Pilih Posisi --</option>
                    @foreach(['Kiper', 'Flank Kanan', 'Flank Kiri', 'Pivot','Anchor', 'All Position'] as $posisi)
                        <option value="{{ $posisi }}" {{ old('posisi_bermain', $athlete->posisi_bermain) == $posisi ? 'selected' : '' }}>{{ $posisi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $athlete->tanggal_lahir) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Nomor WA Orang Tua/Wali <span class="text-danger">*</span></label>
                <input type="text" name="nomor_wa_ortu" class="form-control" value="{{ old('nomor_wa_ortu', $athlete->nomor_wa_ortu) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Nomor WA Siswa (Opsional)</label>
                <input type="text" name="nomor_wa" class="form-control" value="{{ old('nomor_wa', $athlete->nomor_wa) }}">
            </div>
            <div class="col-md-12">
                <label class="form-label font-weight-bold">Alamat Domisili</label>
                <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $athlete->alamat) }}</textarea>
            </div>
            <div class="col-md-12">
                <label class="form-label font-weight-bold">Foto Profil</label>
                @if($athlete->foto)
                    <div class="mb-2">
                        <img src="{{ asset($athlete->foto) }}" alt="Foto {{ $athlete->nama }}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                    </div>
                @endif
                <input type="file" name="foto" class="form-control" accept="image/*">
                <div class="form-text text-xs">Biarkan kosong jika tidak ingin mengubah foto. Format: JPG/PNG/WEBP. Maksimal 2MB.</div>
            </div>
        </div>

        <!-- PENGATURAN AKUN WALI TERHUBUNG -->
        <div class="p-4 bg-light rounded border mb-4">
            <h6 class="text-success font-weight-bold mb-3"><i class="bi bi-key-fill me-2"></i>B. Informasi Akun Login Wali Murid</h6>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small font-weight-bold">Akun Wali Murid yang Terhubung:</label>
                    <select name="user_id" class="form-select">
                        <option value="">-- Tidak Dikaitkan ke Akun Manapun --</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}" {{ old('user_id', $athlete->user_id) == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }} — (Username: {{ $parent->username }})
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($athlete->user)
                <div class="col-md-6">
                    <label class="form-label small font-weight-bold text-danger">Reset Password Akun (Opsional):</label>
                    <input type="text" name="new_password_wali" class="form-control border-danger" placeholder="Ketik password baru jika ingin mereset...">
                    <div class="form-text text-xs">Kosongkan jika tidak ingin mengubah password akun <b>{{ $athlete->user->username }}</b>.</div>
                </div>
                @endif
            </div>
        </div>

        <div class="pt-3 border-top d-flex justify-content-end gap-2">
            <a href="{{ route('admin.athletes.index') }}" class="btn btn-light border px-4">Batal</a>
            <button type="submit" class="btn btn-warning px-5 font-weight-bold">
                <i class="bi bi-save me-1"></i> Perbarui Data Siswa
            </button>
        </div>
    </form>
</div>
@endsection