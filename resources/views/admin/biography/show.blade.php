@extends('layouts.admin')

@section('title', 'Detail Profil Atlet - Super Seed')

@section('content')
@php
    // ===== Data dasar =====
    $stats        = $biography->statistics ?? collect();
    $prestasiList = $biography->achievements ?? collect();
    $aktif        = (bool) $biography->status_aktif;

    // ===== Tanggal bergabung & durasi =====
    $tglGabung = $biography->tanggal_bergabung
        ? \Carbon\Carbon::parse($biography->tanggal_bergabung)
        : null;

    $durasiText = '-';
    if ($tglGabung) {
        if ($tglGabung->isFuture()) {
            $durasiText = 'Belum bergabung';
        } else {
            $selisih = $tglGabung->diff(now());
            $bagian  = [];
            if ($selisih->y > 0) $bagian[] = $selisih->y . ' tahun';
            if ($selisih->m > 0) $bagian[] = $selisih->m . ' bulan';
            if (empty($bagian))  $bagian[] = $selisih->d . ' hari';
            $durasiText = implode(' ', $bagian);
        }
    }

    // ===== Ringkasan statistik =====
    $ringkasan = [
        ['label' => 'Pertandingan', 'value' => $stats->count(),            'icon' => 'bi-flag-fill',           'bg' => '#f1f5f9', 'fg' => '#475569', 'vc' => '#1e293b'],
        ['label' => 'Gol',          'value' => $stats->sum('gol'),         'icon' => 'bi-bullseye',            'bg' => '#dbeafe', 'fg' => '#2563eb', 'vc' => '#2563eb'],
        ['label' => 'Assist',       'value' => $stats->sum('assist'),      'icon' => 'bi-hand-thumbs-up-fill', 'bg' => '#d1fae5', 'fg' => '#059669', 'vc' => '#1e293b'],
        ['label' => 'Menit Main',   'value' => $stats->sum('menit_bermain'),'icon' => 'bi-stopwatch-fill',     'bg' => '#fef3c7', 'fg' => '#d97706', 'vc' => '#1e293b'],
    ];
@endphp

