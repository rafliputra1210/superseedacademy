@extends('layouts.admin')
@section('title', 'Kamera Scanner Absensi')

@section('content')
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card card-custom bg-white p-4 shadow-sm border-0 text-center">
            <h5 class="font-weight-bold text-dark mb-1"><i class="bi bi-qr-code-scan text-success me-2"></i>Kamera Scan Barcode Atlet</h5>
            <p class="text-muted small mb-3">Arahkan ID Card atau Barcode ke depan kamera HP/Laptop.</p>

            <div class="mb-3 d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-success font-weight-bold" onclick="startScanner('environment')">
                    <i class="bi bi-camera-fill me-1"></i> Kamera Belakang (HP)
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold" onclick="startScanner('user')">
                    <i class="bi bi-person-video me-1"></i> Kamera Depan / Laptop
                </button>
                <button type="button" class="btn btn-sm btn-danger font-weight-bold" onclick="stopScanner()">
                    <i class="bi bi-stop-circle me-1"></i> Stop
                </button>
            </div>

            <div id="reader" class="mx-auto rounded border border-success border-3 shadow-sm" style="width: 100%; max-width: 400px; min-height: 280px; background: #f8fafc;"></div>
            
            <div class="mt-3">
                <span id="scan-status" class="badge bg-secondary px-3 py-2 fs-6">⏸️ Kamera Belum Aktif... Klik tombol di atas!</span>
            </div>

            <div class="mt-4 pt-3 border-top text-start">
                <label class="form-label small font-weight-bold text-muted">Atau Ketik / Scan USB Manual:</label>
                <form id="manualForm" onsubmit="handleManualSubmit(event)">
                    <div class="input-group">
                        <input type="text" id="manual_barcode" class="form-control" placeholder="Contoh: SSA-2026-1234" autocomplete="off">
                        <button type="submit" class="btn btn-success font-weight-bold">Input</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div id="result-box" class="card card-custom p-4 mb-4 text-center border-0 shadow-sm" style="background: #f1f5f9; min-height: 140px; display: flex; align-items: center; justify-content: center;">
            <div>
                <i class="bi bi-upc-scan fs-1 text-muted d-block mb-2"></i>
                <h6 class="font-weight-bold text-muted mb-0">Belum ada barcode yang di-scan</h6>
                <small class="text-muted">Hasil identitas atlet akan muncul di sini</small>
            </div>
        </div>

        <div class="card card-custom bg-white p-4 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <h6 class="font-weight-bold text-dark mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Absensi Latihan Hari Ini</h6>
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
                            <td class="font-weight-bold text-success">{{ \Carbon\Carbon::parse($absen->waktu_absen)->format('H:i') }} WIB</td>
                            <td><strong class="text-dark">{{ $absen->athlete->nama ?? 'Siswa' }}</strong></td>
                            <td><span class="badge bg-warning text-dark text-xs">{{ $absen->athlete->kelompok_umur ?? '-' }}</span></td>
                            <td><span class="badge bg-success">Hadir</span></td>
                        </tr>
                        @empty
                        <tr id="empty-row"><td colspan="4" class="text-center py-4 text-muted">Belum ada siswa yang absen hari ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    let html5QrcodeScanner = null;
    let isProcessing = false; // Mencegah scan dobel dalam hitungan milidetik

    // 1. Memulai Kamera
    function startScanner(cameraType) {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear();
        }

        const statusBadge = document.getElementById('scan-status');
        statusBadge.className = 'badge bg-warning text-dark px-3 py-2 fs-6';
        statusBadge.innerHTML = '🔄 Membuka Kamera...';

        html5QrcodeScanner = new Html5Qrcode("reader");

        const config = { 
            fps: 10, 
            qrbox: { width: 250, height: 150 },
            aspectRatio: 1.3333 
        };

        html5QrcodeScanner.start(
            { facingMode: cameraType }, 
            config, 
            onScanSuccess, 
            onScanFailure
        ).then(() => {
            statusBadge.className = 'badge bg-success px-3 py-2 fs-6';
            statusBadge.innerHTML = '📸 Kamera Aktif! Arahkan ID Card ke kotak...';
        }).catch(err => {
            statusBadge.className = 'badge bg-danger px-3 py-2 fs-6';
            statusBadge.innerHTML = '❌ Gagal membuka kamera. Pastikan izin kamera diaktifkan di browser!';
            console.error("Error start camera", err);
        });
    }

    // 2. Menghentikan Kamera
    function stopScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().then(() => {
                html5QrcodeScanner.clear();
                const statusBadge = document.getElementById('scan-status');
                statusBadge.className = 'badge bg-secondary px-3 py-2 fs-6';
                statusBadge.innerHTML = '⏹️ Kamera Dihentikan';
            }).catch(err => console.error(err));
        }
    }

    // 3. Ketika Barcode Berhasil Terbaca oleh Kamera
    function onScanSuccess(decodedText, decodedResult) {
        if (isProcessing) return; // Abaikan jika sedang memproses scan sebelumnya
        isProcessing = true;

        // Bunyikan suara Beep!
        playBeepSound();

        // Kirim ke server via AJAX
        submitBarcode(decodedText);

        // Beri jeda 3 detik sebelum bisa scan barcode berikutnya
        setTimeout(() => {
            isProcessing = false;
        }, 3000);
    }

    function onScanFailure(error) {
        // Abaikan error pembacaan frame kosong
    }

    // 4. Submit Manual (via Form Input USB/Ketik)
    function handleManualSubmit(e) {
        e.preventDefault();
        const input = document.getElementById('manual_barcode');
        if (input.value.trim() !== '') {
            submitBarcode(input.value.trim());
            input.value = '';
        }
    }

    // 5. Fungsi Utama Kirim AJAX ke Backend Laravel
    function submitBarcode(barcode) {
        const resultBox = document.getElementById('result-box');
        resultBox.style.background = '#e2e8f0';
        resultBox.innerHTML = `<div class="spinner-border text-primary" role="status"></div><h6 class="mt-2 font-weight-bold">Memverifikasi Barcode ${barcode}...</h6>`;

        fetch("{{ route('admin.attendances.scan-store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ kode_barcode: barcode })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Background Hijau Sukses
                resultBox.style.background = '#dcfce7';
                resultBox.innerHTML = `
                    <div class="text-success">
                        <i class="bi bi-check-circle-fill fs-1"></i>
                        <h5 class="font-weight-bold mt-1 mb-1">${data.athlete.nama}</h5>
                        <span class="badge bg-success mb-2">${data.athlete.kelompok_umur ?? 'U-12'} | ${data.athlete.posisi_bermain ?? '-'}</span>
                        <p class="small text-dark mb-0 font-weight-bold">✅ Berhasil Absen Hadir pukul ${data.time} WIB</p>
                    </div>
                `;
                // Tambahkan langsung ke baris tabel teratas
                addToTableLog(data.time, data.athlete.nama, data.athlete.kelompok_umur);
                
            } else if (data.status === 'warning') {
                // Background Kuning Peringatan (Sudah Absen)
                resultBox.style.background = '#fef9c3';
                resultBox.innerHTML = `
                    <div class="text-warning text-dark">
                        <i class="bi bi-exclamation-triangle-fill fs-1 text-warning"></i>
                        <h5 class="font-weight-bold mt-1 mb-1">${data.athlete.nama}</h5>
                        <p class="small text-dark mb-0 font-weight-bold">⚠️ Sudah melakukan absensi hari ini!</p>
                    </div>
                `;
            } else {
                // Background Merah Gagal
                resultBox.style.background = '#fee2e2';
                resultBox.innerHTML = `
                    <div class="text-danger">
                        <i class="bi bi-x-circle-fill fs-1"></i>
                        <h6 class="font-weight-bold mt-2 mb-0">${data.message}</h6>
                    </div>
                `;
            }
        })
        .catch(error => {
            resultBox.style.background = '#fee2e2';
            resultBox.innerHTML = `<h6 class="text-danger font-weight-bold">❌ Terjadi kesalahan jaringan / sistem!</h6>`;
            console.error("Error:", error);
        });
    }

    // 6. Menambahkan Baris Baru ke Tabel Log Tanpa Refresh Halaman
    function addToTableLog(time, nama, ku) {
        const tbody = document.getElementById('log-table-body');
        const emptyRow = document.getElementById('empty-row');
        if (emptyRow) emptyRow.remove();

        const tr = document.createElement('tr');
        tr.className = "table-success transition"; // Efek warna hijau sejenak
        tr.innerHTML = `
            <td class="font-weight-bold text-success">${time} WIB</td>
            <td><strong class="text-dark">${nama}</strong></td>
            <td><span class="badge bg-warning text-dark text-xs">${ku ?? '-'}</span></td>
            <td><span class="badge bg-success">Hadir</span></td>
        `;
        tbody.insertBefore(tr, tbody.firstChild);

        setTimeout(() => { tr.classList.remove('table-success'); }, 2000);
    }

    // 7. Generator Suara Beep
    function playBeepSound() {
        const context = new (window.AudioContext || window.webkitAudioContext)();
        const osc = context.createOscillator();
        const gain = context.createGain();
        osc.type = 'sine';
        osc.frequency.value = 1000; // Frekuensi suara BEEP (Hz)
        gain.gain.setValueAtTime(0.5, context.currentTime);
        osc.connect(gain);
        gain.connect(context.destination);
        osc.start();
        osc.stop(context.currentTime + 0.15); // Durasi 150 milidetik
    }

    // Otomatis aktifkan kamera depan saat halaman dimuat
    window.onload = function() {
        startScanner('user');
    };
</script>
@endsection