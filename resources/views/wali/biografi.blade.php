@extends('layouts.wali')

@section('content')
@php
    \Carbon\Carbon::setLocale('id');

    if ($biography) {
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
    }
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

    .sp-wrap { width: 100%; margin: 0 auto; }
    .sp-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
    
    /* Hero Banner & Profil */
    .sp-hero {
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
        border-radius: 1.25rem;
        background: #fff;
    }
    .sp-banner {
        height: 120px;
        background: linear-gradient(135deg, #0A192F 0%, #064E3B 60%, #047857 100%);
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
    .sp-avatar-box {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }
    @media (min-width: 640px) {
        .sp-avatar-box {
            flex-direction: row;
            align-items: flex-end;
        }
    }
    .sp-avatar {
        width: 110px;
        height: 110px;
        margin-top: -55px;
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
        color: #0f172a;
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

    /* Tombol Aksi */
    .sp-actions {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        flex-wrap: nowrap;
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
</style>

<div class="sp-wrap">

    {{-- Pilihan Ganti Anak (Jika Punya Lebih dari 1 Anak di Akademi) --}}
    @if($myAthletes->count() > 1)
    <div class="mb-4 bg-brand-light border border-blue-200 p-4 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-3 shadow-sm">
        <div class="d-flex align-items-center">
            <i class="bi bi-people-fill fs-4 text-brand-blue me-3"></i>
            <span class="small fw-semibold text-brand-navy">Anda memiliki {{ $myAthletes->count() }} anak terdaftar. Pilih profil anak:</span>
        </div>
        <form action="{{ route('wali.biografi') }}" method="GET" class="d-flex gap-2">
            <select name="child_id" class="form-select form-select-sm fw-medium border-primary-subtle text-brand-navy" onchange="this.form.submit()" style="min-width: 200px;">
                @foreach($myAthletes as $child)
                    <option value="{{ $child->id }}" {{ ($athlete && $athlete->id == $child->id) ? 'selected' : '' }}>
                        {{ $child->nama }} ({{ $child->kelompok_umur ? 'KU-' . $child->kelompok_umur : 'KU -' }})
                    </option>
                @endforeach
            </select>
        </form>
    </div>
    @endif

    @if(!$athlete)
        <div class="alert bg-white text-center p-5 card-custom border-top border-4" style="border-top-color: #f59e0b !important;">
            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                <i class="bi bi-exclamation-triangle-fill fs-1 text-warning"></i>
            </div>
            <h5 class="fw-bold text-dark">Belum Ada Data Anak Terhubung</h5>
            <p class="text-secondary mb-0">Akun Anda belum dikaitkan dengan data atlet manapun oleh Admin. Silakan hubungi sekretariat admin Super Seed Academy.</p>
        </div>
    @elseif(!$biography)
        {{-- Kondisi jika atlet belum memiliki biografi yang diinput oleh Admin --}}
        <div class="card card-custom p-5 text-center bg-white border-0 shadow-sm">
            <div class="rounded-circle bg-light d-inline-flex p-4 mx-auto mb-3 text-secondary">
                <i class="bi bi-person-lines-fill fs-1 opacity-50"></i>
            </div>
            <h5 class="fw-bold text-brand-navy mb-2">Biografi Belum Tersedia</h5>
            <p class="text-secondary small mb-4" style="max-width: 480px; margin: 0 auto;">
                Data biografi resmi dan rekam jejak atlet untuk ananda <strong>{{ $athlete->nama }}</strong> sedang dalam proses penyusunan oleh tim pelatih & manajemen akademi.
            </p>
            <div>
                <a href="{{ route('wali.dashboard', ['child_id' => $athlete->id]) }}" class="btn btn-sm btn-outline-secondary px-4 fw-semibold rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Biodata
                </a>
            </div>
        </div>
    @else
        {{-- ================= HERO BANNER & PROFIL ATLET ================= --}}
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
                                Kelompok Umur: {{ $biography->athlete->kelompok_umur ? 'KU-' . $biography->athlete->kelompok_umur : ($biography->kategori_kelas ?? '-') }}
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
                    <a href="{{ route('wali.biografi.print', $biography->id) }}" target="_blank" class="sp-btn sp-btn-red">
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
            <div class="d-flex flex-column gap-4">
                {{-- Profil Pemain (Fisik) --}}
                <div class="sp-card">
                    <div class="sp-sec-head">
                        <h3 class="sp-sec-title"><i class="bi bi-person-lines-fill text-primary"></i> Profil Pemain</h3>
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
                        <h3 class="sp-sec-title"><i class="bi bi-mortarboard-fill text-success"></i> Data Akademisi</h3>
                    </div>
                    <div>
                        <div class="sp-row">
                            <span class="sp-row-lbl">Nomor Induk Atlet</span>
                            <span class="sp-row-val">{{ $biography->athlete->nomor_induk ?? $biography->athlete->kode_barcode ?? '-' }}</span>
                        </div>
                        <div class="sp-row">
                            <span class="sp-row-lbl">Kelompok Umur</span>
                            <span class="sp-row-val">{{ $biography->athlete->kelompok_umur ? 'KU-' . $biography->athlete->kelompok_umur : ($biography->kategori_kelas ?? '-') }}</span>
                        </div>
                        <div class="sp-row">
                            <span class="sp-row-lbl">Kelompok Latihan</span>
                            <span class="sp-row-val font-bold text-success">{{ $biography->athlete->kelompok_latihan ?? '-' }}</span>
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
            <div class="d-flex flex-column gap-4">
                {{-- Tabel Statistik Pertandingan --}}
                <div class="sp-card">
                    <div class="sp-sec-head">
                        <h3 class="sp-sec-title"><i class="bi bi-bar-chart-line-fill text-primary"></i> Statistik Pertandingan</h3>
                        <span class="badge bg-light text-secondary border small">Total: {{ $stats->count() }} Laga</span>
                    </div>
                    <div class="table-responsive">
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
                                                <div class="fw-bold text-dark">{{ $stat->nama_pertandingan }}</div>
                                                <div class="small text-muted">vs {{ $stat->nama_lawan }} &bull; {{ \Carbon\Carbon::parse($stat->tanggal)->translatedFormat('d M Y') }}</div>
                                            </td>
                                            <td class="text-center fw-semibold">{{ $stat->menit_bermain }}'</td>
                                            <td class="text-center fw-bold text-primary">{{ $stat->gol }}</td>
                                            <td class="text-center fw-bold text-success">{{ $stat->assist }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border">
                                                    {{ $stat->nilai_evaluasi ?? '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="p-5 text-center text-secondary small">
                                <i class="bi bi-bar-chart fs-1 d-block mb-2 text-muted opacity-50"></i>
                                Belum ada riwayat statistik pertandingan.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Riwayat Prestasi --}}
                <div class="sp-card">
                    <div class="sp-sec-head">
                        <h3 class="sp-sec-title"><i class="bi bi-trophy-fill text-warning"></i> Riwayat Prestasi Atlet</h3>
                        <span class="badge bg-light text-secondary border small">Total: {{ $achievements->count() }} Prestasi</span>
                    </div>
                    <div class="p-4">
                        @if($achievements->count() > 0)
                            <div class="d-flex flex-column gap-3">
                                @foreach($achievements as $ach)
                                    <div class="p-3 rounded-3 border bg-light d-flex align-items-start gap-3">
                                        <div class="rounded-3 bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; font-size: 1.25rem;">
                                            <i class="bi bi-award-fill"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1">{{ $ach->nama_turnamen }}</h6>
                                            <div class="small text-muted">
                                                <span class="fw-bold text-warning">{{ $ach->pencapaian }}</span> &bull; 
                                                Tingkat {{ $ach->tingkat }}
                                                @if(!empty($ach->kategori) && $ach->kategori !== '-') &bull; Kategori {{ $ach->kategori }} @endif &bull; 
                                                {{ \Carbon\Carbon::parse($ach->tanggal)->translatedFormat('d M Y') }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="py-5 text-center text-secondary small">
                                <i class="bi bi-award fs-1 d-block mb-2 text-muted opacity-50"></i>
                                Belum ada riwayat prestasi atlet.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    @endif

</div>
@endsection