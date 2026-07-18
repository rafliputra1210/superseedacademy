@extends('layouts.admin')
@section('title', 'Edit Data Atlet')

@section('content')
<div class="card card-custom bg-white p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h5 class="mb-0">Edit Data Atlet: <strong>{{ $athlete->nama }}</strong></h5>
        <a href="{{ route('admin.athletes.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
    </div>

    <form action="{{ route('admin.athletes.update', $athlete->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label font-weight-bold">Nama Lengkap Atlet <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama', $athlete->nama) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label font-weight-bold">Nomor Punggung</label>
                <input type="number" name="nomor_punggung" class="form-control" value="{{ old('nomor_punggung', $athlete->nomor_punggung) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Posisi Bermain</label>
                <select name="posisi_bermain" class="form-select">
                    <option value="">-- Pilih Posisi --</option>
                    @foreach(['Penyerang (Forward)', 'Gelandang (Midfielder)', 'Pemain Bertahan (Defender)', 'Penjaga Gawang (Goalkeeper)'] as $posisi)
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
                <label class="form-label font-weight-bold">Kaitkan dengan Akun Login Wali Murid</label>
                <select name="user_id" class="form-select">
                    <option value="">-- Tidak dikaitkan / Pilih Akun Orang Tua --</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}" {{ old('user_id', $athlete->user_id) == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }} (Username: {{ $parent->username }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12">
                <label class="form-label font-weight-bold">Alamat Lengkap</label>
                <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $athlete->alamat) }}</textarea>
            </div>
        </div>

        <div class="mt-4 pt-3 border-top d-flex justify-content-end">
            <button type="submit" class="btn btn-warning px-4"><i class="bi bi-save me-1"></i> Perbarui Data Atlet</button>
        </div>
    </form>
</div>
@endsection