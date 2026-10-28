@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Biografi: {{ $biography->athlete->nama ?? '-' }}</h2>
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

    <form action="{{ route('admin.biography.update', $biography->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Atlet (Read Only) -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Atlet</label>
                <input type="text" value="{{ $biography->athlete->nama ?? '-' }} (NIS: {{ $biography->athlete->nomor_induk ?? '-' }})" disabled class="w-full px-4 py-2 border border-slate-200 bg-slate-50 text-slate-500 rounded-lg cursor-not-allowed">
                <input type="hidden" name="athlete_id" value="{{ $biography->athlete_id }}">
            </div>

            <!-- Kategori Kelas -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Kategori Kelas *</label>
                <select name="kategori_kelas" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-brand-blue focus:border-brand-blue">
                    <option value="01" {{ (old('kategori_kelas', $biography->kategori_kelas) == '01') ? 'selected' : '' }}>NEW BORN CLASS</option>
                    <option value="02" {{ (old('kategori_kelas', $biography->kategori_kelas) == '02') ? 'selected' : '' }}>REGULER CLASS U-10</option>
                    <option value="03" {{ (old('kategori_kelas', $biography->kategori_kelas) == '03') ? 'selected' : '' }}>REGULER CLASS U-13</option>
                    <option value="04" {{ (old('kategori_kelas', $biography->kategori_kelas) == '04') ? 'selected' : '' }}>REGULER CLASS U-16</option>
                </select>
            </div>

            <!-- Tanggal Bergabung & Status -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Bergabung *</label>
                    <input type="date" name="tanggal_bergabung" value="{{ old('tanggal_bergabung', $biography->tanggal_bergabung) }}" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-brand-blue focus:border-brand-blue">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Status Aktif</label>
                    <select name="status_aktif" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-brand-blue">
                        <option value="1" {{ old('status_aktif', $biography->status_aktif) == 1 ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('status_aktif', $biography->status_aktif) == 0 ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            <!-- Posisi -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Posisi Bermain</label>
                <select name="posisi" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-brand-blue focus:border-brand-blue">
                    @php $posisiVal = old('posisi', $biography->posisi); @endphp
                    <option value="">-- Pilih Posisi --</option>
                    <option value="Kiper" {{ $posisiVal == 'Kiper' ? 'selected' : '' }}>Kiper</option>
                    <option value="Anchor" {{ $posisiVal == 'Anchor' ? 'selected' : '' }}>Anchor</option>
                    <option value="Flank" {{ $posisiVal == 'Flank' ? 'selected' : '' }}>Flank</option>
                    <option value="Pivot" {{ $posisiVal == 'Pivot' ? 'selected' : '' }}>Pivot</option>
                    <option value="Universal" {{ $posisiVal == 'Universal' ? 'selected' : '' }}>Universal</option>
                </select>
            </div>

            <!-- Fisik & Atribut -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">No. Punggung</label>
                    <input type="number" name="nomor_punggung" value="{{ old('nomor_punggung', $biography->nomor_punggung) }}" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-brand-blue">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Kaki Dominan</label>
                    <select name="kaki_dominan" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-brand-blue">
                        @php $kaki = old('kaki_dominan', $biography->kaki_dominan); @endphp
                        <option value="">-- Pilih --</option>
                        <option value="Kanan" {{ $kaki == 'Kanan' ? 'selected' : '' }}>Kanan</option>
                        <option value="Kiri" {{ $kaki == 'Kiri' ? 'selected' : '' }}>Kiri</option>
                        <option value="Keduanya" {{ $kaki == 'Keduanya' ? 'selected' : '' }}>Keduanya</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tinggi (cm)</label>
                    <input type="number" name="tinggi_cm" value="{{ old('tinggi_cm', $biography->tinggi_cm) }}" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-brand-blue">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Berat (kg)</label>
                    <input type="number" step="0.1" name="berat_kg" value="{{ old('berat_kg', $biography->berat_kg) }}" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-brand-blue">
                </div>
            </div>
            
            <!-- Update Foto -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Update Foto Profil</label>
                @if($biography->foto_profil)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $biography->foto_profil) }}" class="w-20 h-20 object-cover rounded-lg border border-slate-200">
                    </div>
                @endif
                <input type="file" name="foto_profil" accept="image/*" class="w-full px-4 py-2 border border-slate-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-brand-blue hover:file:bg-blue-100">
                <p class="text-xs text-slate-500 mt-1">Biarkan kosong jika tidak ingin mengubah foto.</p>
            </div>

        </div>

        <div class="mt-8 flex justify-end gap-3 border-t pt-5">
            <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-medium transition-colors">Perbarui Biografi</button>
        </div>
    </form>
</div>
@endsection