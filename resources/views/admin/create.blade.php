@extends('layouts.admin')
@section('title', 'Tambah Data Atlet Baru')

@section('content')
<div class="card card-custom bg-white p-4 max-w-2xl">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h5 class="mb-0">Form Input Data Atlet / Murid</h5>
        <a href="{{ route('admin.athletes.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.athletes.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label font-weight-bold">Nama Lengkap Atlet <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required placeholder="Contoh: Raditya Pratama">
            </div>
            <div class="col-md-4">
                <label class="form-label font-weight-bold">Nomor Punggung</label>
                <input type="number" name="nomor_punggung" class="form-control" value="{{ old('nomor_punggung') }}" placeholder="Contoh: 10">
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Posisi Bermain</label>
                <select name="posisi_bermain" class="form-select">
                    <option value="">-- Pilih Posisi --</option>
                    <option value="Penyerang (Forward)" {{ old('posisi_bermain') == 'Penyerang (Forward)' ? 'selected' : '' }}>Penyerang (Forward)</option>
                    <option value="Gelandang (Midfielder)" {{ old('posisi_bermain') == 'Gelandang (Midfielder)' ? 'selected' : '' }}>Gelandang (Midfielder)</option>
                    <option value="Pemain Bertahan (Defender)" {{ old('posisi_bermain') == 'Pemain Bertahan (Defender)' ? 'selected' : '' }}>Pemain Bertahan (Defender)</option>
                    <option value="Penjaga Gawang (Goalkeeper)" {{ old('posisi_bermain') == 'Penjaga Gawang (Goalkeeper)' ? 'selected' : '' }}>Penjaga Gawang (Goalkeeper)</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Nomor WA Orang Tua/Wali <span class="text-danger">*</span></label>
                <input type="text" name="nomor_wa_ortu" class="form-control" value="{{ old('nomor_wa_ortu') }}" required placeholder="Contoh: 081234567890">
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Nomor WA Siswa (Opsional)</label>
                <input type="text" name="nomor_wa" class="form-control" value="{{ old('nomor_wa') }}" placeholder="Contoh: 089876543210">
            </div>
            <div class="col-md-12">
                <label class="form-label font-weight-bold">Kaitkan dengan Akun Login Wali Murid</label>
                <select name="user_id" class="form-select">
                    <option value="">-- Tidak dikaitkan / Pilih Akun Orang Tua --</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}" {{ old('user_id') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }} (Username: {{ $parent->username }})
                        </option>
                    @endforeach
                </select>
                <div class="form-text text-muted">Agar orang tua dapat melihat raport dan absensi anak ini di Modul Portal Wali Murid.</div>
            </div>
            <div class="col-md-12">
                <label class="form-label font-weight-bold">Alamat Lengkap</label>
                <textarea name="alamat" class="form-control" rows="3" placeholder="Jl. Raya Malang No. 123...">{{ old('alamat') }}</textarea>
            </div>
        </div>

        <div class="mt-4 pt-3 border-top d-flex justify-content-end">
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Simpan Data Atlet</button>
        </div>
    </form>
</div>
@endsection