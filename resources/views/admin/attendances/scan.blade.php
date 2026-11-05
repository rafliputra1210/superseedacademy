@extends('layouts.admin')
@section('title', 'Kamera Scanner Absensi')

@section('content')
<div class="row g-4">
    {{-- Kolom Kiri: Scanner Kamera --}}
    <div class="col-lg-6">
        <div class="card card-custom bg-white p-4 shadow-sm border-0 text-center">
            <h5 class="fw-bold text-dark mb-1">
                <i class="bi bi-qr-code-scan text-success me-2"></i>Kamera Scan Barcode &amp; QR Atlet
            </h5>
            <p class="text-muted small mb-3">Arahkan ID Card atau QR Code atlet ke depan kamera HP / Laptop.</p>

            {{-- Peringatan HTTPS untuk Deployment Hosting (FastPanel / cPanel / VPS) --}}
            <div id="https-warning" class="alert alert-warning border border-warning shadow-sm small text-start mb-3 d-none">
                <div class="d-flex gap-2">
                    <i class="bi bi-shield-exclamation text-warning fs-4 flex-shrink-0"></i>
                    <div>
                        <strong class="text-dark d-block">Peringatan Keamanan Browser (HTTPS Diperlukan):</strong>
                        <span>Halaman ini diakses melalui <code>http://</code> (koneksi tidak terenkripsi). Kebijakan keamanan browser (Chrome, Edge, Safari) <strong>secara otomatis memblokir akses kamera</strong> jika website online belum menggunakan HTTPS.</span>
                        <div class="mt-2 p-2 bg-white rounded border">
                            <strong class="d-block mb-1">Solusi Cepat di FastPanel:</strong>
                            <ol class="mb-1 ps-3 text-secondary">
                                <li>Masuk ke FastPanel &rarr; Menu <strong>Site</strong> &rarr; Pilih domain website.</li>
                                <li>Pilih menu <strong>SSL Certificates</strong> &rarr; Klik <strong>New Certificate</strong> &rarr; Pilih <strong>Let's Encrypt (Gratis)</strong>.</li>
                                <li>Centang opsi <strong>Redirect HTTP to HTTPS</strong>, lalu simpan.</li>
                                <li>Buka kembali halaman ini dengan awalan <strong>https://</strong></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pilihan Dropdown Kamera (Otomatis Terisi setelah kamera terdeteksi) --}}
            <div id="camera-select-wrapper" class="mb-3 d-none">
                <div class="input-group input-group-sm mx-auto" style="max-width: 380px;">
                    <span class="input-group-text bg-light border text-muted small">
                        <i class="bi bi-camera-video me-1"></i>Pilih Kamera:
                    </span>
                    <select id="camera-select" class="form-select form-select-sm" onchange="startScanner(this.value)">
                    </select>
                </div>
            </div>

            {{-- Tombol Kontrol Kamera --}}
            <div class="mb-3 d-flex justify-content-center gap-2 flex-wrap">
                <button type="button" id="btn-start" class="btn btn-sm btn-success fw-bold px-3 shadow-sm" onclick="startScanner()">
                    <i class="bi bi-play-circle-fill me-1"></i> Mulai Kamera
                </button>
                <button type="button" class="btn btn-sm btn-outline-success fw-bold" onclick="startScanner('environment')">
                    <i class="bi bi-phone me-1"></i> Kamera Belakang (HP)
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary fw-bold" onclick="startScanner('user')">
                    <i class="bi bi-laptop me-1"></i> Kamera Depan / Laptop
                </button>
                <button type="button" id="btn-stop" class="btn btn-sm btn-outline-danger fw-bold" onclick="stopScanner()">
                    <i class="bi bi-stop-circle me-1"></i> Stop
                </button>
            </div>

            {{-- Viewport Kamera Reader --}}
            <div id="reader" class="mx-auto rounded-3 border border-success border-2 shadow-sm position-relative overflow-hidden"
                 style="width: 100%; max-width: 420px; min-height: 290px; background: #0f172a; display: flex; align-items: center; justify-content: center;">
                <div id="reader-placeholder" class="text-center p-4 text-white-50">
                    <i class="bi bi-camera-video fs-1 d-block mb-2 text-success"></i>
                    <h6 class="fw-bold text-white mb-1">Kamera Siap Diaktifkan</h6>
                    <small class="d-block mb-3">Klik tombol <strong>"Mulai Kamera"</strong> di atas</small>
                    <button type="button" class="btn btn-sm btn-success px-3 fw-bold" onclick="startScanner()">
                        <i class="bi bi-camera-fill me-1"></i> Aktifkan Kamera Sekarang
                    </button>
                </div>
            </div>

            {{-- Badge Status & Diagnosa Pesan --}}
            <div class="mt-3">
                <div id="scan-status" class="alert alert-secondary py-2 px-3 small mb-0 d-inline-block text-start border-0 shadow-sm" style="max-width: 480px;">
                    <i class="bi bi-info-circle me-1"></i> Kamera belum aktif. Silakan klik tombol <strong>"Mulai Kamera"</strong> di atas.
                </div>
            </div>

            {{-- Alternatif 1: Scan via Foto Kamera HP Langsung / Unggah Gambar ID Card --}}
            <div class="mt-3 pt-2 d-flex justify-content-center gap-2 flex-wrap">
                <input type="file" id="qr-camera-input" accept="image/*" capture="environment" class="d-none" onchange="handleFileInput(event)">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('qr-camera-input').click()">
                    <i class="bi bi-camera me-1"></i> Ambil Foto ID Card (Kamera HP)
                </button>

                <input type="file" id="qr-file-input" accept="image/*" class="d-none" onchange="handleFileInput(event)">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('qr-file-input').click()">
                    <i class="bi bi-image me-1"></i> Upload File Foto / Galeri
                </button>
            </div>

            {{-- Alternatif 2: Input Manual / Scanner Barcode USB Gun --}}
            <div class="mt-4 pt-3 border-top text-start">
                <label class="form-label small fw-bold text-muted">
                    <i class="bi bi-keyboard me-1"></i>Atau Ketik / Scan Barcode Gun USB Manual:
                </label>
                <form id="manualForm" onsubmit="handleManualSubmit(event)">
                    <div class="input-group">
                        <input type="text" id="manual_barcode" class="form-control"
                               placeholder="Contoh: SSA-2026-1234 atau scan via barcode gun" autocomplete="off">
                        <button type="submit" class="btn btn-success fw-bold px-3">
                            <i class="bi bi-arrow-right-circle me-1"></i> Input
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Hasil Scan & Log Absensi Hari Ini --}}
    <div class="col-lg-6">
        <div id="result-box" class="card card-custom p-4 mb-4 text-center border-0 shadow-sm"
             style="background: #f1f5f9; min-height: 140px; display: flex; align-items: center; justify-content: center;">
            <div>
                <i class="bi bi-upc-scan fs-1 text-muted d-block mb-2"></i>
                <h6 class="fw-bold text-muted mb-0">Belum ada barcode / QR yang di-scan</h6>
                <small class="text-muted">Hasil identitas atlet akan langsung muncul di sini</small>
            </div>
        </div>

        <div class="card card-custom bg-white p-4 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="bi bi-clock-history text-primary me-2"></i>Absensi Latihan Hari Ini
                </h6>
                <span class="badge bg-success">{{ date('d M Y') }}</span>
            </div>

            <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                <table class="table table-hover align-middle text-sm mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>Waktu</th>
                            <th>Nama Atlet</th>
                            <th>Kelompok Umur</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="log-table-body">
                        @forelse($todayAttendances as $absen)
                        <tr>
                            <td class="fw-bold text-success">
                                {{ $absen->waktu_absen
                                    ? \Carbon\Carbon::parse($absen->waktu_absen)->format('H:i')
                                    : \Carbon\Carbon::parse($absen->created_at)->format('H:i') }} WIB
                            </td>
                            <td><strong class="text-dark">{{ $absen->athlete->nama ?? 'Siswa' }}</strong></td>
                            <td><span class="badge bg-warning text-dark text-xs">{{ $absen->athlete->kelompok_umur ?? '-' }}</span></td>
                            <td><span class="badge bg-success">Hadir</span></td>
                        </tr>
                        @empty
                        <tr id="empty-row">
                            <td colspan="4" class="text-center py-4 text-muted">Belum ada siswa yang absen hari ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Muat library html5-qrcode: File lokal langsung --}}
