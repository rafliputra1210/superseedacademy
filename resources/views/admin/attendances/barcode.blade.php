<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Bukti Absensi - {{ $attendance->kode_barcode }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; font-family: 'Courier New', Courier, monospace; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .barcode-card { width: 380px; background: #fff; border: 2px dashed #333; border-radius: 12px; padding: 24px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .academy-title { font-family: 'Arial', sans-serif; font-weight: 900; font-size: 1.4rem; color: #064e3b; letter-spacing: 1px; margin-bottom: 2px; }
        .academy-sub { font-family: 'Arial', sans-serif; font-size: 0.75rem; color: #666; text-transform: uppercase; margin-bottom: 15px; border-bottom: 2px solid #064e3b; padding-bottom: 8px; }
        .student-photo { width: 110px; height: 110px; object-fit: cover; border-radius: 50%; border: 3px solid #f59e0b; margin: 0 auto 12px; display: block; background: #e2e8f0; }
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .barcode-card { box-shadow: none; border: 2px solid #000; }
        }
    </style>
</head>
<body>

    <div>
        <div class="text-center mb-3 no-print">
            <button onclick="window.print()" class="btn btn-warning font-weight-bold px-4 shadow"><i class="bi bi-printer-fill"></i> Cetak Bukti Absensi</button>
            <button onclick="window.close()" class="btn btn-secondary px-3">Tutup</button>
        </div>

        <div class="barcode-card">
            <div class="academy-title">⚽ SUPERSEED ACADEMY</div>
            <div class="academy-sub">Football Training & Development Center</div>

            @if($attendance->foto_bukti)
                <img src="{{ storage_img_url($attendance->foto_bukti) }}" class="student-photo" alt="Foto Absensi">
            @elseif($attendance->athlete->foto)
                <img src="{{ storage_img_url($attendance->athlete->foto) }}" class="student-photo" alt="Foto Siswa">
            @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode($attendance->athlete->nama ?? 'Siswa') }}&background=0D8ABC&color=fff&size=128" class="student-photo" alt="Avatar">
            @endif

            <h5 class="font-weight-bold mb-0" style="font-family: Arial, sans-serif;">{{ $attendance->athlete->nama ?? 'Nama Atlet' }}</h5>
            <p class="text-muted small mb-3" style="font-family: Arial, sans-serif;">
                Posisi: {{ $attendance->athlete->posisi_bermain ?? 'Siswa' }} | Tanggal: <strong>{{ \Carbon\Carbon::parse($attendance->tanggal)->format('d M Y') }}</strong>
            </p>

            <div class="my-3 p-2 bg-light rounded border">
                <img src="https://barcode.tec-it.com/barcode.ashx?data={{ $attendance->kode_barcode }}&code=Code128&translate-esc=on" alt="Barcode {{ $attendance->kode_barcode }}" style="max-width: 100%; height: 65px;">
            </div>

            <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 0.7rem; font-family: Arial, sans-serif;">
                <span>Status: <strong class="text-uppercase text-success">{{ $attendance->status }}</strong></span>
                <span>ID: {{ $attendance->kode_barcode }}</span>
            </div>
        </div>
    </div>

</body>
</html>