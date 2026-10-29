@extends('layouts.admin')

@section('title', 'Detail Biografi - ' . ($biography->athlete->nama ?? $biography->nama_atlet ?? 'Atlet'))

@section('content')
@php
    \Carbon\Carbon::setLocale('id');

    $stats        = $biography->statistics ?? collect();
    $achievements = $biography->achievements ?? collect();

    $totalMain   = $stats->count();
    $totalMenit  = $stats->sum('menit_bermain');
    $totalGol    = $stats->sum('gol');
    $totalAssist = $stats->sum('assist');

    $aktif = (bool) ($biography->status_aktif ?? true);

    $bergabung = $biography->tanggal_bergabung
        ? \Carbon\Carbon::parse($biography->tanggal_bergabung)
        : null;

    $lama = $bergabung ? $bergabung->diff(now()) : null;

    $ringkasan = [
        ['label' => 'PERTANDINGAN', 'val' => $totalMain, 'icon' => 'bi-flag-fill', 'bg' => '#f1f5f9', 'fg' => '#475569'],
        ['label' => 'GOL', 'val' => $totalGol, 'icon' => 'bi-bullseye', 'bg' => '#eff6ff', 'fg' => '#2563eb'],
        ['label' => 'ASSIST', 'val' => $totalAssist, 'icon' => 'bi-hand-thumbs-up-fill', 'bg' => '#ecfdf5', 'fg' => '#059669'],
        ['label' => 'MENIT MAIN', 'val' => $totalMenit, 'icon' => 'bi-stopwatch-fill', 'bg' => '#fffbeb', 'fg' => '#d97706'],
    ];
@endphp

