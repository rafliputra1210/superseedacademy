@extends('layouts.admin')

@section('title', 'Tambah Biografi')

@section('content')
<div class="container-fluid px-4 py-6 max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tambah Biografi Akademisi</h2>
            <a href="{{ route('admin.biography.index') }}" class="text-sm text-brand-blue hover:underline inline-flex items-center gap-1 mt-1">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    {{-- Error Validation --}}
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-xl mb-6">
            <div class="flex items-center gap-2 font-semibold mb-1"><i class="bi bi-exclamation-circle-fill"></i> Terdapat kesalahan input:</div>
            <ul class="list-disc pl-5 text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.biography.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Card: Pilih Atlet & Kelas --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 mb-5">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-slate-400 inline-block"></span>
                <h3 class="font-semibold text-slate-700 text-sm uppercase tracking-wider">Informasi Atlet</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Pilih Atlet --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pilih Atlet (Murid) <span class="text-rose-500">*</span></label>
                    <select name="athlete_id" required
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih Atlet --</option>
                        @foreach($athletes as $atlet)
                            <option value="{{ $atlet->id }}" {{ old('athlete_id') == $atlet->id ? 'selected' : '' }}>
                                {{ $atlet->nama }} (NIS: {{ $atlet->nomor_induk }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Hanya menampilkan atlet yang belum memiliki biografi.</p>
                </div>

                {{-- Kategori Kelas --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kategori Kelas <span class="text-rose-500">*</span></label>
                    <select name="kategori_kelas" required
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih Kategori --</option>
                        <option value="01" {{ old('kategori_kelas') == '01' ? 'selected' : '' }}>NEW BORN CLASS</option>
                        <option value="02" {{ old('kategori_kelas') == '02' ? 'selected' : '' }}>REGULER CLASS U-10</option>
                        <option value="03" {{ old('kategori_kelas') == '03' ? 'selected' : '' }}>REGULER CLASS U-13</option>
                        <option value="04" {{ old('kategori_kelas') == '04' ? 'selected' : '' }}>REGULER CLASS U-16</option>
                    </select>
                </div>

                {{-- Tanggal Bergabung --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Bergabung <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal_bergabung"
                           value="{{ old('tanggal_bergabung', date('Y-m-d')) }}"
                           required
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

            </div>
        </div>

        {{-- Card: Data Teknis & Fisik --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 mb-5">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span>
                <h3 class="font-semibold text-slate-700 text-sm uppercase tracking-wider">Data Teknis & Fisik</h3>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">

                {{-- Posisi Bermain --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Posisi Bermain</label>
                    <select name="posisi"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih Posisi --</option>
                        <option value="Kiper"     {{ old('posisi') == 'Kiper'     ? 'selected' : '' }}>Kiper</option>
                        <option value="Anchor"    {{ old('posisi') == 'Anchor'    ? 'selected' : '' }}>Anchor</option>
                        <option value="Flank"     {{ old('posisi') == 'Flank'     ? 'selected' : '' }}>Flank</option>
                        <option value="Pivot"     {{ old('posisi') == 'Pivot'     ? 'selected' : '' }}>Pivot</option>
                        <option value="Universal" {{ old('posisi') == 'Universal' ? 'selected' : '' }}>Universal</option>
                    </select>
                </div>

                {{-- No. Punggung --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">No. Punggung</label>
                    <input type="number" name="nomor_punggung" min="1" max="99"
                           value="{{ old('nomor_punggung') }}"
                           placeholder="Contoh: 10"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                {{-- Kaki Dominan --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kaki Dominan</label>
                    <select name="kaki_dominan"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih --</option>
                        <option value="Kanan"    {{ old('kaki_dominan') == 'Kanan'    ? 'selected' : '' }}>Kanan</option>
                        <option value="Kiri"     {{ old('kaki_dominan') == 'Kiri'     ? 'selected' : '' }}>Kiri</option>
                        <option value="Keduanya" {{ old('kaki_dominan') == 'Keduanya' ? 'selected' : '' }}>Keduanya</option>
                    </select>
                </div>

                {{-- Tinggi --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tinggi Badan</label>
                    <div class="relative">
                        <input type="number" name="tinggi_cm"
                               value="{{ old('tinggi_cm') }}"
                               placeholder="165"
                               class="w-full px-4 py-2.5 pr-12 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">cm</span>
                    </div>
                </div>

                {{-- Berat --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Berat Badan</label>
                    <div class="relative">
                        <input type="number" step="0.1" name="berat_kg"
                               value="{{ old('berat_kg') }}"
                               placeholder="60.0"
                               class="w-full px-4 py-2.5 pr-12 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">kg</span>
                    </div>
                </div>

            </div>
        </div>

        {{-- Card: Foto Profil --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 mb-5">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block"></span>
                <h3 class="font-semibold text-slate-700 text-sm uppercase tracking-wider">Foto Profil</h3>
            </div>
            <div class="p-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5 bg-slate-50 rounded-xl p-5 border border-slate-200">
                    {{-- Placeholder Avatar --}}
                    <div class="shrink-0">
                        <div class="w-20 h-20 rounded-xl bg-slate-200 border border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400">
                            <i class="bi bi-person-fill text-3xl"></i>
                            <span class="text-[10px] mt-0.5 font-medium">Belum Ada</span>
                        </div>
                    </div>
                    {{-- Input Upload --}}
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Unggah Foto Atlet</label>
                        <input type="file" name="foto_profil" accept="image/*"
                               class="block w-full text-sm text-slate-500
                                      file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                      file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700
                                      hover:file:bg-blue-100 border border-slate-300 rounded-lg bg-white p-1">
                        <p class="text-xs text-slate-500 mt-1.5">Format JPG, PNG, WEBP · Maks. 2MB. Opsional.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="reset"
                    class="px-5 py-2.5 border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors shadow-sm">
                Reset
            </button>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-brand-blue hover:bg-blue-700 text-white rounded-lg font-semibold text-sm transition-colors shadow-sm">
                <i class="bi bi-save-fill"></i>
                Simpan Biografi
            </button>
        </div>

    </form>
</div>
@endsection
