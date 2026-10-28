<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV Atlet - {{ $biography->nama_atlet ?? 'Super Seed' }}</title>
    @vite(['resources/css/app.css'])

    @php
        \Carbon\Carbon::setLocale('id');

        $stats        = $biography->statistics ?? collect();
        $achievements = $biography->achievements ?? collect();

        $totalMain   = $stats->count();
        $totalMenit  = $stats->sum('menit_bermain');
        $totalGol    = $stats->sum('gol');
        $totalAssist = $stats->sum('assist');

        $bergabung = $biography->tanggal_bergabung
            ? \Carbon\Carbon::parse($biography->tanggal_bergabung)
            : null;

        $lama = $bergabung ? $bergabung->diff(now()) : null;

        $inisial = mb_strtoupper(mb_substr($biography->nama_atlet ?? 'A', 0, 1));
        $isAktif = (bool) $biography->status_aktif;
    @endphp

    <style>
        :root {
            --navy: #0f3d3a;
            --navy-dark: #0a2927;
            --green: #059669;
            --green-soft: #ecfdf5;
            --slate-900: #0f172a;
            --slate-700: #334155;
            --slate-500: #64748b;
            --slate-400: #94a3b8;
            --slate-200: #e2e8f0;
            --slate-100: #f1f5f9;
            --slate-50:  #f8fafc;
            --rose: #e11d48;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: #cbd5e1;
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            color: var(--slate-900);
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* ===== Kertas A4 ===== */
        .a4-paper {
            width: 210mm;
            min-height: 297mm;
            margin: 24px auto;
            padding: 14mm 15mm 12mm;
            background: #fff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            display: flex;
            flex-direction: column;
        }

        /* ===== Kop Surat ===== */
        .kop {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-bottom: 10px;
            border-bottom: 4px solid var(--navy);
            position: relative;
        }
        .kop::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: -7px;
            border-bottom: 1px solid var(--green);
        }
        .kop-logo { width: 80px; flex-shrink: 0; }
        .kop-logo img { width: 100%; height: auto; max-height: 90px; object-fit: contain; }
        .kop-text { flex: 1; text-align: center; }
        .kop-text h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 900;
            letter-spacing: 0.18em;
            color: var(--navy);
            line-height: 1.1;
        }
        .kop-text .sub {
            margin: 4px 0 0;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            color: var(--green);
            text-transform: uppercase;
        }
        .kop-text .alamat {
            margin-top: 6px;
            font-size: 10.5px;
            color: var(--slate-500);
            line-height: 1.5;
        }
        .kop-spacer { width: 80px; flex-shrink: 0; }

        .judul-dokumen {
            margin: 16px 0 14px;
            text-align: center;
        }
        .judul-dokumen h2 {
            margin: 0;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: var(--slate-700);
        }

        /* ===== Profil ===== */
        .profil {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 16px 18px;
            background: linear-gradient(135deg, var(--slate-50), #ffffff);
            border: 1px solid var(--slate-200);
            border-left: 5px solid var(--green);
            border-radius: 12px;
        }
        .foto {
            width: 96px;
            height: 96px;
            flex-shrink: 0;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--green);
            background: var(--slate-100);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .foto img { width: 100%; height: 100%; object-fit: cover; }
        .foto .inisial {
            font-size: 38px;
            font-weight: 900;
            color: var(--slate-400);
        }
        .profil-info { flex: 1; }
        .profil-info h3 {
            margin: 0;
            font-size: 22px;
            font-weight: 900;
            text-transform: uppercase;
            color: var(--navy);
        }
        .profil-info .meta {
            margin-top: 3px;
            font-size: 13px;
            font-weight: 700;
            color: var(--green);
        }
        .chips {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .chip {
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
            color: var(--slate-700);
            background: #fff;
            border: 1px solid var(--slate-200);
            border-radius: 999px;
        }
        .chip span { color: var(--slate-500); font-weight: 600; }

        .badge-status {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.05em;
        }
        .badge-aktif    { background: var(--green-soft); color: var(--green); border: 1px solid #a7f3d0; }
        .badge-nonaktif { background: #fff1f2; color: var(--rose); border: 1px solid #fecdd3; }

        /* ===== Ringkasan Angka ===== */
        .ringkasan {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 14px;
        }
        .kartu-angka {
            padding: 10px 8px;
            text-align: center;
            border: 1px solid var(--slate-200);
            border-radius: 10px;
            background: #fff;
        }
        .kartu-angka .nilai {
            font-size: 20px;
            font-weight: 900;
            color: var(--navy);
            line-height: 1;
        }
        .kartu-angka .label {
            margin-top: 5px;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--slate-500);
        }

        /* ===== Layout 2 Kolom ===== */
        .dua-kolom {
            display: flex;
            gap: 22px;
            margin-top: 20px;
        }
        .kolom-kiri  { width: 34%; flex-shrink: 0; }
        .kolom-kanan { width: 66%; }

        .judul-seksi {
            margin: 0 0 10px;
            padding-bottom: 5px;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--navy);
            border-bottom: 2px solid var(--navy);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .judul-seksi::before {
            content: "";
            width: 6px;
            height: 6px;
            background: var(--green);
            border-radius: 50%;
        }
        .blok { margin-bottom: 22px; }

        .tabel-info {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
        }
        .tabel-info td {
            padding: 6px 0;
            border-bottom: 1px dashed var(--slate-200);
            vertical-align: top;
        }
        .tabel-info td:first-child {
            width: 42%;
            color: var(--slate-500);
            font-weight: 600;
        }
        .tabel-info td:last-child { font-weight: 700; color: var(--slate-900); }

        /* ===== Tabel Statistik ===== */
        .tabel-stat {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
        }
        .tabel-stat thead th {
            padding: 8px 6px;
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #fff;
            background: var(--navy);
        }
        .tabel-stat th:first-child { text-align: left; border-radius: 6px 0 0 0; }
        .tabel-stat th:last-child  { border-radius: 0 6px 0 0; }
        .tabel-stat tbody td {
            padding: 7px 6px;
            border-bottom: 1px solid var(--slate-200);
            text-align: center;
        }
        .tabel-stat tbody tr:nth-child(even) td { background: var(--slate-50); }
        .tabel-stat tbody td.lawan { text-align: left; }
        .tabel-stat .lawan strong { display: block; font-size: 12px; }
        .tabel-stat .lawan small { color: var(--slate-500); font-size: 10px; }
        .tabel-stat .gol { font-weight: 800; color: var(--green); }
        .tabel-stat tfoot td {
            padding: 8px 6px;
            font-weight: 900;
            text-align: center;
            background: var(--green-soft);
            color: var(--navy);
            border-top: 2px solid var(--navy);
        }
        .tabel-stat tfoot td:first-child { text-align: left; }

        .kosong {
            padding: 14px;
            font-size: 11px;
            font-style: italic;
            text-align: center;
            color: var(--slate-400);
            border: 1px dashed var(--slate-200);
            border-radius: 8px;
        }

        /* ===== Prestasi ===== */
        .prestasi-list { display: flex; flex-direction: column; gap: 8px; }
        .prestasi {
            position: relative;
            padding: 10px 12px 10px 14px;
            background: #fff;
            border: 1px solid var(--slate-200);
            border-left: 4px solid var(--green);
            border-radius: 8px;
            page-break-inside: avoid;
        }
        .prestasi h4 {
            margin: 0 0 4px;
            font-size: 12.5px;
            font-weight: 800;
            color: var(--slate-900);
            padding-right: 80px;
        }
        .prestasi .detail {
            font-size: 11px;
            color: var(--slate-700);
        }
        .prestasi .detail b { color: var(--green); }
        .prestasi .tahun {
            position: absolute;
            top: 10px;
            right: 12px;
            font-size: 10.5px;
            font-weight: 700;
            color: var(--slate-500);
        }

        /* ===== Tanda Tangan & Footer ===== */
        .ttd {
            margin-top: auto;
            padding-top: 30px;
            display: flex;
            justify-content: space-between;
            gap: 30px;
            font-size: 11px;
        }
        .ttd-box { width: 40%; text-align: center; }
        .ttd-box .jabatan { font-weight: 700; color: var(--slate-700); }
        .ttd-box .garis {
            margin-top: 56px;
            padding-top: 4px;
            border-top: 1px solid var(--slate-700);
            font-weight: 700;
        }

        .footer-cetak {
            margin-top: 18px;
            padding-top: 8px;
            border-top: 1px solid var(--slate-200);
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: var(--slate-400);
        }

        /* ===== Tombol Layar ===== */
        .kontrol {
            text-align: center;
            margin-top: 24px;
        }
        .btn {
            display: inline-block;
            padding: 9px 18px;
            border: 0;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            color: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.12);
        }
        .btn-gelap { background: var(--slate-900); }
        .btn-gelap:hover { background: #1e293b; }
        .btn-hijau { background: var(--green); margin-left: 8px; }
        .btn-hijau:hover { background: #047857; }

        /* ===== Pengaturan Cetak ===== */
        @page {
            size: A4;
            margin: 12mm;
        }

        @media print {
            body { background: #fff; }
            .a4-paper {
                width: 100%;
                min-height: auto;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
            .no-print { display: none !important; }
            .blok, .prestasi, .ringkasan, tr { page-break-inside: avoid; }
            .ttd { margin-top: 30px; }
        }
    </style>
</head>
<body>

    <!-- Kontrol (hanya tampil di layar) -->
    <div class="kontrol no-print">
        <button class="btn btn-gelap" onclick="window.close()">Tutup Halaman</button>
        <button class="btn btn-hijau" onclick="window.print()">Cetak Ulang</button>
    </div>

    <div class="a4-paper">

        <!-- KOP SURAT -->
        <header class="kop">
            <div class="kop-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Super Seed Academy">
            </div>
            <div class="kop-text">
                <h1>SUPER SEED ACADEMY</h1>
                <p class="sub">Pusat Pembinaan Futsal Usia Dini &amp; Muda</p>
                <div class="alamat">
                    <div>Basecamp: Lapangan Futsal Mojokerto, Jawa Timur</div>
                    <div>WhatsApp: 0888-8061-522 &nbsp;|&nbsp; Instagram: @super_seed_academy</div>
                </div>
            </div>
            <div class="kop-spacer"></div>
        </header>

        <div class="judul-dokumen">
            <h2>Curriculum Vitae Atlet</h2>
        </div>

        <!-- PROFIL -->
        <section class="profil">
            <div class="foto">
                @if($biography->foto_profil)
                    <img src="{{ asset('storage/' . $biography->foto_profil) }}" alt="Foto {{ $biography->nama_atlet }}">
                @else
                    <span class="inisial">{{ $inisial }}</span>
                @endif
            </div>

            <div class="profil-info">
                <h3>{{ $biography->nama_atlet ?? 'Nama Atlet' }}</h3>
                <div class="meta">
                    Kelas {{ $biography->kategori_kelas ?? '-' }} &nbsp;•&nbsp; NIS {{ $biography->nis ?? '-' }}
                    @if($biography->no_punggung)
                        &nbsp;•&nbsp; No. {{ $biography->no_punggung }}
                    @endif
                </div>
                <div class="chips">
                    <div class="chip"><span>Posisi:</span> {{ $biography->posisi_bermain ?? '-' }}</div>
                    <div class="chip"><span>Tinggi:</span> {{ $biography->tinggi ? $biography->tinggi . ' cm' : '-' }}</div>
                    <div class="chip"><span>Berat:</span> {{ $biography->berat ? $biography->berat . ' kg' : '-' }}</div>
                </div>
            </div>
        </section>

        <!-- RINGKASAN ANGKA -->
        <section class="ringkasan">
            <div class="kartu-angka">
                <div class="nilai">{{ $totalMain }}</div>
                <div class="label">Pertandingan</div>
            </div>
            <div class="kartu-angka">
                <div class="nilai">{{ $totalMenit }}'</div>
                <div class="label">Total Menit</div>
            </div>
            <div class="kartu-angka">
                <div class="nilai">{{ $totalGol }}</div>
                <div class="label">Gol</div>
            </div>
            <div class="kartu-angka">
                <div class="nilai">{{ $totalAssist }}</div>
                <div class="label">Assist</div>
            </div>
        </section>

        <div class="dua-kolom">

            <!-- KOLOM KIRI -->
            <div class="kolom-kiri">
                <div class="blok">
                    <h3 class="judul-seksi">Data Akademi</h3>
                    <table class="tabel-info">
                        <tr>
                            <td>Status</td>
                            <td>
                                <span class="badge-status {{ $isAktif ? 'badge-aktif' : 'badge-nonaktif' }}">
                                    {{ $isAktif ? 'AKTIF' : 'NON-AKTIF' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Bergabung</td>
                            <td>{{ $bergabung ? $bergabung->translatedFormat('d F Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Masa Latihan</td>
                            <td>
                                @if($lama)
                                    {{ $lama->y > 0 ? $lama->y . ' tahun ' : '' }}{{ $lama->m }} bulan
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="blok">
                    <h3 class="judul-seksi">Atribut Fisik</h3>
                    <table class="tabel-info">
                        <tr>
                            <td>Kaki Dominan</td>
                            <td>{{ $biography->kaki_dominan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Nomor Punggung</td>
                            <td>{{ $biography->no_punggung ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Posisi Bermain</td>
                            <td>{{ $biography->posisi_bermain ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- KOLOM KANAN -->
            <div class="kolom-kanan">
                <div class="blok">
                    <h3 class="judul-seksi">Statistik Pertandingan</h3>

                    @if($stats->count() > 0)
                        <table class="tabel-stat">
                            <thead>
                                <tr>
                                    <th style="width:50%;">Lawan</th>
                                    <th>Menit</th>
                                    <th>Gol</th>
                                    <th>Assist</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats as $stat)
                                <tr>
                                    <td class="lawan">
                                        <strong>{{ $stat->nama_lawan }}</strong>
                                        <small>{{ \Carbon\Carbon::parse($stat->tanggal)->translatedFormat('d M Y') }}</small>
                                    </td>
                                    <td>{{ $stat->menit_bermain }}'</td>
                                    <td class="gol">{{ $stat->gol }}</td>
                                    <td>{{ $stat->assist }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>Total</td>
                                    <td>{{ $totalMenit }}'</td>
                                    <td>{{ $totalGol }}</td>
                                    <td>{{ $totalAssist }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    @else
                        <div class="kosong">Belum ada data statistik pertandingan.</div>
                    @endif
                </div>

                <div class="blok">
                    <h3 class="judul-seksi">Riwayat Prestasi</h3>

                    @if($achievements->count() > 0)
                        <div class="prestasi-list">
                            @foreach($achievements as $prestasi)
                            <div class="prestasi">
                                <span class="tahun">{{ \Carbon\Carbon::parse($prestasi->tanggal)->format('Y') }}</span>
                                <h4>{{ $prestasi->nama_turnamen }}</h4>
                                <div class="detail">
                                    <b>{{ $prestasi->pencapaian }}</b>
                                    &nbsp;|&nbsp; {{ $prestasi->kategori }}
                                    @if($prestasi->tingkat)
                                        &nbsp;({{ $prestasi->tingkat }})
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="kosong">Belum ada data prestasi.</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- TANDA TANGAN -->
        <section class="ttd">
            <div class="ttd-box">
                <div class="jabatan">Orang Tua / Wali</div>
                <div class="garis">( ............................... )</div>
            </div>
            <div class="ttd-box">
                <div class="jabatan">Mojokerto, {{ now()->translatedFormat('d F Y') }}</div>
                <div class="jabatan">Pelatih Kepala</div>
                <div class="garis">( ............................... )</div>
            </div>
        </section>

        <footer class="footer-cetak">
            <span>Dokumen ini dicetak dari sistem Super Seed Academy</span>
            <span>Dicetak: {{ now()->translatedFormat('d M Y H:i') }}</span>
        </footer>
    </div>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 800);
        });
    </script>
</body>
</html>