<style>
    :root {
        --sp-navy: #0A192F;
        --sp-blue: #0066FF;
        --sp-slate-900: #0f172a;
        --sp-slate-700: #334155;
        --sp-slate-500: #64748b;
        --sp-slate-100: #f1f5f9;
        --sp-slate-50:  #f8fafc;
    }

    .sp-wrap { max-width: 1200px; margin: 0 auto; padding: 1.5rem 1rem; }
    .sp-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
    
    /* Hero Banner */
        /* Hero Banner & Profil */
    .sp-hero {
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
        border-radius: 1.25rem;
        background: #fff;
    }
    .sp-banner {
        height: 110px;
        background: linear-gradient(135deg, #0A192F 0%, #153E75 60%, #0066FF 100%);
    }
    .sp-hero-body {
        padding: 0 1.75rem 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }
    @media (min-width: 992px) {
        .sp-hero-body {
            flex-direction: row;
            align-items: flex-end;
            justify-content: space-between;
        }
    }
    .sp-profile-left {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }
    @media (min-width: 640px) {
        .sp-profile-left {
            flex-direction: row;
            align-items: flex-end;
        }
    }
    .sp-avatar {
        width: 110px;
        height: 110px;
        margin-top: -55px; /* Setengah di atas banner, setengah di latar putih */
        border-radius: 1.25rem;
        background: #fff;
        border: 4px solid #fff;
        box-shadow: 0 8px 24px rgba(10, 25, 47, 0.12);
        overflow: hidden;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 2;
    }
    .sp-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .sp-avatar-ph { font-size: 3.5rem; color: #cbd5e1; }

    .sp-hero-info {
        padding-bottom: 0.25rem;
    }
    .sp-name {
        font-size: 1.75rem;
        font-weight: 800;
        color: #0f172a; /* Warna gelap kontras di atas latar putih */
        margin: 0;
        line-height: 1.2;
        letter-spacing: -0.02em;
    }
    .sp-chips {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.75rem;
    }
    .sp-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.8rem;
        font-weight: 600;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .sp-chip-blue {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
        font-weight: 700;
    }
    .sp-chip-green {
        background: #ecfdf5;
        color: #047857;
        border-color: #a7f3d0;
        font-weight: 700;
    }
    .sp-chip-ok {
        background: #f0fdf4;
        color: #15803d;
        border-color: #bbf7d0;
    }
    .sp-chip-no {
        background: #fff1f2;
        color: #be123c;
        border-color: #fecdd3;
    }
    .sp-chip-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
    }

    /* Tombol Aksi (Kunci Sejajar 1 Baris) */
    .sp-actions {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        flex-wrap: nowrap; /* Mencegah tombol turun ke baris 2 */
        padding-bottom: 0.25rem;
        flex-shrink: 0;
    }
    @media (max-width: 991px) {
        .sp-actions {
            flex-wrap: wrap;
            width: 100%;
        }
    }
    .sp-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        padding: 0.65rem 1.15rem;
        border-radius: 0.65rem;
        font-size: 0.85rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease-in-out;
        white-space: nowrap;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .sp-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .sp-btn-light {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        color: #334155;
    }
    .sp-btn-light:hover { background: #f8fafc; color: #0f172a; border-color: #94a3b8; }
    .sp-btn-blue {
        background: #0066FF;
        border: 1.5px solid #0066FF;
        color: #ffffff;
    }
    .sp-btn-blue:hover { background: #0052cc; border-color: #0052cc; color: #ffffff; }
    .sp-btn-red {
        background: #ef4444;
        border: 1.5px solid #ef4444;
        color: #ffffff;
    }
    .sp-btn-red:hover { background: #dc2626; border-color: #dc2626; color: #ffffff; }

    /* Ringkasan Angka (4 Kartu) */
    .sp-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    @media (min-width: 768px) {
        .sp-stats { grid-template-columns: repeat(4, 1fr); }
    }
    .sp-stat-box { padding: 1.25rem; text-align: center; }
    .sp-stat-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: .5rem; }
    .sp-stat-label { font-size: .7rem; font-weight: 800; color: #64748b; letter-spacing: .05em; text-transform: uppercase; }
    .sp-stat-icon { width: 32px; height: 32px; border-radius: .5rem; display: flex; align-items: center; justify-content: center; font-size: .95rem; }
    .sp-stat-val { font-size: 2rem; font-weight: 800; color: #0f172a; line-height: 1; text-align: left; }

    /* Layout 2 Kolom */
    .sp-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    @media (min-width: 992px) {
        .sp-grid { grid-template-columns: 360px 1fr; }
    }

    /* Tabel Detail */
    .sp-sec-head { padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
    .sp-sec-title { font-size: .95rem; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: .5rem; }
    .sp-row { display: flex; justify-content: space-between; padding: .65rem 1.25rem; border-bottom: 1px solid #f8fafc; font-size: .85rem; }
    .sp-row:last-child { border-bottom: none; }
    .sp-row-lbl { color: #64748b; }
    .sp-row-val { font-weight: 600; color: #1e293b; text-align: right; }

    /* Tabel & List */
    .sp-table { width: 100%; border-collapse: collapse; font-size: .85rem; }
    .sp-table th { background: #f8fafc; padding: .75rem 1rem; color: #64748b; font-size: .75rem; text-transform: uppercase; font-weight: 700; border-bottom: 1px solid #e2e8f0; }
    .sp-table td { padding: .75rem 1rem; border-bottom: 1px solid #f1f5f9; color: #334155; }
    .sp-table tr:hover td { background: #f8fafc; }

    /* Modal */
    .sp-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(10, 25, 47, .65);
        backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .sp-modal-card {
        background: #fff;
        border-radius: 1rem;
        max-width: 520px;
        width: 100%;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,.2);
    }
</style>

<div class="sp-wrap">

    {{-- ALERT SUKSES / ERROR --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl mb-5 flex items-center gap-3">
            <i class="bi bi-check-circle-fill text-emerald-500 text-lg"></i>
            <span class="font-medium text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- ================= HERO BANNER & PROFIL ================= --}}
    <div class="sp-card sp-hero">
        <div class="sp-banner"></div>

        <div class="sp-hero-body">
            <div class="sp-avatar-box">
                {{-- Foto Profil --}}
                <div class="sp-avatar">
                    @php
                        $fotoSrc = $biography->foto_url;
                    @endphp
                    @if($fotoSrc)
                        <img src="{{ $fotoSrc }}" alt="Foto Atlet" onerror="this.onerror=null;this.parentElement.innerHTML='<div class=\'sp-avatar-ph\'><i class=\'bi bi-person-fill\'></i></div>';">
                    @else
                        <div class="sp-avatar-ph"><i class="bi bi-person-fill"></i></div>
                    @endif
                </div>

                {{-- Info Utama Atlet --}}
                <div class="sp-hero-info">
                    <h1 class="sp-name">{{ $biography->athlete->nama ?? $biography->nama_atlet ?? 'Nama Atlet' }}</h1>

                    <div class="sp-chips">
                        {{-- 1. Nomor Induk Mengikuti Data Atlet --}}
                        <span class="sp-chip">
                            <i class="bi bi-person-vcard text-slate-400"></i>
                            NIS: {{ $biography->athlete->nomor_induk ?? $biography->athlete->kode_barcode ?? $biography->nis ?? '-' }}
                        </span>

                        {{-- 2. Kelompok Umur --}}
                        <span class="sp-chip sp-chip-blue">
                            <i class="bi bi-calendar3"></i>
                            Kelompok Umur: {{ $biography->athlete->kelompok_umur ?? $biography->kategori_kelas ?? '-' }}
                        </span>

                        {{-- 3. Kelompok Latihan --}}
                        <span class="sp-chip sp-chip-green">
                            <i class="bi bi-tag-fill"></i>
                            Kelompok Latihan: {{ $biography->athlete->kelompok_latihan ?? '-' }}
                        </span>

                        {{-- 4. Status Keaktifan --}}
                        <span class="sp-chip {{ $aktif ? 'sp-chip-ok' : 'sp-chip-no' }}">
                            <span class="sp-chip-dot"></span>
                            {{ $aktif ? 'Aktif' : 'Non-Aktif' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="sp-actions">
                <a href="{{ route('admin.biography.index') }}" class="sp-btn sp-btn-light">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('admin.biography.edit', $biography->id) }}" class="sp-btn sp-btn-blue">
                    <i class="bi bi-pencil-square"></i> Edit Profil
                </a>
                <a href="{{ route('admin.biography.print', $biography->id) }}" target="_blank" class="sp-btn sp-btn-red">
                    <i class="bi bi-file-earmark-pdf"></i> Cetak & Unduh PDF
                </a>
            </div>
        </div>
    </div>

    {{-- ================= 4 KARTU STATISTIK ================= --}}
    <div class="sp-stats">
        @foreach($ringkasan as $item)
            <div class="sp-card sp-stat-box">
                <div class="sp-stat-top">
                    <span class="sp-stat-label">{{ $item['label'] }}</span>
                    <span class="sp-stat-icon" style="background: {{ $item['bg'] }}; color: {{ $item['fg'] }};">
                        <i class="bi {{ $item['icon'] }}"></i>
                    </span>
                </div>
                <div class="sp-stat-val">{{ $item['val'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- ================= GRID RINCIAN ================= --}}
    <div class="sp-grid">

        {{-- KOLOM KIRI: DATA AKADEMI & FISIK --}}
        <div class="space-y-5">
            {{-- Profil Pemain (Fisik) --}}
            <div class="sp-card">
                <div class="sp-sec-head">
                    <h3 class="sp-sec-title"><i class="bi bi-person-lines-fill text-blue-600"></i> Profil Pemain</h3>
                </div>
                <div>
                    <div class="sp-row">
                        <span class="sp-row-lbl">Posisi Bermain</span>
                        <span class="sp-row-val">{{ $biography->posisi ?? $biography->athlete->posisi_bermain ?? '-' }}</span>
                    </div>
                    <div class="sp-row">
                        <span class="sp-row-lbl">No. Punggung</span>
                        <span class="sp-row-val">{{ $biography->nomor_punggung ?? $biography->athlete->nomor_punggung ?? '-' }}</span>
                    </div>
                    <div class="sp-row">
                        <span class="sp-row-lbl">Kaki Dominan</span>
                        <span class="sp-row-val">{{ $biography->kaki_dominan ?? '-' }}</span>
                    </div>
                    <div class="sp-row">
                        <span class="sp-row-lbl">Tinggi Badan</span>
                        <span class="sp-row-val">{{ $biography->tinggi_cm ? $biography->tinggi_cm . ' cm' : '-' }}</span>
                    </div>
                    <div class="sp-row">
                        <span class="sp-row-lbl">Berat Badan</span>
                        <span class="sp-row-val">{{ $biography->berat_kg ? $biography->berat_kg . ' kg' : '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Data Akademi --}}
            <div class="sp-card">
                <div class="sp-sec-head">
                    <h3 class="sp-sec-title"><i class="bi bi-mortarboard-fill text-emerald-600"></i> Data Akademisi</h3>
                </div>
                <div>
                    <div class="sp-row">
                        <span class="sp-row-lbl">Nomor Induk Atlet</span>
                        <span class="sp-row-val">{{ $biography->athlete->nomor_induk ?? $biography->athlete->kode_barcode ?? '-' }}</span>
                    </div>
                    <div class="sp-row">
                        <span class="sp-row-lbl">Kelompok Umur</span>
                        <span class="sp-row-val">{{ $biography->athlete->kelompok_umur ?? $biography->kategori_kelas ?? '-' }}</span>
                    </div>
                    <div class="sp-row">
                        <span class="sp-row-lbl">Kelompok Latihan</span>
                        <span class="sp-row-val font-bold text-emerald-600">{{ $biography->athlete->kelompok_latihan ?? '-' }}</span>
                    </div>
                    <div class="sp-row">
                        <span class="sp-row-lbl">Tanggal Bergabung</span>
                        <span class="sp-row-val">{{ $bergabung ? $bergabung->translatedFormat('d F Y') : '-' }}</span>
                    </div>
                    <div class="sp-row">
                        <span class="sp-row-lbl">Masa Latihan</span>
                        <span class="sp-row-val">
                            @if($lama)
                                {{ $lama->y > 0 ? $lama->y . ' th ' : '' }}{{ $lama->m }} bln
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="sp-row">
                        <span class="sp-row-lbl">Asal Sekolah</span>
                        <span class="sp-row-val">{{ $biography->asal_sekolah ?? '-' }}</span>
                    </div>
                    <div class="sp-row">
                        <span class="sp-row-lbl">Status</span>
                        <span class="sp-row-val">
                            <span class="sp-chip {{ $aktif ? 'sp-chip-ok' : 'sp-chip-no' }}" style="padding: 2px 8px; font-size: 11px;">
                                {{ $aktif ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: STATISTIK & PRESTASI --}}
        <div class="space-y-5">
            {{-- Tabel Statistik Pertandingan --}}
            <div class="sp-card">
                <div class="sp-sec-head">
                    <h3 class="sp-sec-title"><i class="bi bi-bar-chart-line-fill text-blue-600"></i> Statistik Pertandingan</h3>
                    <button type="button" onclick="openModal('modal-statistik')" class="sp-btn sp-btn-blue text-xs py-1.5 px-3">
                        <i class="bi bi-plus-lg"></i> Tambah Data
                    </button>
                </div>
                <div class="overflow-x-auto">
                    @if($stats->count() > 0)
                        <table class="sp-table">
                            <thead>
                                <tr>
                                    <th>Pertandingan / Lawan</th>
                                    <th class="text-center">Menit</th>
                                    <th class="text-center">Gol</th>
                                    <th class="text-center">Assist</th>
                                    <th class="text-center">Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats as $stat)
                                    <tr>
                                        <td>
                                            <div class="font-bold text-slate-800">{{ $stat->nama_pertandingan }}</div>
                                            <div class="text-xs text-slate-500">vs {{ $stat->nama_lawan }} &bull; {{ \Carbon\Carbon::parse($stat->tanggal)->translatedFormat('d M Y') }}</div>
                                        </td>
                                        <td class="text-center font-semibold">{{ $stat->menit_bermain }}'</td>
                                        <td class="text-center font-bold text-blue-600">{{ $stat->gol }}</td>
                                        <td class="text-center font-bold text-emerald-600">{{ $stat->assist }}</td>
                                        <td class="text-center">
                                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-700">
                                                {{ $stat->nilai_evaluasi ?? '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-8 text-center text-slate-400 text-sm">
                            <i class="bi bi-bar-chart text-3xl block mb-2 text-slate-300"></i>
                            Belum ada riwayat statistik pertandingan.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Riwayat Prestasi --}}
            <div class="sp-card">
                <div class="sp-sec-head">
                    <h3 class="sp-sec-title"><i class="bi bi-trophy-fill text-amber-500"></i> Riwayat Prestasi Atlet</h3>
                    <button type="button" onclick="openModal('modal-prestasi')" class="sp-btn sp-btn-light text-xs py-1.5 px-3">
                        <i class="bi bi-plus-lg"></i> Tambah Prestasi
                    </button>
                </div>
                <div class="p-5">
                    @if($achievements->count() > 0)
                        <div class="space-y-3">
                            @foreach($achievements as $ach)
                                <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                                            <i class="bi bi-award-fill text-lg"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-800 text-sm mb-0.5">{{ $ach->nama_turnamen }}</h4>
                                            <div class="text-xs text-slate-600">
                                                <span class="font-bold text-amber-600">{{ $ach->pencapaian }}</span> &bull; 
                                                Tingkat {{ $ach->tingkat }}
                                                @if(!empty($ach->kategori) && $ach->kategori !== '-') &bull; Kategori {{ $ach->kategori }} @endif &bull; 
                                                {{ \Carbon\Carbon::parse($ach->tanggal)->translatedFormat('d M Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-6 text-center text-slate-400 text-sm">
                            <i class="bi bi-award text-3xl block mb-2 text-slate-300"></i>
                            Belum ada riwayat prestasi atlet.
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>

{{-- ================= MODAL STATISTIK ================= --}}
<div id="modal-statistik" class="sp-modal">
    <div class="sp-modal-card">
        <form action="{{ route('admin.biography.statistic.store', $biography->id) }}" method="POST">
            @csrf
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 text-base"><i class="bi bi-plus-circle text-blue-600 mr-1.5"></i> Input Statistik Baru</h3>
                <button type="button" onclick="closeModal('modal-statistik')" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="p-5 grid grid-cols-2 gap-4 text-sm">
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nama Pertandingan *</label>
                    <input type="text" name="nama_pertandingan" required placeholder="Cth: Liga AAFI U-13 Matchday 1" class="w-full px-3 py-2 border rounded-lg border-slate-300">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nama Tim Lawan *</label>
                    <input type="text" name="nama_lawan" required placeholder="Cth: Akademi Futsal Garuda" class="w-full px-3 py-2 border rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Tanggal Tanding *</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Menit Bermain *</label>
                    <input type="number" name="menit_bermain" value="40" required class="w-full px-3 py-2 border rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Gol *</label>
                    <input type="number" name="gol" value="0" min="0" required class="w-full px-3 py-2 border rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Assist *</label>
                    <input type="number" name="assist" value="0" min="0" required class="w-full px-3 py-2 border rounded-lg border-slate-300">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nilai Evaluasi (1-100) - Opsional</label>
                    <input type="number" name="nilai_evaluasi" min="1" max="100" placeholder="Cth: 85" class="w-full px-3 py-2 border rounded-lg border-slate-300">
                </div>
            </div>
            <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-statistik')" class="sp-btn sp-btn-light">Batal</button>
                <button type="submit" class="sp-btn sp-btn-blue">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL PRESTASI ================= --}}
<div id="modal-prestasi" class="sp-modal">
    <div class="sp-modal-card">
        <form action="{{ route('admin.biography.achievement.store', $biography->id) }}" method="POST">
            @csrf
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 text-base"><i class="bi bi-award text-amber-500 mr-1.5"></i> Input Prestasi Baru</h3>
                <button type="button" onclick="closeModal('modal-prestasi')" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="p-5 space-y-4 text-sm">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nama Turnamen *</label>
                    <input type="text" name="nama_turnamen" required placeholder="Cth: Kejuaraan Futsal Daerah Jawa Timur" class="w-full px-3 py-2 border rounded-lg border-slate-300">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Pencapaian / Juara *</label>
                        <input type="text" name="pencapaian" required placeholder="Cth: Juara 1 / Best Player" class="w-full px-3 py-2 border rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Tingkat *</label>
                        <select name="tingkat" required class="w-full px-3 py-2 border rounded-lg border-slate-300 bg-white">
                            <option value="Kota/Kabupaten">Kota/Kabupaten</option>
                            <option value="Provinsi">Provinsi</option>
                            <option value="Nasional">Nasional</option>
                            <option value="Internasional">Internasional</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Tanggal Turnamen *</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Kategori Turnamen</label>
                        <input type="text" name="kategori" placeholder="Cth: U-12, U-15, Umum" class="w-full px-3 py-2 border rounded-lg border-slate-300">
                    </div>
                </div>
            </div>
            <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-prestasi')" class="sp-btn sp-btn-light">Batal</button>
                <button type="submit" class="sp-btn sp-btn-blue">Simpan Prestasi</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        const m = document.getElementById(id);
        if (m) m.style.display = 'flex';
    }
    function closeModal(id) {
        const m = document.getElementById(id);
        if (m) m.style.display = 'none';
    }
    window.onclick = function(e) {
        if (e.target.classList.contains('sp-modal')) {
            e.target.style.display = 'none';
        }
    };
</script>
@endsection