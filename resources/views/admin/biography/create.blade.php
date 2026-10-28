@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tambah Biografi Akademisi</h2>
            <a href="{{ route('admin.biography.index') }}" class="text-sm text-brand-blue hover:underline">&larr; Kembali ke Daftar</a>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-rose-50 text-rose-600 p-4 rounded-lg mb-6">
            <ul class="list-disc pl-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.biography.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Pilih Atlet -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Atlet (Murid) *</label>
                <select name="athlete_id" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-brand-blue focus:border-brand-blue">
                    <option value="">-- Pilih Atlet --</option>
                    @foreach($athletes as $atlet)
                        <option value="{{ $atlet->id }}" {{ old('athlete_id') == $atlet->id ? 'selected' : '' }}>
                            {{ $atlet->nama }} (NIS: {{ $atlet->nomor_induk }})
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-500 mt-1">Hanya menampilkan atlet yang belum memiliki biografi.</p>
            </div>

            <!-- Kategori Kelas -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Kategori Kelas *</label>
                <select name="kategori_kelas" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-brand-blue focus:border-brand-blue">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="01" {{ old('kategori_kelas') == '01' ? 'selected' : '' }}>NEW BORN CLASS</option>
                    <option value="02" {{ old('kategori_kelas') == '02' ? 'selected' : '' }}>REGULER CLASS U-10</option>
                    <option value="03" {{ old('kategori_kelas') == '03' ? 'selected' : '' }}>REGULER CLASS U-13</option>
                    <option value="04" {{ old('kategori_kelas') == '04' ? 'selected' : '' }}>REGULER CLASS U-16</option>
                </select>
            </div>

            <!-- Tanggal Bergabung -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Bergabung *</label>
                <input type="date" name="tanggal_bergabung" value="{{ old('tanggal_bergabung') }}" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-brand-blue focus:border-brand-blue">
            </div>

            <!-- Posisi -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Posisi Bermain</label>
                <select name="posisi" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-brand-blue focus:border-brand-blue">
                    <option value="">-- Pilih Posisi --</option>
                    <option value="Kiper">Kiper</option>
                    <option value="Anchor">Anchor</option>
                    <option value="Flank">Flank</option>
                    <option value="Pivot">Pivot</option>
                    <option value="Universal">Universal</option>
                </select>
            </div>

            <!-- Fisik & Atribut -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">No. Punggung</label>
                    <input type="number" name="nomor_punggung" value="{{ old('nomor_punggung') }}" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-brand-blue">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Kaki Dominan</label>
                    <select name="kaki_dominan" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-brand-blue">
                        <option value="">-- Pilih --</option>
                        <option value="Kanan">Kanan</option>
                        <option value="Kiri">Kiri</option>
                        <option value="Keduanya">Keduanya</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tinggi (cm)</label>
                    <input type="number" name="tinggi_cm" value="{{ old('tinggi_cm') }}" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-brand-blue">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Berat (kg)</label>
                    <input type="number" step="0.1" name="berat_kg" value="{{ old('berat_kg') }}" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-brand-blue">
                </div>
            </div>

            <!-- Foto Profil -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Foto Profil Atlet</label>
                <input type="file" name="foto_profil" accept="image/*" class="w-full px-4 py-2 border border-slate-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-brand-blue hover:file:bg-blue-100">
            </div>

        </div>

        <div class="mt-8 flex justify-end gap-3 border-t pt-5">
            <button type="reset" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition-colors">Reset</button>
            <button type="submit" class="px-5 py-2.5 bg-brand-blue hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">Simpan Biografi</button>
        </div>
    </form>
</div>
@endsection