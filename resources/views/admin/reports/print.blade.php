<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Raport - {{ $raport->athlete->nama ?? 'Atlet' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm;
        }
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #1e293b;
            background: #fff;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.4;
        }
        
        /* Kop Dokumen */
        .kop {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }
        .kop-logo img { height: 55px; object-fit: contain; }
        .kop-text { text-align: center; flex: 1; }
        .kop-text h1 { margin: 0; font-size: 18px; font-weight: 800; color: #0A192F; letter-spacing: 0.5px; }
        .kop-text p { margin: 2px 0 0; font-size: 10.5px; color: #64748b; }
        
        .doc-title {
            text-align: center;
            font-size: 15px;
            font-weight: 800;
            text-transform: uppercase;
            margin: 10px 0 16px;
            color: #0f172a;
            letter-spacing: 0.5px;
        }

        /* Info Atlet */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 16px;
            font-size: 11.5px;
        }
        .info-row { display: flex; }
        .info-label { width: 130px; font-weight: bold; color: #475569; }
        .info-val { flex: 1; font-weight: 600; color: #0f172a; }

        /* Tabel Standar */
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        th, td { border: 1px solid #cbd5e1; padding: 7px 10px; vertical-align: top; }
        th { background: #f1f5f9; font-weight: 700; color: #334155; text-align: left; }
        
        .section-header {
            font-weight: 800;
            font-size: 13px;
            color: #0f172a;
            margin: 14px 0 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .section-desc { font-size: 11px; margin-bottom: 8px; color: #64748b; font-style: italic; }

        .table-scores th { text-align: center; font-size: 11px; }
        .table-scores td.score-col { text-align: center; font-weight: bold; width: 85px; }
        
        /* Box Rekap & Observasi */
        .observasi-table td { font-size: 11.5px; }
        .rekap-box {
            background-color: #f0fdf4;
            border: 1.5px solid #86efac;
        }
        
        .page-break { page-break-inside: avoid; }

        /* Tanda Tangan */
        .ttd-wrap {
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .ttd-box { text-align: center; width: 200px; font-size: 11.5px; }
        .ttd-space { height: 60px; }
        .ttd-line { border-top: 1px solid #334155; font-weight: bold; padding-top: 4px; }

        /* Layar kontrol */
        @media screen {
            body { background: #e2e8f0; padding: 20px; }
            .sheet {
                background: #fff;
                max-width: 210mm;
                margin: 0 auto;
                padding: 15mm;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                border-radius: 4px;
            }
            .screen-controls {
                max-width: 210mm;
                margin: 0 auto 15px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .btn {
                padding: 8px 16px;
                border-radius: 6px;
                border: none;
                cursor: pointer;
                font-weight: bold;
                font-size: 13px;
                text-decoration: none;
            }
            .btn-print { background: #0066FF; color: #fff; }
            .btn-close { background: #64748b; color: #fff; }
        }
        @media print {
            .no-print { display: none !important; }
            .sheet { width: 100%; margin: 0; padding: 0; box-shadow: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="screen-controls no-print">
        <button onclick="window.close()" class="btn btn-close">&larr; Tutup Halaman</button>
        <button onclick="window.print()" class="btn btn-print">Cetak / Simpan PDF</button>
    </div>

    <div class="sheet">
        <!-- KOP DOKUMEN -->
        <div class="kop">
            <div class="kop-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Super Seed" onerror="this.style.display='none'">
            </div>
            <div class="kop-text">
                <h1>SUPER SEED ACADEMY</h1>
                <p>Sekretariat: Perum Puri Lestari Blok E RT.003 RW.016</p>
                <p>Desa Sukajaya, Kec. Cibitung, Kab. Bekasi, Jawa Barat - 17520</p>
                <p>IG: @Super_seed_Academy | Tik Tok: @Super_Seed_Academy | website: https://superseedacademy.id/</p>

            </div>
            <div class="kop-logo" style="width: 55px; text-align: right;">
                <img src="{{ asset('images/logo aafi.png') }}" alt="Logo AAFI" onerror="this.style.display='none'">
            </div>
        </div>

        <div class="doc-title">Laporan Hasil Penilaian &amp; Evaluasi Atlet (Raport)</div>

        <!-- INFO ATLET -->
        <div class="info-grid">
            <div class="info-row">
                <span class="info-label">Nama Atlet</span>
                <span class="info-val">: {{ $raport->athlete->nama ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Periode Penilaian</span>
                <span class="info-val">: {{ $raport->periode ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Nomor Induk (NIS)</span>
                <span class="info-val">: {{ $raport->athlete->nomor_induk ?? $raport->athlete->kode_barcode ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kelompok Latihan</span>
                <span class="info-val">: {{ $raport->athlete->kelompok_latihan ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kelompok Umur</span>
                <span class="info-val">: KU-{{ $raport->athlete->kelompok_umur ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Posisi Bermain</span>
                <span class="info-val">: {{ $raport->athlete->posisi_bermain ?? '-' }}</span>
            </div>
        </div>

        <!-- RINCIAN ASPEK PENILAIAN (SKALA 1 - 5) -->
        @if(!empty($raport->scores_summary) && is_array($raport->scores_summary))
            @foreach($raport->scores_summary as $catIdx => $catSum)
                @php
                    $subItems = $catSum['sub_points'] ?? [];
                    $catScore = (float)($catSum['nilai'] ?? 0);
                @endphp
                <div class="page-break" style="margin-bottom: 12px;">
                    <div class="section-header">
                        <span>{{ chr(65 + $loop->index) }}. Penilaian {{ $catSum['nama'] }}</span>
                        <span style="font-weight: normal; font-size: 11px; margin-left: auto;">(Rata-rata: <b>{{ $catScore }}</b> / 5)</span>
                    </div>

                    @if(!empty($subItems) && count($subItems) > 0)
                        <table class="table-scores">
                            <thead>
                                <tr>
                                    <th style="width: 35px; text-align: center;">No</th>
                                    <th>Aspek Penilaian</th>
                                    <th style="width: 90px; text-align: center;">Skor (1–5)</th>
                                    <th>Catatan / Evaluasi Lapangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subItems as $spIdx => $subItem)
                                    @php $sVal = (float)($subItem['skor'] ?? 0); @endphp
                                    <tr>
                                        <td style="text-align: center; color: #64748b;">{{ $spIdx + 1 }}</td>
                                        <td style="font-weight: 600;">{{ $subItem['aspek'] }}</td>
                                        <td class="score-col">{{ $sVal }}</td>
                                        <td style="color: #475569; font-style: italic;">
                                            {{ !empty($subItem['catatan']) ? $subItem['catatan'] : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @endforeach
        @endif

        @php
            $observasi = [];
            if (!empty($raport->observasi_kualitatif)) {
                $observasi = is_string($raport->observasi_kualitatif) 
                    ? json_decode($raport->observasi_kualitatif, true) 
                    : $raport->observasi_kualitatif;
            }

            $skor100 = (float)($raport->rekap_skor_100 ?? 0);
            $skorRata = (float)($raport->rekap_rata_rata ?? 0);

            // Fallback kalkulasi jika data lama belum terisi
            if ($skor100 <= 0 && !empty($raport->scores_summary) && is_array($raport->scores_summary)) {
                $tmpTot = 0; $tmpC = 0;
                foreach ($raport->scores_summary as $cat) {
                    if (!empty($cat['sub_points']) && is_array($cat['sub_points'])) {
                        foreach ($cat['sub_points'] as $sp) {
                            if (isset($sp['skor']) && (float)$sp['skor'] > 0) {
                                $tmpTot += (float)$sp['skor'];
                                $tmpC++;
                            }
                        }
                    } elseif (!empty($cat['nilai']) && (float)$cat['nilai'] > 0) {
                        $tmpTot += (float)$cat['nilai'];
                        $tmpC++;
                    }
                }
                if ($tmpC > 0) {
                    $skorRata = round($tmpTot / $tmpC, 2);
                    $skor100  = round(($skorRata / 5) * 100);
                }
            }
        @endphp

        <!-- ==========================================
             BAGIAN E: OBSERVASI LAPANGAN (KUALITATIF) 
             ========================================== -->
        <div class="page-break">
            <div class="section-header">E. Observasi Lapangan (Kualitatif)</div>
            <div class="section-desc">Catat hasil pengamatan selama latihan atau pertandingan, termasuk situasi bola mati.</div>
            
            <table class="observasi-table">
                <tr>
                    <td style="width: 32%; font-weight: bold; background: #f8fafc;">Kekuatan Utama (Strengths)</td>
                    <td>{{ !empty($observasi['kekuatan_utama']) ? $observasi['kekuatan_utama'] : '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; background: #f8fafc;">Area yang Perlu Ditingkatkan (Weaknesses)</td>
                    <td>{{ !empty($observasi['area_peningkatan']) ? $observasi['area_peningkatan'] : '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; background: #f8fafc;">Catatan Perilaku / Sikap di Lapangan</td>
                    <td>{{ !empty($observasi['catatan_perilaku']) ? $observasi['catatan_perilaku'] : '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; background: #f8fafc;">Fokus Latihan / Tindak Lanjut</td>
                    <td>{{ !empty($observasi['fokus_latihan']) ? $observasi['fokus_latihan'] : '-' }}</td>
                </tr>
            </table>
        </div>

        <!-- ==========================================
             BAGIAN F: REKAP HASIL PENILAIAN 
             ========================================== -->
        <div class="page-break">
            <div class="section-header">F. Rekap Hasil Penilaian</div>
            
            <table>
                <tr>
                    <td style="width: 55%; vertical-align: middle; padding: 10px 14px;">
                        <div>Total Skor: <b style="font-size: 15px; color: #0066FF;">{{ $skor100 }}</b> / 100</div>
                        <div style="margin-top: 3px;">Rata-rata: <b style="font-size: 15px; color: #059669;">{{ $skorRata }}</b> / 5</div>
                    </td>
                    <td style="width: 45%; background: #f0fdf4; border-left: 2px solid #22c55e; vertical-align: middle;">
                        <span style="font-size: 11px; font-weight: bold; color: #166534; display: block;">Status Kelulusan / Progres:</span>
                        <span style="font-size: 13px; font-weight: 800; color: #15803d;">
                            @if($skor100 >= 85)
                                SANGAT BAIK (EXCELLENT)
                            @elseif($skor100 >= 70)
                                BAIK (GOOD)
                            @elseif($skor100 >= 55)
                                CUKUP (SATISFACTORY)
                            @else
                                PERLU PEMBINAAN KHUSUS
                            @endif
                        </span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 10px 14px;">
                        <div style="font-weight: bold; margin-bottom: 4px; color: #334155;">Kesimpulan / Rekomendasi Asesor &amp; Tim Pelatih:</div>
                        <div style="font-style: italic; color: #1e293b; min-height: 45px; line-height: 1.5;">
                            {{ !empty($raport->catatan_pelatih) ? $raport->catatan_pelatih : '-' }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- TANDA TANGAN -->
        <div class="ttd-wrap">
            <div class="ttd-box">
                <div>Mengetahui,</div>
                <div style="font-weight: bold;">Orang Tua / Wali Murid</div>
                <div class="ttd-space"></div>
                <div class="ttd-line">( ............................................ )</div>
            </div>
            <div class="ttd-box">
                <div>Mojokerto, {{ \Carbon\Carbon::parse($raport->updated_at ?? now())->translatedFormat('d F Y') }}</div>
                <div style="font-weight: bold;">Tim Pelatih &amp; Asesor</div>
                <div class="ttd-space"></div>
                <div class="ttd-line">( Super Seed Academy )</div>
            </div>
        </div>
    </div>

</body>
</html>
