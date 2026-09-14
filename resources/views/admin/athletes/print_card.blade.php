<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak ID Card - {{ $athlete->nama }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- JsBarcode Library -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        /* Ukuran Standar ID Card Badge Portrait (70mm x 105mm) */
        .id-card {
            width: 70mm;
            height: 105mm;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            margin: 0 auto;
            position: relative;
            overflow: hidden;
            border: 1px solid #cbd5e1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        /* Header Hijau Superseed */
        .card-header-custom {
            background: linear-gradient(135deg, #064e3b 0%, #047857 100%);
            color: white;
            text-align: center;
            padding: 10px 5px;
            border-bottom: 3px solid #f59e0b; /* Garis emas */
        }
        .logo-text {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
            margin: 0;
            text-transform: uppercase;
        }
        /* Foto Atlet */
        .photo-area {
            text-align: center;
            margin-top: 8px;
            position: relative;
        }
        .photo-box {
            width: 25mm;
            height: 28mm;
            background-color: #e2e8f0;
            border: 2px solid #047857;
            border-radius: 6px;
            margin: 0 auto;
            object-fit: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #64748b;
        }
        /* Nomor Punggung di bawah nama */
        .jersey-block {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin: 3px 0 2px 0;
        }
        .jersey-label {
            font-size: 7.5px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .jersey-badge {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #000;
            font-weight: 900;
            font-size: 11px;
            padding: 1px 8px;
            border-radius: 20px;
            border: 1.5px solid #fde68a;
            letter-spacing: 0.5px;
            box-shadow: 0 1px 3px rgba(245,158,11,0.4);
        }
        /* Detail Atlet */
        .details-area {
            text-align: center;
            padding: 0 8px;
        }
        .athlete-name {
            font-size: 13px;
            font-weight: 800;
            color: #0f172a;
            margin: 4px 0 2px 0;
            line-height: 1.2;
            text-transform: uppercase;
        }
        .badge-category {
            font-size: 9px;
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 2px;
        }
        .position-text {
            font-size: 9px;
            color: #64748b;
            font-weight: 600;
            display: block;
        }
        /* Area Barcode di Bawah */
        .barcode-area {
            text-align: center;
            background: #f8fafc;
            padding: 6px 0 4px 0;
            border-top: 1px dashed #cbd5e1;
        }
        .barcode-svg {
            max-width: 90%;
            height: auto;
        }
        /* Pengaturan Mode Cetak (Print) */
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            body {
                background: none !important;
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
            .id-card {
                box-shadow: none;
                border: 1px solid #cbd5e1;
                margin: 0;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <!-- Tombol Navigasi (Hilang saat dicetak) -->
    <div class="text-center mb-4 no-print">
        <button onclick="window.print()" class="btn btn-primary font-weight-bold px-4 me-2 shadow">
            🖨️ Cetak ID Card Sekarang
        </button>
        <button onclick="window.close()" class="btn btn-secondary px-3">Tutup Jendela</button>
    </div>

    <!-- KARTU ID ATHLETE -->
    <div class="id-card">
        <!-- Header -->
        <div class="card-header-custom">
            <p class="logo-text" style="display:flex;align-items:center;justify-content:center;gap:5px;">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width:20px;height:20px;object-fit:contain;">
                    SUPERSEED ACADEMY
                </p>

            <span style="font-size: 7px; opacity: 0.8; letter-spacing: 0.5px;">KARTU IDENTITAS & ABSENSI ATLET</span>
        </div>

        <!-- Foto -->
        <div class="photo-area">
            @if($athlete->foto)
                <img src="{{ storage_img_url($athlete->foto) }}" class="photo-box" alt="Foto">
            @else
                <div class="photo-box">👤</div>
            @endif
        </div>

        <!-- Biodata Singkat -->
        <div class="details-area">
            <h4 class="athlete-name">{{ Str::limit($athlete->nama, 22) }}</h4>
            <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 2px 8px; display: inline-block; margin: 2px 0 4px 0;">
                <span style="font-size: 7px; color: #64748b; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: -2px;">Nomor Induk Atlet</span>
                <span style="font-size: 11px; font-weight: 800; color: #047857; font-family: monospace;">{{ $athlete->kode_barcode }}</span>
            </div>
            @if($athlete->nomor_punggung)
            <div class="jersey-block">
                <span class="jersey-label">Nomor Punggung</span>
                <span class="jersey-badge">#{{ $athlete->nomor_punggung }}</span>
            </div>
            @endif
            <div>
                <span class="badge-category">{{ $athlete->kelompok_umur ?? 'U-12' }}</span>
                <span class="badge-category" style="background:#fffbeb; color:#b45309; border-color:#fde68a;">{{ $athlete->kelompok_latihan ?? 'Reguler' }}</span>
            </div>
            <span class="position-text">{{ $athlete->posisi_bermain ?? 'Siswa Akademi' }}</span>
        </div>

        <!-- Barcode Scanner Area -->
        <div class="barcode-area">
            <!-- Elemen SVG tempat JsBarcode menggambar barcode -->
            <svg id="barcode_{{ $athlete->id }}" class="barcode-svg"></svg>
            <div style="font-size: 7px; color: #94a3b8; font-weight: bold; margin-top: -2px;">Wajib dibawa saat jadwal latihan & pertandingan</div>
        </div>
    </div>

    <!-- Script Render Barcode Otomatis -->
    <script>
        JsBarcode("#barcode_{{ $athlete->id }}", "{{ $athlete->kode_barcode }}", {
            format: "CODE128",
            width: 1.5,
            height: 35,
            displayValue: true,
            fontSize: 10,
            fontOptions: "bold",
            lineColor: "#0f172a",
            margin: 2
        });
    </script>
</body>
</html>