<script src="{{ asset('js/html5-qrcode.min.js') }}"></script>

<script>
    let html5QrCode = null;
    let isProcessing = false;
    let isScannerRunning = false;
    let libraryLoadPromise = null;

    // Helper escape string untuk mencegah XSS saat render detail error
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Helper cek apakah konteks aman (HTTPS atau localhost)
    function isSecureContextOrLocalhost() {
        return window.isSecureContext === true || 
               location.hostname === 'localhost' || 
               location.hostname === '127.0.0.1';
    }

    // Cek dan tampilkan peringatan HTTPS jika dibuka di HTTP online
    function verifyHttpsRequirement() {
        if (!isSecureContextOrLocalhost()) {
            const warningEl = document.getElementById('https-warning');
            if (warningEl) warningEl.classList.remove('d-none');
        }
    }

    // Loader mandiri dengan dukungan Multi-CDN jika file lokal belum selesai diunduh di jaringan lambat
    function ensureLibraryLoaded() {
        if (typeof Html5Qrcode !== 'undefined') {
            return Promise.resolve();
        }
        if (libraryLoadPromise) {
            return libraryLoadPromise;
        }

        libraryLoadPromise = new Promise((resolve, reject) => {
            const cdnUrls = [
                "{{ asset('js/html5-qrcode.min.js') }}",
                "https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js",
                "https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js",
                "https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"
            ];

            let index = 0;
            function tryNextUrl() {
                if (typeof Html5Qrcode !== 'undefined') {
                    return resolve();
                }
                if (index >= cdnUrls.length) {
                    libraryLoadPromise = null;
                    return reject(new Error("File scanner (Html5Qrcode) belum berhasil dimuat. Pastikan file public/js/html5-qrcode.min.js terupload di server hosting atau periksa koneksi internet Anda."));
                }

                const url = cdnUrls[index++];
                const s = document.createElement('script');
                s.src = url;
                s.async = true;
                s.onload = () => {
                    if (typeof Html5Qrcode !== 'undefined') {
                        resolve();
                    } else {
                        tryNextUrl();
                    }
                };
                s.onerror = () => {
                    tryNextUrl();
                };
                document.head.appendChild(s);
            }

            tryNextUrl();
        });

        return libraryLoadPromise;
    }

    // Helper update tampilan status
    function updateStatus(type, messageHtml) {
        const statusBox = document.getElementById('scan-status');
        if (!statusBox) return;

        let alertClass = 'alert-secondary';
        if (type === 'success') alertClass = 'alert-success';
        if (type === 'warning') alertClass = 'alert-warning';
        if (type === 'danger') alertClass = 'alert-danger';

        statusBox.className = `alert ${alertClass} py-2 px-3 small mb-0 d-inline-block text-start border-0 shadow-sm w-100`;
        statusBox.innerHTML = messageHtml;
    }

    // Helper update status tombol
    function updateButtonStates(running) {
        isScannerRunning = running;
        const btnStart = document.getElementById('btn-start');
        const btnStop = document.getElementById('btn-stop');

        if (btnStart) {
            if (running) {
                btnStart.classList.remove('btn-success');
                btnStart.classList.add('btn-outline-success');
                btnStart.innerHTML = '<i class="bi bi-camera-video-fill me-1"></i> Kamera Aktif';
            } else {
                btnStart.classList.remove('btn-outline-success');
                btnStart.classList.add('btn-success');
                btnStart.innerHTML = '<i class="bi bi-play-circle-fill me-1"></i> Mulai Kamera';
            }
        }

        if (btnStop) {
            btnStop.disabled = !running;
        }
    }

    // Helper tampilan placeholder ketika kamera mati
    function showPlaceholder() {
        const reader = document.getElementById('reader');
        if (!reader) return;
        reader.innerHTML = `
            <div id="reader-placeholder" class="text-center p-4 text-white-50">
                <i class="bi bi-camera-video-off fs-1 d-block mb-2 opacity-50"></i>
                <h6 class="fw-bold text-white mb-1">Kamera Dihentikan</h6>
                <small class="d-block mb-3">Klik tombol <strong>"Mulai Kamera"</strong> untuk mengaktifkan kembali</small>
                <button type="button" class="btn btn-sm btn-success px-3 fw-bold" onclick="startScanner()">
                    <i class="bi bi-play-circle-fill me-1"></i> Mulai Kamera
                </button>
            </div>
        `;
    }

    // ─── 1. Hentikan Scanner Secara Aman ──────────────────────────────────────
    async function stopScannerSafe() {
        if (html5QrCode) {
            try {
                const state = typeof html5QrCode.getState === 'function' ? html5QrCode.getState() : 0;
                // Html5QrcodeScannerState: SCANNING = 2, PAUSED = 3
                if (state === 2 || state === 3) {
                    await html5QrCode.stop();
                }
            } catch (e) {
                console.warn("Stop scanner error:", e);
            }
            try {
                await html5QrCode.clear();
            } catch (e) {}
            html5QrCode = null;
        }
        updateButtonStates(false);
    }

    async function stopScanner() {
        await stopScannerSafe();
        updateStatus('secondary', '<i class="bi bi-stop-circle me-1"></i> Kamera dihentikan. Klik tombol di atas untuk mengaktifkan kembali.');
        showPlaceholder();
    }

    // ─── 2. Deteksi & Muat Daftar Kamera ke Dropdown ──────────────────────────
    async function loadCameraDevices(autoSelectId = null) {
        if (typeof Html5Qrcode === 'undefined') return [];

        try {
            const devices = await Html5Qrcode.getCameras();
            if (devices && devices.length > 0) {
                const select = document.getElementById('camera-select');
                const wrapper = document.getElementById('camera-select-wrapper');
                if (select) {
                    select.innerHTML = '';
                    devices.forEach((dev, index) => {
                        const opt = document.createElement('option');
                        opt.value = dev.id;
                        opt.textContent = dev.label ? dev.label : `Kamera ${index + 1}`;
                        if (autoSelectId && dev.id === autoSelectId) {
                            opt.selected = true;
                        }
                        select.appendChild(opt);
                    });
                    if (wrapper && devices.length > 1) {
                        wrapper.classList.remove('d-none');
                    }
                }
                return devices;
            }
        } catch (e) {
            console.warn("Enumerasi kamera sebelum izin akses diberikan:", e);
        }
        return [];
    }

    // ─── 3. Fungsi Utama Memulai Kamera Scanner ──────────────────────────────
    async function startScanner(preferredModeOrId) {
        // Cek dukungan browser
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            if (!isSecureContextOrLocalhost()) {
                updateStatus('danger', '<strong><i class="bi bi-shield-x me-1"></i>Koneksi HTTP Tidak Aman:</strong> Browser memblokir kamera karena website belum menggunakan HTTPS. Silakan pasang SSL di FastPanel atau buka via <code>https://</code>.');
                verifyHttpsRequirement();
            } else {
                updateStatus('danger', '<strong><i class="bi bi-exclamation-triangle me-1"></i>Tidak Didukung:</strong> Browser ini tidak mendukung akses kamera web (WebRTC getUserMedia).');
            }
            return;
        }

        // Hentikan dan bersihkan instance lama
        await stopScannerSafe();

        updateStatus('warning', '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyiapkan modul pemindai kamera...');

        // Tunggu library selesai diunduh jika koneksi lambat
        try {
            await ensureLibraryLoaded();
        } catch (libErr) {
            updateStatus('danger', `
                <div class="mb-1">❌ <strong>Modul Scanner Belum Siap:</strong> ${escapeHtml(libErr.message)}</div>
                <div class="small text-muted mt-1">Silakan coba klik tombol <strong>"Mulai Kamera"</strong> lagi atau gunakan tombol <strong>"Ambil Foto ID Card"</strong> di bawah.</div>
            `);
            return;
        }

        updateStatus('warning', '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Membuka kamera... Mohon pilih <strong>"Allow" / "Izinkan"</strong> jika browser meminta izin.');

        const readerEl = document.getElementById('reader');
        if (readerEl) readerEl.innerHTML = '';

        try {
            // Ambil daftar kamera yang tersedia terlebih dahulu jika ada
            const devices = await loadCameraDevices();

            // Tentukan target kamera yang paling kompatibel
            let cameraTarget = null;
            const selectEl = document.getElementById('camera-select');
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

            if (preferredModeOrId && preferredModeOrId !== 'environment' && preferredModeOrId !== 'user') {
                // Dipilih dari dropdown kamera (deviceId)
                cameraTarget = preferredModeOrId;
            } else if (preferredModeOrId === 'environment') {
                // Prioritas kamera belakang
                if (devices && devices.length > 0) {
                    const backCam = devices.find(d => /back|rear|belakang|environment/i.test(d.label));
                    cameraTarget = backCam ? backCam.id : (devices.length > 1 ? devices[devices.length - 1].id : devices[0].id);
                } else {
                    cameraTarget = { facingMode: "environment" };
                }
            } else if (preferredModeOrId === 'user') {
                // Prioritas kamera depan / laptop
                if (devices && devices.length > 0) {
                    const frontCam = devices.find(d => /front|user|depan|webcam|integrated/i.test(d.label));
                    cameraTarget = frontCam ? frontCam.id : devices[0].id;
                } else {
                    cameraTarget = { facingMode: "user" };
                }
            } else {
                // Default klik "Mulai Kamera"
                if (selectEl && selectEl.value) {
                    cameraTarget = selectEl.value;
                } else if (devices && devices.length > 0) {
                    // Jika di HP, gunakan kamera belakang jika ada. Jika di laptop/PC, gunakan kamera pertama
                    if (isMobile) {
                        const backCam = devices.find(d => /back|rear|belakang|environment/i.test(d.label));
                        cameraTarget = backCam ? backCam.id : devices[0].id;
                    } else {
                        cameraTarget = devices[0].id;
                    }
                } else {
                    // Fallback jika enumerasi kamera awal belum memiliki izin
                    cameraTarget = isMobile ? { facingMode: "environment" } : { facingMode: "user" };
                }
            }

            const config = {
                fps: 12,
                qrbox: function(viewfinderWidth, viewfinderHeight) {
                    const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                    const size = Math.floor(minEdge * 0.72);
                    return {
                        width: Math.max(160, Math.min(size, 270)),
                        height: Math.max(160, Math.min(size, 270))
                    };
                },
                aspectRatio: 1.3333
            };

            // Inisialisasi instance scanner baru
            html5QrCode = new Html5Qrcode("reader");

            try {
                await html5QrCode.start(cameraTarget, config, onScanSuccess, onScanFailure);
            } catch (firstErr) {
                console.warn("Percobaan kamera pertama gagal, mencoba fallback aman:", firstErr);

                // Buat instance bersih baru untuk fallback
                await stopScannerSafe();
                html5QrCode = new Html5Qrcode("reader");

                // Jika target awal adalah objek facingMode, coba deviceId nyata atau sebaliknya
                const refreshedDevices = await loadCameraDevices();
                if (refreshedDevices && refreshedDevices.length > 0) {
                    await html5QrCode.start(refreshedDevices[0].id, config, onScanSuccess, onScanFailure);
                } else {
                    // Coba mode 'user' yang didukung hampir semua webcam
                    await html5QrCode.start({ facingMode: "user" }, config, onScanSuccess, onScanFailure);
                }
            }

            updateButtonStates(true);
            updateStatus('success', '<i class="bi bi-check-circle-fill me-1 text-success"></i> <strong>Kamera Aktif!</strong> Arahkan Barcode atau QR Code ke dalam kotak scanner.');

            // Refresh nama-nama kamera setelah izin webcam diberikan
            loadCameraDevices();

        } catch (err) {
            console.error("Gagal membuka kamera scanner:", err);
            await stopScannerSafe();
            showPlaceholder();

            const rawError = String(err && (err.message || err));
            const errStr = rawError.toLowerCase();

            let msg = 'Terjadi kendala saat mengakses perangkat kamera.';

            if (errStr.includes('notallowed') || errStr.includes('permission')) {
                msg = '🔒 <strong>Izin Kamera Ditolak / Diblokir:</strong><br>Silakan klik ikon gembok 🔒 di sebelah kiri address bar browser Anda, ubah izin Kamera menjadi <strong>"Allow" / "Izinkan"</strong>, lalu klik Mulai Kamera kembali.';
            } else if (errStr.includes('notfound') || errStr.includes('devicesnotfound')) {
                msg = '📷 <strong>Kamera Tidak Ditemukan:</strong><br>Tidak ada webcam atau kamera yang terdeteksi di perangkat ini. Anda dapat menggunakan tombol <em>"Ambil Foto ID Card"</em> di bawah.';
            } else if (errStr.includes('notreadable') || errStr.includes('trackstart') || errStr.includes('could not start')) {
                msg = '⚠️ <strong>Kamera Sedang Digunakan:</strong><br>Kamera sedang dipakai oleh aplikasi lain (Zoom, Google Meet, WhatsApp, atau tab browser lain). Tutup aplikasi tersebut lalu coba lagi.';
            } else if (errStr.includes('overconstrained')) {
                msg = '⚙️ <strong>Mode Kamera Tidak Sesuai:</strong><br>Kamera tidak mendukung resolusi atau mode ini. Coba klik tombol <strong>"Kamera Depan / Laptop"</strong>.';
            } else if (!isSecureContextOrLocalhost()) {
                msg = '🛡️ <strong>Koneksi HTTP Tidak Aman:</strong><br>Browser memblokir kamera karena website belum menggunakan HTTPS. Aktifkan Let\'s Encrypt SSL di FastPanel Anda.';
                verifyHttpsRequirement();
            }

            updateStatus('danger', `
                <div class="mb-1">❌ <strong>Gagal membuka kamera:</strong> ${msg}</div>
                <div class="mt-2 p-2 bg-white rounded border border-danger-subtle text-danger font-monospace" style="font-size: 11px; word-break: break-all;">
                    <strong>Info Teknis:</strong> ${escapeHtml(rawError)}
                </div>
            `);
        }
    }

    // ─── 4. Callback Ketika Barcode / QR Berhasil Terbaca ────────────────────
    function onScanSuccess(decodedText, decodedResult) {
        if (isProcessing) return;
        isProcessing = true;

        playBeepSound();
        submitBarcode(decodedText);

        // Jeda 3 detik sebelum dapat membaca barcode berikutnya
        setTimeout(() => {
            isProcessing = false;
        }, 3000);
    }

    function onScanFailure(error) {
        // Abaikan frame kosong saat video berjalan mencari QR
    }

    // ─── 5. Scan dari File Foto / Kamera Native HP ───────────────────────────
    async function handleFileInput(e) {
        const file = e.target.files && e.target.files[0];
        if (!file) return;

        const resultBox = document.getElementById('result-box');
        resultBox.style.background = '#e2e8f0';
        resultBox.innerHTML = `
            <div class="spinner-border text-primary" role="status"></div>
            <h6 class="mt-2 fw-bold text-dark">Membaca Barcode / QR dari gambar...</h6>
        `;

        try {
            await stopScannerSafe();
            showPlaceholder();

            // Tunggu library dimuat jika belum selesai
            await ensureLibraryLoaded();

            const tempScanner = new Html5Qrcode("reader");
            const decodedText = await tempScanner.scanFile(file, false);
            playBeepSound();
            submitBarcode(decodedText);
            try { await tempScanner.clear(); } catch(err) {}
        } catch (err) {
            resultBox.style.background = '#fee2e2';
            resultBox.innerHTML = `
                <div class="text-danger">
                    <i class="bi bi-x-circle-fill fs-1"></i>
                    <h6 class="fw-bold mt-2 mb-1">Tidak dapat menemukan Barcode / QR pada gambar</h6>
                    <small class="text-muted">Pastikan foto tidak buram, pencahayaan cukup, dan kode QR tampak jelas.</small>
                </div>
            `;
            console.warn("Scan file error:", err);
        } finally {
            e.target.value = '';
        }
    }

    // ─── 6. Submit Manual (via Form Input USB Gun / Ketik) ───────────────────
    function handleManualSubmit(e) {
        e.preventDefault();
        const input = document.getElementById('manual_barcode');
        const val = input.value.trim();
        if (val !== '') {
            submitBarcode(val);
            input.value = '';
        }
    }

    // ─── 7. Kirim AJAX Absensi ke Backend Laravel ────────────────────────────
    function submitBarcode(barcode) {
        const resultBox = document.getElementById('result-box');
        resultBox.style.background = '#e2e8f0';
        resultBox.innerHTML = `
            <div class="spinner-border text-primary" role="status"></div>
            <h6 class="mt-2 fw-bold text-dark">Memverifikasi Barcode <span class="badge bg-secondary font-monospace">${escapeHtml(barcode)}</span>...</h6>
        `;

        fetch("{{ route('admin.attendances.scan-store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ kode_barcode: barcode })
        })
        .then(response => {
            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                throw new Error("Server mengembalikan respons non-JSON (kemungkinan error 500 atau session login telah berakhir).");
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                resultBox.style.background = '#dcfce7';
                resultBox.innerHTML = `
                    <div class="text-success">
                        <i class="bi bi-check-circle-fill fs-1"></i>
                        <h5 class="fw-bold mt-1 mb-1">${escapeHtml(data.athlete.nama)}</h5>
                        <span class="badge bg-success mb-2">${escapeHtml(data.athlete.kelompok_umur ?? 'U-?')} | ${escapeHtml(data.athlete.posisi_bermain ?? '-')}</span>
                        <p class="small text-dark mb-0 fw-bold">✅ Berhasil Absen Hadir pukul ${escapeHtml(data.time)} WIB</p>
                    </div>
                `;
                addToTableLog(data.time, data.athlete.nama, data.athlete.kelompok_umur);

            } else if (data.status === 'warning') {
                resultBox.style.background = '#fef9c3';
                resultBox.innerHTML = `
                    <div class="text-warning text-dark">
                        <i class="bi bi-exclamation-triangle-fill fs-1 text-warning"></i>
                        <h5 class="fw-bold mt-1 mb-1">${escapeHtml(data.athlete.nama)}</h5>
                        <p class="small text-dark mb-0 fw-bold">⚠️ ${escapeHtml(data.message)}</p>
                    </div>
                `;
            } else {
                resultBox.style.background = '#fee2e2';
                resultBox.innerHTML = `
                    <div class="text-danger">
                        <i class="bi bi-x-circle-fill fs-1"></i>
                        <h6 class="fw-bold mt-2 mb-0">${escapeHtml(data.message ?? 'Barcode tidak terdaftar.')}</h6>
                    </div>
                `;
            }
        })
        .catch(error => {
            resultBox.style.background = '#fee2e2';
            resultBox.innerHTML = `
                <div class="text-danger">
                    <i class="bi bi-exclamation-octagon-fill fs-2 mb-1"></i>
                    <h6 class="fw-bold mb-0">Terjadi kesalahan sistem / jaringan</h6>
                    <small class="text-muted">${escapeHtml(error.message)}</small>
                </div>
            `;
            console.error("Scan submission error:", error);
        });
    }

    // ─── 8. Tambahkan Baris Baru ke Log Tabel Hari Ini ───────────────────────
    function addToTableLog(time, nama, ku) {
        const tbody = document.getElementById('log-table-body');
        if (!tbody) return;

        const emptyRow = document.getElementById('empty-row');
        if (emptyRow) emptyRow.remove();

        const tr = document.createElement('tr');
        tr.className = "table-success";
        tr.innerHTML = `
            <td class="fw-bold text-success">${escapeHtml(time)} WIB</td>
            <td><strong class="text-dark">${escapeHtml(nama)}</strong></td>
            <td><span class="badge bg-warning text-dark text-xs">${escapeHtml(ku ?? '-')}</span></td>
            <td><span class="badge bg-success">Hadir</span></td>
        `;
        tbody.insertBefore(tr, tbody.firstChild);
        setTimeout(() => { tr.classList.remove('table-success'); }, 2500);
    }

    // ─── 9. Generator Nada Suara Beep ────────────────────────────────────────
    function playBeepSound() {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();
            if (ctx.state === 'suspended') {
                ctx.resume();
            }
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(1046.5, ctx.currentTime);
            gain.gain.setValueAtTime(0.3, ctx.currentTime);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.12);
        } catch (e) {
            // Audio context policy
        }
    }

    // Inisialisasi saat DOM siap
    document.addEventListener('DOMContentLoaded', function () {
        verifyHttpsRequirement();
        // Pre-load library di latar belakang
        ensureLibraryLoaded().then(() => {
            loadCameraDevices();
        }).catch(e => {
            console.warn("Preload library warning:", e);
        });
    });
</script>
@endsection