<style>
    /* ================= VARIABEL ================= */
    .sp-wrap {
        --sp-blue: #1565ff;
        --sp-blue-dark: #0f4fd1;
        --sp-navy: #0f1d3a;
        --sp-slate-50: #f8fafc;
        --sp-slate-100: #f1f5f9;
        --sp-slate-200: #e2e8f0;
        --sp-slate-300: #cbd5e1;
        --sp-slate-500: #64748b;
        --sp-slate-700: #334155;
        --sp-slate-800: #1e293b;
        --sp-amber: #d97706;

        max-width: 1280px;
        margin: 0 auto;
        padding: 1.5rem 1rem;
        color: var(--sp-slate-800);
        font-size: 14px;
    }
    .sp-wrap *, .sp-wrap *::before, .sp-wrap *::after { box-sizing: border-box; }

    /* ================= KARTU DASAR ================= */
    .sp-card {
        background: #fff;
        border: 1px solid var(--sp-slate-200);
        border-radius: 1rem;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }
    .sp-section { padding: 1.5rem; }
    .sp-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding-bottom: .875rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid var(--sp-slate-100);
    }
    .sp-title {
        display: flex;
        align-items: center;
        gap: .5rem;
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: var(--sp-navy);
    }
    .sp-title i { color: var(--sp-blue); }

    /* ================= TOMBOL ================= */
    .sp-btn {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .6rem 1rem;
        border-radius: .6rem;
        font-size: .875rem;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        transition: background .15s ease, color .15s ease;
        white-space: nowrap;
    }
    .sp-btn-light { background: #fff; color: var(--sp-slate-700); border-color: #cbd5e1; }
    .sp-btn-light:hover { background: var(--sp-slate-50); }
    .sp-btn-blue { background: var(--sp-blue); color: #fff; }
    .sp-btn-blue:hover { background: var(--sp-blue-dark); }
    .sp-btn-red { background: #f43f5e; color: #fff; }
    .sp-btn-red:hover { background: #e11d48; }
    .sp-btn-soft {
        background: rgba(21, 101, 255, .1);
        color: var(--sp-blue);
        padding: .4rem .75rem;
        font-size: .8rem;
    }
    .sp-btn-soft:hover { background: var(--sp-blue); color: #fff; }

    /* ================= HERO / HEADER PROFIL ================= */
    .sp-hero { position: relative; overflow: hidden; margin-bottom: 1.5rem; }
    .sp-banner {
        height: 7rem;
        background: linear-gradient(90deg, var(--sp-navy), #1e293b, var(--sp-navy));
        position: relative;
    }
    .sp-banner::after {
        content: "";
        position: absolute;
        inset: 0;
        opacity: .12;
        background-image: radial-gradient(#fff 1px, transparent 1px);
        background-size: 18px 18px;
    }
    .sp-hero-body {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 1.25rem;
        padding: 0 1.5rem 1.5rem;
        margin-top: -3.5rem;
    }
    .sp-avatar {
        position: relative;
        width: 8.5rem;
        height: 8.5rem;
        flex-shrink: 0;
        border-radius: 1rem;
        background: var(--sp-slate-100);
        border: 4px solid #fff;
        box-shadow: 0 6px 18px rgba(15, 23, 42, .12);
        overflow: hidden;
    }
    .sp-avatar-ph {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--sp-slate-300);
        font-size: 4rem;
    }
    .sp-avatar img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .sp-stat {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 1rem;
    width: 100%;
    max-width: 11rem;          /* lebar kartu, makin kecil makin ringkas */
    margin: 0 auto;            /* kartu berada di tengah kolomnya */
    aspect-ratio: 1 / 1;
    transition: box-shadow .2s ease, transform .2s ease;
    }
    .sp-hero-info { flex: 1 1 260px; min-width: 0; }
    .sp-eyebrow {
        margin: 0;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: var(--sp-blue);
    }
    .sp-name {
        margin: .25rem 0 0;
        font-size: 1.75rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: -.01em;
        color: var(--sp-slate-800);
        overflow-wrap: anywhere;
    }
    .sp-chips { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: .75rem; }
    .sp-chip {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .25rem .6rem;
        border-radius: .4rem;
        font-size: .8rem;
        font-weight: 600;
        background: var(--sp-slate-100);
        color: var(--sp-slate-700);
    }
    .sp-chip-blue { background: rgba(21, 101, 255, .1); color: var(--sp-blue); font-weight: 700; }
    .sp-chip-ok { background: #ecfdf5; color: #047857; box-shadow: inset 0 0 0 1px #a7f3d0; }
    .sp-chip-no { background: #fff1f2; color: #be123c; box-shadow: inset 0 0 0 1px #fecdd3; }
    .sp-chip-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    .sp-actions { display: flex; flex-wrap: wrap; gap: .5rem; }

    /* ================= STATISTIK RINGKAS (4 KARTU PERSEGI) ================= */
    .sp-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .sp-stat {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 1.25rem;
        aspect-ratio: 1 / 1;
        transition: box-shadow .2s ease, transform .2s ease;
    }
    .sp-stat:hover {
        box-shadow: 0 8px 22px rgba(15, 23, 42, .08);
        transform: translateY(-2px);
    }
    .sp-stat-top { display: flex; align-items: flex-start; justify-content: space-between; gap: .5rem; }
    .sp-stat-label {
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--sp-slate-500);
    }
    .sp-stat-icon {
    width: 2rem;
    height: 2rem;
    border-radius: .55rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .95rem;
    flex-shrink: 0;
}

.sp-stat-value {
    font-size: 2rem;
    font-weight: 900;
    line-height: 1;
    font-variant-numeric: tabular-nums;
}

    /* ================= LAYOUT UTAMA ================= */
    .sp-main {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 2fr);
        gap: 1.5rem;
        align-items: start;
    }
    .sp-col { display: flex; flex-direction: column; gap: 1.5rem; min-width: 0; }

    /* Daftar label-nilai */
    .sp-dl { margin: 0; }
    .sp-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: .75rem 0;
        border-bottom: 1px solid var(--sp-slate-100);
    }
    .sp-row:last-child { border-bottom: none; }
    .sp-row dt { color: var(--sp-slate-500); }
    .sp-row dd { margin: 0; font-weight: 600; color: var(--sp-slate-800); text-align: right; }
    .sp-jersey {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.25rem;
        height: 2.25rem;
        padding: 0 .5rem;
        border-radius: .5rem;
        background: var(--sp-navy);
        color: #fff;
        font-weight: 900;
    }
    .sp-badge-amber {
        display: inline-block;
        padding: .25rem .6rem;
        border-radius: .4rem;
        background: #fffbeb;
        color: #b45309;
        font-weight: 700;
    }

    /* Tabel */
    .sp-table-wrap { overflow-x: auto; border: 1px solid var(--sp-slate-200); border-radius: .75rem; }
    .sp-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
    .sp-table thead th {
        background: var(--sp-slate-50);
        color: var(--sp-slate-500);
        font-size: .7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        padding: .75rem 1rem;
        text-align: center;
        white-space: nowrap;
    }
    .sp-table thead th:first-child { text-align: left; }
    .sp-table tbody td {
        padding: .75rem 1rem;
        text-align: center;
        border-top: 1px solid var(--sp-slate-100);
        vertical-align: middle;
    }
    .sp-table tbody tr:hover { background: #fafcff; }
    .sp-table .sp-match { text-align: left; }
    .sp-match-name { margin: 0; font-weight: 700; color: var(--sp-slate-800); }
    .sp-match-sub { margin: .15rem 0 0; font-size: .75rem; color: var(--sp-slate-500); }
    .sp-goal { font-weight: 800; color: var(--sp-blue); }
    .sp-score {
        display: inline-block;
        min-width: 2.25rem;
        padding: .25rem .5rem;
        border-radius: .4rem;
        background: #fef3c7;
        color: #92400e;
        font-weight: 800;
    }

    /* Prestasi */
    .sp-achv-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }
    .sp-achv {
        display: flex;
        align-items: flex-start;
        gap: .9rem;
        padding: 1rem;
        border: 1px solid var(--sp-slate-200);
        border-radius: .75rem;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .sp-achv:hover { border-color: #fcd34d; box-shadow: 0 4px 12px rgba(15, 23, 42, .06); }
    .sp-achv-icon {
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 50%;
        background: #fef3c7;
        color: #d97706;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .sp-achv-title { margin: 0; font-weight: 700; color: var(--sp-slate-800); overflow-wrap: anywhere; }
    .sp-achv-sub { margin: .25rem 0 0; font-size: .85rem; font-weight: 600; color: var(--sp-blue); }
    .sp-achv-sub span { color: var(--sp-slate-500); font-weight: 500; }
    .sp-achv-meta { display: flex; flex-wrap: wrap; align-items: center; gap: .5rem; margin-top: .5rem; font-size: .75rem; color: var(--sp-slate-500); }
    .sp-tag {
        padding: .15rem .45rem;
        border-radius: .3rem;
        background: #d1fae5;
        color: #047857;
        font-size: .65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    /* Empty state */
    .sp-empty {
        text-align: center;
        padding: 2.5rem 1rem;
        border: 1px dashed var(--sp-slate-300);
        border-radius: .75rem;
        background: var(--sp-slate-50);
    }
    .sp-empty-icon {
        width: 3rem;
        height: 3rem;
        margin: 0 auto .75rem;
        border-radius: 50%;
        background: #fff;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .06);
    }
    .sp-empty h4 { margin: 0 0 .25rem; font-size: .95rem; font-weight: 700; color: var(--sp-slate-700); }
    .sp-empty p { margin: 0; font-size: .85rem; color: var(--sp-slate-500); }

    /* ================= MODAL ================= */
    .sp-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        background: rgba(15, 23, 42, .5);
        backdrop-filter: blur(3px);
        overflow-y: auto;
    }
    .sp-modal.is-open { display: flex; }
    .sp-modal-box {
        width: 100%;
        max-width: 640px;
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 20px 50px rgba(15, 23, 42, .25);
        overflow: hidden;
    }
    .sp-modal-body { padding: 1.25rem 1.5rem; }
    .sp-modal-foot {
        display: flex;
        justify-content: flex-end;
        gap: .6rem;
        padding: 1rem 1.5rem;
        background: var(--sp-slate-50);
        border-top: 1px solid var(--sp-slate-200);
    }
    .sp-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }
    .sp-form-full { grid-column: 1 / -1; }
    .sp-label {
        display: block;
        margin-bottom: .35rem;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: var(--sp-slate-500);
    }
    .sp-label small { text-transform: none; letter-spacing: 0; font-weight: 400; color: #94a3b8; }
    .sp-input {
        width: 100%;
        padding: .55rem .75rem;
        border: 1px solid #cbd5e1;
        border-radius: .5rem;
        font-size: .875rem;
        color: var(--sp-slate-800);
        background: #fff;
        outline: none;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .sp-input:focus { border-color: var(--sp-blue); box-shadow: 0 0 0 3px rgba(21, 101, 255, .15); }

    .sp-modal-title {
        display: flex;
        align-items: center;
        gap: .5rem;
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--sp-navy);
    }
    .sp-modal-title i { color: var(--sp-blue); }
    .sp-close {
        background: none;
        border: none;
        color: #94a3b8;
        font-size: 1rem;
        cursor: pointer;
        padding: .25rem;
    }
    .sp-close:hover { color: #f43f5e; }

    /* ================= RESPONSIVE ================= */
    @media (max-width: 1024px) {
        .sp-main { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .sp-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .sp-achv-grid { grid-template-columns: 1fr; }
        .sp-name { font-size: 1.4rem; }
        .sp-hero-body { justify-content: center; text-align: center; }
        .sp-hero-info { text-align: center; }
        .sp-chips { justify-content: center; }
        .sp-actions { width: 100%; justify-content: center; }
    }
    @media (max-width: 480px) {
    .sp-stat { aspect-ratio: 1 / 1; max-width: 9rem; }
    .sp-stat-value { font-size: 1.6rem; }
    .sp-form-grid { grid-template-columns: 1fr; }
    }

    /* ================= PRINT ================= */
    @media print {
        .no-print, aside, nav, header, .sp-actions, .sp-btn-soft, .sp-modal { display: none !important; }
        .sp-wrap { padding: 0; max-width: none; }
        .sp-card, .sp-stat { box-shadow: none !important; break-inside: avoid; }
        .sp-stat:hover { transform: none !important; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        @page { size: A4 portrait; margin: 10mm; }
    }
</style>

<div class="sp-wrap">

    {{-- ================= HEADER PROFIL ================= --}}
    <div class="sp-card sp-hero">
        <div class="sp-banner"></div>

        <div class="sp-hero-body">
            {{-- Foto --}}
            <div class="sp-avatar">
                <div class="sp-avatar-ph"><i class="bi bi-person-fill"></i></div>
                @if ($biography->foto_profil)
                    <img src="{{ asset('storage/' . $biography->foto_profil) }}"
                         alt="Foto {{ $biography->nama_atlet }}"
                         onerror="this.remove()">
                @endif
                <span class="sp-status-dot" style="background: {{ $aktif ? '#10b981' : '#f43f5e' }};"></span>
            </div>

            {{-- Info --}}
            <div class="sp-hero-info">
                <p class="sp-eyebrow">Profil Atlet</p>
                <h1 class="sp-name">{{ $biography->nama_atlet ?? 'Nama Atlet' }}</h1>

                <div class="sp-chips">
                    <span class="sp-chip">NIS: {{ $biography->nis ?? '-' }}</span>
                    <span class="sp-chip sp-chip-blue">Kelas {{ $biography->kategori_kelas ?? '-' }}</span>
                    <span class="sp-chip {{ $aktif ? 'sp-chip-ok' : 'sp-chip-no' }}">
                        <span class="sp-chip-dot"></span>
                        {{ $aktif ? 'Aktif' : 'Non-Aktif' }}
                    </span>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="sp-actions no-print">
                <a href="{{ route('admin.biography.index') }}" class="sp-btn sp-btn-light">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('admin.biography.edit', $biography->id) }}" class="sp-btn sp-btn-blue">
                    <i class="bi bi-pencil-square"></i> Edit Profil
                </a>
                <a href="{{ route('admin.biography.print', $biography->id) }}" target="_blank" class="sp-btn sp-btn-red">
                    <i class="bi bi-file-earmark-pdf"></i> Cetak PDF
                </a>
            </div>
        </div>
    </div>

    {{-- ================= RINGKASAN STATISTIK (4 KARTU PERSEGI) ================= --}}
    <div class="sp-stats">
        @foreach ($ringkasan as $item)
            <div class="sp-card sp-stat">
                <div class="sp-stat-top">
                    <span class="sp-stat-label">{{ $item['label'] }}</span>
                    <span class="sp-stat-icon" style="background: {{ $item['bg'] }}; color: {{ $item['fg'] }};">
                        <i class="bi {{ $item['icon'] }}"></i>
                    </span>
                </div>
                <div class="sp-stat-value" style="color: {{ $item['vc'] }};">{{ $item['value'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- ================= KONTEN UTAMA ================= --}}
    <div class="sp-main">

        {{-- Kolom kiri --}}
        <div class="sp-col">

            <div class="sp-card sp-section">
                <div class="sp-section-head">
                    <h3 class="sp-title"><i class="bi bi-person-vcard"></i> Profil Pemain</h3>
                </div>
                <dl class="sp-dl">
                    <div class="sp-row">
                        <dt>Posisi Bermain</dt>
                        <dd>{{ $biography->posisi_bermain ?? '-' }}</dd>
                    </div>
                    <div class="sp-row">
                        <dt>Kaki Dominan</dt>
                        <dd>{{ $biography->kaki_dominan ?? '-' }}</dd>
                    </div>
                    <div class="sp-row">
                        <dt>No. Punggung</dt>
                        <dd><span class="sp-jersey">{{ $biography->no_punggung ?? '-' }}</span></dd>
                    </div>
                    <div class="sp-row">
                        <dt>Tinggi / Berat</dt>
                        <dd style="color: var(--sp-blue);">
                            {{ $biography->tinggi ?? '-' }} cm
                            <span style="color: #cbd5e1; margin: 0 .25rem;">/</span>
                            {{ $biography->berat ?? '-' }} kg
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="sp-card sp-section">
                <div class="sp-section-head">
                    <h3 class="sp-title"><i class="bi bi-building"></i> Data Akademi</h3>
                </div>
                <dl class="sp-dl">
                    <div class="sp-row">
                        <dt>Tanggal Bergabung</dt>
                        <dd>{{ $tglGabung ? $tglGabung->copy()->locale('id')->translatedFormat('d F Y') : '-' }}</dd>
                    </div>
                    <div class="sp-row">
                        <dt>Lama Bergabung</dt>
                        <dd><span class="sp-badge-amber">{{ $durasiText }}</span></dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Kolom kanan --}}
        <div class="sp-col">

            {{-- Statistik Pertandingan --}}
            <div class="sp-card sp-section">
                <div class="sp-section-head">
                    <h3 class="sp-title"><i class="bi bi-bar-chart-line"></i> Statistik Pertandingan</h3>
                    <button type="button" class="sp-btn sp-btn-soft" onclick="openModal('modal-statistik')">
                        <i class="bi bi-plus-lg"></i> Tambah Data
                    </button>
                </div>

                @if ($stats->count() > 0)
                    <div class="sp-table-wrap">
                        <table class="sp-table">
                            <thead>
                                <tr>
                                    <th style="text-align:left;">Pertandingan / Lawan</th>
                                    <th>Menit</th>
                                    <th>Gol</th>
                                    <th>Assist</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stats as $stat)
                                    <tr>
                                        <td class="sp-match">
                                            <p class="sp-match-name">{{ $stat->nama_pertandingan }}</p>
                                            <p class="sp-match-sub">
                                                vs {{ $stat->nama_lawan }} &middot;
                                                {{ \Carbon\Carbon::parse($stat->tanggal)->locale('id')->translatedFormat('d M Y') }}
                                            </p>
                                        </td>
                                        <td>{{ $stat->menit_bermain }}'</td>
                                        <td class="sp-goal">{{ $stat->gol }}</td>
                                        <td style="font-weight:600; color: var(--sp-slate-700);">{{ $stat->assist }}</td>
                                        <td>
                                            @if ($stat->nilai_evaluasi !== null)
                                                <span class="sp-score">{{ $stat->nilai_evaluasi }}</span>
                                            @else
                                                <span style="color: #cbd5e1;">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="sp-empty">
                        <div class="sp-empty-icon"><i class="bi bi-clipboard-data"></i></div>
                        <h4>Belum ada statistik</h4>
                        <p>Data performa pertandingan atlet akan muncul di sini.</p>
                    </div>
                @endif
            </div>

            {{-- Prestasi & Medali --}}
            <div class="sp-card sp-section">
                <div class="sp-section-head">
                    <h3 class="sp-title"><i class="bi bi-trophy"></i> Daftar Prestasi & Medali</h3>
                    <button type="button" class="sp-btn sp-btn-soft" onclick="openModal('modal-prestasi')">
                        <i class="bi bi-plus-lg"></i> Tambah Prestasi
                    </button>
                </div>

                @if ($prestasiList->count() > 0)
                    <div class="sp-achv-grid">
                        @foreach ($prestasiList as $prestasi)
                            <div class="sp-achv">
                                <div class="sp-achv-icon"><i class="bi bi-award-fill"></i></div>
                                <div style="min-width:0;">
                                    <h4 class="sp-achv-title">{{ $prestasi->nama_turnamen }}</h4>
                                    <p class="sp-achv-sub">
                                        {{ $prestasi->pencapaian }}
                                        <span>&nbsp;|&nbsp;{{ $prestasi->kategori }} ({{ $prestasi->tingkat }})</span>
                                    </p>
                                    <div class="sp-achv-meta">
                                        <span>
                                            <i class="bi bi-calendar3"></i>
                                            {{ \Carbon\Carbon::parse($prestasi->tanggal)->locale('id')->translatedFormat('d F Y') }}
                                        </span>
                                        @if ($prestasi->penghargaan)
                                            <span class="sp-tag">{{ $prestasi->penghargaan }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="sp-empty">
                        <div class="sp-empty-icon"><i class="bi bi-award"></i></div>
                        <h4>Belum ada medali</h4>
                        <p>Tambahkan pencapaian turnamen untuk atlet ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ================= MODAL STATISTIK ================= --}}
<div id="modal-statistik" class="sp-modal no-print" role="dialog" aria-modal="true" aria-labelledby="judul-statistik">
    <div class="sp-modal-box">
        <form action="{{ route('admin.biography.statistic.store', $biography->id) }}" method="POST">
            @csrf
            <div class="sp-modal-body">
                <div class="sp-section-head">
                    <h3 class="sp-modal-title" id="judul-statistik"><i class="bi bi-bar-chart-line"></i> Input Statistik Baru</h3>
                    <button type="button" class="sp-close" onclick="closeModal('modal-statistik')" aria-label="Tutup">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="sp-form-grid">
                    <div>
                        <label class="sp-label" for="nama_pertandingan">Nama Pertandingan</label>
                        <input id="nama_pertandingan" class="sp-input" type="text" name="nama_pertandingan" required>
                    </div>
                    <div>
                        <label class="sp-label" for="nama_lawan">Nama Lawan</label>
                        <input id="nama_lawan" class="sp-input" type="text" name="nama_lawan" required>
                    </div>
                    <div>
                        <label class="sp-label" for="tanggal_tanding">Tanggal Tanding</label>
                        <input id="tanggal_tanding" class="sp-input" type="date" name="tanggal" required>
                    </div>
                    <div>
                        <label class="sp-label" for="menit_bermain">Menit Bermain</label>
                        <input id="menit_bermain" class="sp-input" type="number" name="menit_bermain" min="0" value="0" required>
                    </div>
                    <div>
                        <label class="sp-label" for="gol">Gol</label>
                        <input id="gol" class="sp-input" type="number" name="gol" min="0" value="0" required>
                    </div>
                    <div>
                        <label class="sp-label" for="assist">Assist</label>
                        <input id="assist" class="sp-input" type="number" name="assist" min="0" value="0" required>
                    </div>
                    <div class="sp-form-full">
                        <label class="sp-label" for="nilai_evaluasi">Nilai Evaluasi (1-100) <small>· opsional</small></label>
                        <input id="nilai_evaluasi" class="sp-input" type="number" name="nilai_evaluasi" min="1" max="100">
                    </div>
                </div>
            </div>

            <div class="sp-modal-foot">
                <button type="button" class="sp-btn sp-btn-light" onclick="closeModal('modal-statistik')">Batal</button>
                <button type="submit" class="sp-btn sp-btn-blue">Simpan Statistik</button>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL PRESTASI ================= --}}
<div id="modal-prestasi" class="sp-modal no-print" role="dialog" aria-modal="true" aria-labelledby="judul-prestasi">
    <div class="sp-modal-box">
        <form action="{{ route('admin.biography.achievement.store', $biography->id) }}" method="POST">
            @csrf
            <div class="sp-modal-body">
                <div class="sp-section-head">
                    <h3 class="sp-modal-title" id="judul-prestasi"><i class="bi bi-trophy"></i> Input Prestasi Baru</h3>
                    <button type="button" class="sp-close" onclick="closeModal('modal-prestasi')" aria-label="Tutup">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="sp-form-grid">
                    <div class="sp-form-full">
                        <label class="sp-label" for="nama_turnamen">Nama Turnamen</label>
                        <input id="nama_turnamen" class="sp-input" type="text" name="nama_turnamen" placeholder="Cth: Liga Futsal Pelajar" required>
                    </div>
                    <div>
                        <label class="sp-label" for="tanggal_prestasi">Tanggal</label>
                        <input id="tanggal_prestasi" class="sp-input" type="date" name="tanggal" required>
                    </div>
                    <div>
                        <label class="sp-label" for="tingkat">Tingkat</label>
                        <select id="tingkat" class="sp-input" name="tingkat" required>
                            <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                            <option value="Provinsi">Provinsi</option>
                            <option value="Nasional">Nasional</option>
                            <option value="Internasional">Internasional</option>
                        </select>
                    </div>
                    <div>
                        <label class="sp-label" for="kategori">Kategori Usia</label>
                        <input id="kategori" class="sp-input" type="text" name="kategori" placeholder="Cth: U-13, Umum" required>
                    </div>
                    <div>
                        <label class="sp-label" for="pencapaian">Pencapaian</label>
                        <input id="pencapaian" class="sp-input" type="text" name="pencapaian" placeholder="Cth: Juara 1, Runner Up" required>
                    </div>
                    <div class="sp-form-full">
                        <label class="sp-label" for="penghargaan">Penghargaan Individu <small>· opsional</small></label>
                        <input id="penghargaan" class="sp-input" type="text" name="penghargaan" placeholder="Cth: Top Skor, Best Player">
                    </div>
                </div>
            </div>

            <div class="sp-modal-foot">
                <button type="button" class="sp-btn sp-btn-light" onclick="closeModal('modal-prestasi')">Batal</button>
                <button type="submit" class="sp-btn sp-btn-blue">Simpan Prestasi</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        var el = document.getElementById(id);
        if (!el) return;
        el.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        var el = document.getElementById(id);
        if (!el) return;
        el.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    // Tutup modal dengan tombol Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeModal('modal-statistik');
            closeModal('modal-prestasi');
        }
    });

    // Tutup modal saat klik area gelap di luar kotak
    document.querySelectorAll('.sp-modal').forEach(function (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) closeModal(modal.id);
        });
    });
</script>
@endsection