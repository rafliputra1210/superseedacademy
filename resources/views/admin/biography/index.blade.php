@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header Bagian Atas -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Biografi Akademisi</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola profil utama, kelas, dan status keaktifan atlet.</p>
        </div>
        <a href="{{ route('admin.biography.create') }}" class="bg-brand-blue hover:bg-emerald-700 text-white font-medium py-2.5 px-5 rounded-lg inline-flex items-center gap-2 transition-colors shadow-sm">
            <i class="bi bi-person-plus-fill"></i> Tambah Biografi
        </a>
    </div>

    <!-- Alert Sukses -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg mb-6 flex items-center gap-3 shadow-sm">
            <i class="bi bi-check-circle-fill text-emerald-500 text-lg"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Panel Filter & Pencarian -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6">
        <form action="{{ route('admin.biography.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <!-- Cari Nama / NIS -->
            <div class="flex-1">
                <div class="relative">
                    <i class="bi bi-search absolute left-3.5 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Cari nama atlet atau No. Induk..." 
                        class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-brand-blue outline-none transition-all text-sm">
                </div>
            </div>
            
            <!-- Filter Kategori Kelas -->
            <div class="w-full md:w-64">
                <select name="kategori" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-brand-blue outline-none transition-all text-sm bg-white cursor-pointer">
                    <option value="">Semua Kategori Kelas</option>
                    <option value="01" {{ request('kategori') == '01' ? 'selected' : '' }}>NEW BORN CLASS</option>
                    <option value="02" {{ request('kategori') == '02' ? 'selected' : '' }}>REGULER CLASS U-10</option>
                    <option value="03" {{ request('kategori') == '03' ? 'selected' : '' }}>REGULER CLASS U-13</option>
                    <option value="04" {{ request('kategori') == '04' ? 'selected' : '' }}>REGULER CLASS U-16</option>
                </select>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="bg-brand-navy hover:bg-slate-800 text-white font-medium py-2.5 px-6 rounded-lg transition-colors text-sm shadow-sm">
                    Filter Data
                </button>
                @if(request('search') || request('kategori'))
                    <a href="{{ route('admin.biography.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium py-2.5 px-4 rounded-lg transition-colors text-sm text-center flex items-center">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Data Biografi -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs text-slate-500 uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">No</th>
                        <th class="px-6 py-4 font-semibold">Profil Atlet</th>
                        <th class="px-6 py-4 font-semibold">Kategori Kelas</th>
                        <th class="px-6 py-4 font-semibold">Posisi</th>
                        <th class="px-6 py-4 font-semibold text-center">Status</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($biographies as $index => $bio)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ $biographies->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <!-- Foto Profil -->
                                    @if($bio->foto_profil)
                                        <img src="{{ asset('storage/' . $bio->foto_profil) }}" alt="Foto {{ $bio->athlete->nama ?? '' }}" 
                                            class="w-10 h-10 rounded-full object-cover border border-slate-200 shadow-sm">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400">
                                            <i class="bi bi-person-fill text-xl"></i>
                                        </div>
                                    @endif
                                    
                                    <!-- Nama & NIS -->
                                    <div>
                                        <div class="font-bold text-slate-800">{{ $bio->athlete->nama ?? 'Data Atlet Terhapus' }}</div>
                                        <div class="text-xs text-slate-500 font-medium">NIS: {{ $bio->athlete->nomor_induk ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <!-- Badge Kategori Kelas -->
                                @php
                                    $badges = [
                                        '01' => ['label' => 'NEW BORN', 'color' => 'bg-emerald-100 text-emerald-800 border-emerald-200'],
                                        '02' => ['label' => 'U-10', 'color' => 'bg-blue-100 text-blue-800 border-blue-200'],
                                        '03' => ['label' => 'U-13', 'color' => 'bg-purple-100 text-purple-800 border-purple-200'],
                                        '04' => ['label' => 'U-16', 'color' => 'bg-amber-100 text-amber-800 border-amber-200'],
                                    ];
                                    $kategori = $badges[$bio->kategori_kelas] ?? ['label' => 'Unknown', 'color' => 'bg-slate-100 text-slate-800 border-slate-200'];
                                @endphp
                                <span class="px-2.5 py-1 text-xs font-bold rounded-md border {{ $kategori['color'] }}">
                                    {{ $kategori['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-700">
                                {{ $bio->posisi ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <!-- Status Keaktifan -->
                                @if($bio->status_aktif)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <!-- Tombol Aksi -->
                                <div class="flex items-center justify-end gap-1 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                                    <!-- Tombol Detail -->
                                    <a href="#" class="p-2 text-slate-400 hover:text-brand-blue hover:bg-blue-50 rounded-lg transition-colors" title="Lihat Detail Biografi">
                                        <i class="bi bi-eye text-lg"></i>
                                    </a>
                                    <!-- Tombol Edit -->
                                    <a href="{{ route('admin.biography.edit', $bio->id) }}" class="p-2 text-slate-400 hover:text-amber-500 hover:bg-amber-50 rounded-lg transition-colors" title="Edit Biografi">
                                        <i class="bi bi-pencil-square text-lg"></i>
                                    </a>
                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('admin.biography.destroy', $bio->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus profil biografi ini? Data tidak dapat dikembalikan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Biografi">
                                            <i class="bi bi-trash text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-500">
                                    <i class="bi bi-person-vcard text-5xl mb-4 text-slate-300"></i>
                                    <p class="text-lg font-bold text-slate-700">Belum ada Biografi Atlet</p>
                                    <p class="text-sm mt-1 mb-4">Silakan tambahkan data biografi untuk mulai melacak profil dan statistik atlet.</p>
                                    <a href="{{ route('admin.biography.create') }}" class="text-sm font-medium text-brand-blue hover:underline">
                                        + Tambah Data Sekarang
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginasi -->
        @if($biographies->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $biographies->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection