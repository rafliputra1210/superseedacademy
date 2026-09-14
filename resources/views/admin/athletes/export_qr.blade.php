<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ekspor QR Code Atlet - Superseed Academy</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- QRCode JS Library (Stabil & Tajam) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            color: #0f172a;
        }

        /* Toolbar Navigasi atas */
        .export-toolbar {
            background: #ffffff;
            border-radius: 12px;
            padding: 16px 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 24px;
            border: 1px solid #e2e8f0;
        }

        /* Standard ID Card Badge (72mm x 112mm) */
        .id-card {
            width: 72mm;
            min-height: 112mm;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
            position: relative;
            overflow: hidden;
            border: 1.5px solid #cbd5e1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-inside: avoid;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .id-card:hover {
            box-shadow: 0 10px 24px rgba(0,0,0,0.18);
        }

        /* Header Hijau Superseed */
        .card-header-custom {
            background: linear-gradient(135deg, #064e3b 0%, #047857 100%);
            color: white;
            text-align: center;
            padding: 10px 8px;
            border-bottom: 3.5px solid #f59e0b; /* Garis Emas */
        }
        .logo-text {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
            margin: 0;
            text-transform: uppercase;
        }

        /* Photo Area */
        .photo-area {
            text-align: center;
            margin-top: 8px;
            position: relative;
        }
        .photo-box {
            width: 23mm;
            height: 26mm;
            background-color: #f1f5f9;
            border: 2px solid #047857;
            border-radius: 8px;
            margin: 0 auto;
            object-fit: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #64748b;
        }

        /* Detail Atlet */
        .details-area {
            text-align: center;
            padding: 0 10px;
        }
        .athlete-name {
            font-size: 13px;
            font-weight: 800;
            color: #0f172a;
            margin: 4px 0 2px 0;
            line-height: 1.25;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Badge Nomor Induk SSA-2026-xxx */
        .nomor-induk-box {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 6px;
            padding: 2px 10px;
            display: inline-block;
            margin: 2px 0 4px 0;
        }
        .nomor-induk-label {
            font-size: 7px;
            color: #047857;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: -2px;
        }
        .nomor-induk-val {
            font-size: 11.5px;
            font-weight: 800;
            color: #064e3b;
            letter-spacing: 0.5px;
            font-family: monospace;
        }

        .jersey-badge {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #000;
            font-weight: 900;
            font-size: 9.5px;
            padding: 1px 7px;
            border-radius: 20px;
            border: 1px solid #fde68a;
            letter-spacing: 0.3px;
        }

        .badge-category {
            font-size: 8.5px;
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: 700;
            display: inline-block;
        }

        /* Area QR Code Dengan Nama & Nomor Induk Terpampang Jelas */
        .qr-area {
            text-align: center;
            background: #fafafa;
            padding: 8px 6px 8px 6px;
            border-top: 1px dashed #cbd5e1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .qr-box-container {
            background: #ffffff;
            border: 1.5px solid #047857;
            border-radius: 10px;
            padding: 6px 10px 8px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 2px 8px rgba(4,120,87,0.1);
        }
        .qr-image-holder {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .qr-image-holder img, .qr-image-holder canvas {
            display: block;
            margin: 0 auto;
            width: 105px !important;
            height: 105px !important;
        }
        
        /* Label Nama Siswa & Nomor Induk di Bawah Gambar QR */
        .qr-caption-name {
            font-size: 10px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-top: 4px;
            line-height: 1.2;
            max-width: 180px;
        }
        .qr-caption-nim {
            font-size: 10px;
            font-weight: 800;
            color: #047857;
            font-family: monospace;
            letter-spacing: 0.5px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 4px;
            padding: 0px 6px;
            margin-top: 2px;
            display: inline-block;
        }

        .qr-subtext {
            font-size: 7.5px;
            color: #64748b;
            font-weight: 700;
            margin-top: 4px;
            letter-spacing: 0.3px;
        }

        /* Container Layout */
        .cards-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .card-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .action-btn-group {
            display: flex;
            gap: 4px;
        }

        .btn-mini {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            border: none;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-mini-print {
            background: #047857;
            color: #fff;
        }
        .btn-mini-print:hover {
            background: #064e3b;
        }
        .btn-mini-dl {
            background: #2563eb;
            color: #fff;
        }
        .btn-mini-dl:hover {
            background: #1d4ed8;
        }

        /* Print Media Styles */
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
                page-break-inside: avoid;
            }
            .cards-container {
                gap: 5mm;
            }
        }
    </style>
</head>
<body>

    <!-- TOOLBAR ATAS (Hanya tampil di layar) -->
    <div class="export-toolbar no-print">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h5 class="fw-bold text-dark mb-1">
                    <i class="bi bi-qr-code-scan text-success me-2"></i>Ekspor QR Code Atlet Superseed
                </h5>
                <p class="text-muted small mb-0">
                    Setiap QR Code dilengkapi <strong>Nama Atlet</strong> dan <strong>Nomor Induk (SSA-2026-xxx)</strong> agar sangat mudah diidentifikasi. Total {{ $athletes->count() }} siswa.
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button onclick="window.print()" class="btn btn-success fw-bold px-3 d-flex align-items-center gap-2 shadow-sm">
                    <i class="bi bi-printer-fill"></i> Cetak Semua QR Code
                </button>
                <button onclick="downloadAllQr()" class="btn btn-primary fw-bold px-3 d-flex align-items-center gap-2 shadow-sm">
                    <i class="bi bi-download"></i> Download Semua QR (PNG)
                </button>
                <button onclick="window.close()" class="btn btn-outline-secondary px-3">
                    <i class="bi bi-x-lg"></i> Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- CONTAINER SEMUA KARTU QR CODE ATLET -->
    <div class="cards-container">
        @forelse($athletes as $athlete)
        <div class="card-wrapper" id="wrapper_{{ $athlete->id }}">
            
            <!-- Tombol Aksi Satuan -->
            <div class="action-btn-group no-print">
                <button class="btn-mini btn-mini-print" onclick="cetakSatuan({{ $athlete->id }})" title="Cetak Kartu QR Satuan">
                    <i class="bi bi-printer"></i> Cetak
                </button>
                <button class="btn-mini btn-mini-dl" onclick="downloadQrComposite({{ $athlete->id }}, '{{ Str::slug($athlete->nama) }}', '{{ $athlete->nama }}', '{{ $athlete->kode_barcode }}')" title="Download Image QR dengan Nama & ID">
                    <i class="bi bi-download"></i> PNG QR
                </button>
            </div>

            <!-- KARTU IDENTITAS QR -->
            <div class="id-card" id="card_{{ $athlete->id }}">
                
                <!-- Header Card -->
                <div class="card-header-custom">
                    <p class="logo-text d-flex align-items-center justify-content-center gap-1">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width:18px;height:18px;object-fit:contain;">
                        SUPERSEED ACADEMY
                    </p>
                    <span style="font-size: 7px; opacity: 0.85; letter-spacing: 0.5px;">KARTU IDENTITAS & ABSENSI QR</span>
                </div>

                <!-- Foto Atlet -->
                <div class="photo-area">
                    @if($athlete->foto)
                        <img src="{{ storage_img_url($athlete->foto) }}" class="photo-box" alt="Foto Atlet">
                    @else
                        <div class="photo-box">👤</div>
                    @endif
                </div>

                <!-- Biodata atlet (Nama & Nomor Induk) -->
                <div class="details-area">
                    <h4 class="athlete-name">{{ Str::limit($athlete->nama, 22) }}</h4>
                    
                    <!-- BOX NOMOR INDUK (SSA-2026-xxx) -->
                    <div class="nomor-induk-box">
                        <span class="nomor-induk-label">Nomor Induk Atlet</span>
                        <span class="nomor-induk-val">{{ $athlete->kode_barcode ?? ('SSA-2026-' . sprintf('%04d', $athlete->id)) }}</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-center gap-1 mt-1">
                        @if($athlete->nomor_punggung)
                            <span class="jersey-badge">#{{ $athlete->nomor_punggung }}</span>
                        @endif
                        <span class="badge-category">{{ $athlete->kelompok_umur ?? 'U-12' }}</span>
                        <span class="badge-category" style="background:#fffbeb; color:#b45309; border-color:#fde68a;">{{ $athlete->kelompok_latihan ?? 'Reguler' }}</span>
                    </div>
                </div>

                <!-- AREA QR CODE DENGAN NAMA & NOMOR INDUK DI DALAMNYA -->
                <div class="qr-area">
                    <div class="qr-box-container">
                        <!-- Gambar QR Code -->
                        <div id="qr_box_{{ $athlete->id }}" class="qr-image-holder" data-qr-val="{{ $athlete->kode_barcode }}"></div>
                        
                        <!-- Keterangan Nama Atlet & Nomor Induk di bawah QR -->
                        <div class="qr-caption-name">{{ Str::limit($athlete->nama, 22) }}</div>
                        <div class="qr-caption-nim">{{ $athlete->kode_barcode ?? ('SSA-2026-' . sprintf('%04d', $athlete->id)) }}</div>
                    </div>
                    <div class="qr-subtext">Pindai QR ini pada Kamera Scanner Absensi</div>
                </div>

            </div><!-- end .id-card -->

        </div><!-- end .card-wrapper -->
        @empty
        <div class="text-center py-5">
            <i class="bi bi-qr-code fs-1 text-muted d-block mb-2"></i>
            <h6 class="fw-bold text-secondary">Tidak ada data atlet ditemukan</h6>
        </div>
        @endforelse
    </div>

    <!-- SCRIPT RENDER QR CODE & EMBEDDED DOWNLOAD -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @foreach($athletes as $athlete)
                (function() {
                    const container = document.getElementById("qr_box_{{ $athlete->id }}");
                    const qrVal = "{{ $athlete->kode_barcode }}";
                    if (container && qrVal) {
                        new QRCode(container, {
                            text: qrVal,
                            width: 105,
                            height: 105,
                            colorDark: "#047857",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.H
                        });
                    }
                })();
            @endforeach
        });

        // 1. FUNGSI CETAK SATUAN
        function cetakSatuan(athleteId) {
            const cardEl = document.getElementById('card_' + athleteId);
            if (!cardEl) return;

            const cardHTML = cardEl.outerHTML;
            let styles = '';
            document.querySelectorAll('style').forEach(s => styles += s.innerHTML);

            const printWin = window.open('', '_blank', 'width=450,height=650');
            printWin.document.open();
            printWin.document.write(`
                <!DOCTYPE html>
                <html lang="id">
                <head>
                    <meta charset="UTF-8">
                    <title>Cetak QR Card Atlet</title>
                    <style>
                        body { background: none; margin: 0; padding: 20px; display: flex; justify-content: center; align-items: flex-start; }
                        ${styles}
                        .no-print { display: none !important; }
                        .id-card { box-shadow: none; border: 1px solid #000; }
                        @media print { body { padding: 0; } }
                    <\/style>
                </head>
                <body>
                    ${cardHTML}
                    <script>
                        setTimeout(function(){ window.print(); window.close(); }, 400);
                    <\/script>
                </body>
                </html>
            `);
            printWin.document.close();
        }

        // 2. FUNGSI DOWNLOAD SINGLE QR BERISI NAMA & NOMOR INDUK
        function downloadQrComposite(athleteId, athleteSlug, athleteName, athleteNim) {
            const container = document.getElementById('qr_box_' + athleteId);
            if (!container) return;
            
            const img = container.querySelector('img');
            const sourceCanvas = container.querySelector('canvas');

            const tempCanvas = document.createElement('canvas');
            const ctx = tempCanvas.getContext('2d');

            // Ukuran canvas hasil download: 240px x 300px
            tempCanvas.width = 240;
            tempCanvas.height = 310;

            // 1. Latar Belakang Putih
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);

            // 2. Garis Border Hijau
            ctx.strokeStyle = '#047857';
            ctx.lineWidth = 4;
            ctx.strokeRect(6, 6, tempCanvas.width - 12, tempCanvas.height - 12);

            // 3. Header Text "SUPERSEED ACADEMY"
            ctx.fillStyle = '#064e3b';
            ctx.font = 'bold 11px "Segoe UI", sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('SUPERSEED ACADEMY', tempCanvas.width / 2, 28);

            // 4. Render Gambar QR Code
            const drawQr = (qrImgSource) => {
                ctx.drawImage(qrImgSource, (tempCanvas.width - 150) / 2, 38, 150, 150);

                // 5. Nama Atlet
                ctx.fillStyle = '#0f172a';
                ctx.font = 'bold 13px "Segoe UI", sans-serif';
                ctx.textAlign = 'center';
                
                // Truncate nama jika terlalu panjang
                let displayName = athleteName.toUpperCase();
                if (displayName.length > 22) displayName = displayName.substring(0, 20) + '...';
                ctx.fillText(displayName, tempCanvas.width / 2, 212);

                // 6. Box Nomor Induk (SSA-2026-xxxx)
                ctx.fillStyle = '#ecfdf5';
                ctx.strokeStyle = '#a7f3d0';
                ctx.lineWidth = 1;
                const boxW = 150;
                const boxH = 26;
                const boxX = (tempCanvas.width - boxW) / 2;
                const boxY = 224;
                ctx.fillRect(boxX, boxY, boxW, boxH);
                ctx.strokeRect(boxX, boxY, boxW, boxH);

                ctx.fillStyle = '#047857';
                ctx.font = 'bold 12px monospace';
                ctx.fillText(athleteNim || 'SSA-2026', tempCanvas.width / 2, boxY + 18);

                // 7. Footer Text
                ctx.fillStyle = '#64748b';
                ctx.font = '9px "Segoe UI", sans-serif';
                ctx.fillText('KARTU ABSENSI RESMI ATLET', tempCanvas.width / 2, 280);

                // Unduh sebagai PNG
                const link = document.createElement('a');
                link.download = 'QR_' + athleteSlug + '_' + (athleteNim || 'SSA-2026') + '.png';
                link.href = tempCanvas.toDataURL('image/png');
                link.click();
            };

            if (img && img.src && img.src.startsWith('data:image')) {
                const qImage = new Image();
                qImage.onload = () => drawQr(qImage);
                qImage.src = img.src;
            } else if (sourceCanvas) {
                drawQr(sourceCanvas);
            } else {
                alert('QR Code belum selesai dibuat.');
            }
        }

        // 3. FUNGSI DOWNLOAD ALL QR CODES
        function downloadAllQr() {
            @foreach($athletes as $athlete)
                setTimeout(() => {
                    downloadQrComposite({{ $athlete->id }}, '{{ Str::slug($athlete->nama) }}', '{{ $athlete->nama }}', '{{ $athlete->kode_barcode }}');
                }, {{ $loop->index * 300 }});
            @endforeach
        }
    </script>
</body>
</html>
