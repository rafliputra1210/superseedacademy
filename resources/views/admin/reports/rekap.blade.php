@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="font-weight-bold mb-1">Rekapitulasi Nilai Asesor & Raport</h3>
            <p class="text-muted small mb-0">Pantau status penilaian tiap asesor serta hasil rekap dan observasi lapangan final atlet.</p>
        </div>
        <span class="badge bg-primary text-white px-3 py-2" style="font-size: 0.95rem;">
            <i class="bi bi-calendar3 me-1"></i> Periode: {{ $periodeSekarang }}
        </span>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 1. TABEL STATUS PENILAIAN PER ASESOR --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="bi bi-table text-primary me-2"></i>Status Penilaian Asesor per Atlet
            </h6>
            <span class="small text-muted">Total Atlet: {{ $athletes->count() }}</span>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle text-center mb-0">
                <thead class="bg-dark text-white">
                    <tr>
                        <th rowspan="2" class="align-middle" style="width: 50px;">No</th>
                        <th rowspan="2" class="align-middle text-start">Nama Atlet</th>
                        <th colspan="{{ max(1, $assessors->count()) }}">Status Penilaian Asesor</th>
                        <th rowspan="2" class="align-middle" style="width: 170px;">Aksi Raport</th>
                    </tr>
                    <tr>
                        @forelse($assessors as $asesor)
                            <th>{{ $asesor->name }}</th>
                        @empty
                            <th>Belum ada asesor</th>
                        @endforelse
                    </tr>
                </thead>
                <tbody>
                    @foreach($athletes as $index => $atlet)
                        @php
                            $nilaiAtletIni = $allScores->where('athlete_id', $atlet->id);
                            $jumlahDinilai = $nilaiAtletIni->count();
                            // Menggunakan koleksi $raports yang sudah di-load di controller (mencegah N+1 query)
                            $raportSelesai = $raports->where('athlete_id', $atlet->id)->first();
                        @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-start font-weight-bold">
                            <div>{{ $atlet->nama }}</div>
                            <small class="text-muted">NIS: {{ $atlet->nomor_induk ?? $atlet->kode_barcode ?? '-' }} | KU-{{ $atlet->kelompok_umur ?? '-' }}</small>
                        </td>
                        
                        <!-- Pengecekan per Asesor -->
                        @forelse($assessors as $asesor)
                            @php
                                $sudahDinilai = $nilaiAtletIni->where('assessor_id', $asesor->id)->first();
                            @endphp
                            <td>
                                @if($sudahDinilai)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i> Selesai
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        <i class="bi bi-x-circle me-1"></i> Belum
                                    </span>
                                @endif
                            </td>
                        @empty
                            <td class="text-muted small">-</td>
                        @endforelse

                        <!-- Tombol Aksi Raport -->
                        <td>
                            @if($raportSelesai)
                                <a href="{{ route('admin.reports.print', $raportSelesai->id) }}" target="_blank" class="btn btn-sm btn-primary w-100 mb-1 fw-semibold">
                                    <i class="bi bi-printer me-1"></i> Cetak Raport
                                </a>
                                <form action="{{ route('admin.reports.generate-final', $atlet->id) }}" method="POST" class="form-confirm-action"
                                      data-title="Perbarui Akumulasi Raport?"
                                      data-html="Hitung ulang akumulasi nilai raport <b>{{ $atlet->nama }}</b> dari penilaian asesor yang tersedia?"
                                      data-icon="question"
                                      data-btn-text="<i class='bi bi-arrow-repeat me-1'></i> Ya, Perbarui"
                                      data-btn-class="btn-primary">
                                    @csrf
                                    <input type="hidden" name="periode_bulan" value="{{ $periodeSekarang }}">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary w-100 py-0" style="font-size: 0.75rem;" title="Perbarui ulang raport">
                                        <i class="bi bi-arrow-repeat"></i> Update Raport
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.reports.generate-final', $atlet->id) }}" method="POST" class="form-confirm-action"
                                      data-title="Generate Nilai Final Raport?"
                                      data-html="Generate nilai final untuk <b>{{ $atlet->nama }}</b> dari <b>{{ $jumlahDinilai }} asesor</b> yang sudah masuk?"
                                      data-icon="info"
                                      data-btn-text="<i class='bi bi-cpu me-1'></i> Ya, Generate"
                                      data-btn-class="btn-success">
                                    @csrf
                                    <input type="hidden" name="periode_bulan" value="{{ $periodeSekarang }}">
                                    <button type="submit" class="btn btn-sm {{ $jumlahDinilai > 0 ? 'btn-success' : 'btn-secondary' }} w-100" 
                                        {{ $jumlahDinilai == 0 ? 'disabled' : '' }}>
                                        <i class="bi bi-cpu me-1"></i> Generate
                                    </button>
                                </form>
                                <small class="d-block mt-1 text-muted" style="font-size: 0.75rem;">
                                    {{ $jumlahDinilai }}/{{ $assessors->count() }} Asesor dinilai
                                </small>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ==========================================================
         2. HASIL RAPORT ATLET (BAGIAN E & F DI BAWAH RAPORT)
         ========================================================== --}}
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-journal-check text-primary me-2"></i>Hasil Rekapitulasi & Observasi Raport (Bagian E & F)
            </h5>
            <span class="text-muted small">Periode: <strong>{{ $periodeSekarang }}</strong></span>
        </div>

        @php
            // Memanfaatkan koleksi $raports yang sudah di-eager-load bersama relasi athlete dari controller
            $daftarRaport = $raports;
        @endphp

        @if($daftarRaport->isEmpty())
            <div class="card border-0 shadow-sm p-4 text-center">
                <div class="text-muted py-4">
                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                    <p class="mb-1 fw-bold">Belum ada Raport yang di-generate pada periode ini.</p>
                    <small>Klik tombol <strong>Generate</strong> pada tabel di atas untuk memproses nilai asesor menjadi raport final dan observasi kualitatif.</small>
                </div>
            </div>
        @else
            <div class="row g-4">
                @foreach($daftarRaport as $rap)
                    @php
                        $obs = $rap->observasi_kualitatif;
                        if (is_string($obs)) {
                            $obs = json_decode($obs, true);
                        }
                        $skor100 = $rap->rekap_skor_100 ?? 0;
                        $skorRata = $rap->rekap_rata_rata ?? 0;

                        // Fallback jika belum pernah re-generate
                        if ($skor100 <= 0 && !empty($rap->scores_summary) && is_array($rap->scores_summary)) {
                            $tmpTotal = 0; $tmpCnt = 0;
                            foreach ($rap->scores_summary as $c) {
                                if (!empty($c['nilai']) && (float)$c['nilai'] > 0) {
                                    $tmpTotal += (float)$c['nilai'];
                                    $tmpCnt++;
                                }
                            }
                            if ($tmpCnt > 0) {
                                $skorRata = round($tmpTotal / $tmpCnt, 2);
                                $skor100  = round(($skorRata / 5) * 100);
                            }
                        }
                    @endphp
                    <div class="col-12">
                        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                            <!-- Card Header Info Atlet -->
                            <div class="card-header bg-dark text-white py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 42px; height: 42px; font-size: 1.1rem;">
                                        {{ strtoupper(substr($rap->athlete->nama ?? 'A', 0, 1)) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold fs-6">{{ $rap->athlete->nama ?? '-' }}</h6>
                                        <small class="text-light opacity-75">
                                            NIS: {{ $rap->athlete->nomor_induk ?? $rap->athlete->kode_barcode ?? '-' }} &bull; 
                                            KU: {{ $rap->athlete->kelompok_umur ? 'KU-' . $rap->athlete->kelompok_umur : '-' }} &bull; 
                                            Kelompok Latihan: {{ $rap->athlete->kelompok_latihan ?? '-' }}
                                        </small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('admin.reports.print', $rap->id) }}" target="_blank" class="btn btn-sm btn-primary fw-bold px-3">
                                        <i class="bi bi-printer me-1"></i> Cetak / Unduh PDF
                                    </a>
                                </div>
                            </div>

                            <div class="card-body p-4 bg-white">
                                <div class="row g-4">
                                    {{-- BAGIAN E: OBSERVASI LAPANGAN (KUALITATIF) --}}
                                    <div class="col-lg-7">
                                        <div class="border rounded-3 p-3 h-100 bg-light-subtle">
                                            <h6 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                                                <i class="bi bi-eye-fill text-primary"></i> E. Observasi Lapangan (Kualitatif)
                                            </h6>
                                            <p class="text-muted small mb-3 fst-italic">Catat hasil pengamatan selama latihan atau pertandingan, termasuk situasi bola mati.</p>
                                            
                                            <div class="table-responsive">
                                                <table class="table table-bordered bg-white mb-0" style="font-size: 0.85rem;">
                                                    <tbody>
                                                        <tr>
                                                            <td style="width: 36%; font-weight: bold; background: #f8fafc;">
                                                                <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Kekuatan Utama (Strengths)
                                                            </td>
                                                            <td class="text-dark">{{ !empty($obs['kekuatan_utama']) ? $obs['kekuatan_utama'] : '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-weight: bold; background: #f8fafc;">
                                                                <i class="bi bi-arrow-up-right-circle-fill text-primary me-1"></i> Area yang Perlu Ditingkatkan (Weaknesses)
                                                            </td>
                                                            <td class="text-dark">{{ !empty($obs['area_peningkatan']) ? $obs['area_peningkatan'] : '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-weight: bold; background: #f8fafc;">
                                                                <i class="bi bi-shield-check text-success me-1"></i> Catatan Perilaku / Sikap di Lapangan
                                                            </td>
                                                            <td class="text-dark">{{ !empty($obs['catatan_perilaku']) ? $obs['catatan_perilaku'] : '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-weight: bold; background: #f8fafc;">
                                                                <i class="bi bi-flag-fill text-danger me-1"></i> Fokus Latihan / Tindak Lanjut
                                                            </td>
                                                            <td class="text-dark">{{ !empty($obs['fokus_latihan']) ? $obs['fokus_latihan'] : '-' }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- BAGIAN F: REKAP HASIL PENILAIAN --}}
                                    <div class="col-lg-5">
                                        <div class="border rounded-3 p-3 h-100 bg-light-subtle d-flex flex-column">
                                            <h6 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                                                <i class="bi bi-award-fill text-success"></i> F. Rekap Hasil Penilaian
                                            </h6>
                                            <p class="text-muted small mb-3">Akumulasi penilaian dari seluruh asesor dan status capaian.</p>

                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <div class="p-3 bg-white border rounded-3 text-center">
                                                        <small class="text-muted fw-bold d-block text-uppercase" style="font-size: 0.7rem;">Total Skor</small>
                                                        <span class="fs-4 fw-bold text-primary">{{ $skor100 }}</span>
                                                        <span class="text-muted small">/ 100</span>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="p-3 bg-white border rounded-3 text-center">
                                                        <small class="text-muted fw-bold d-block text-uppercase" style="font-size: 0.7rem;">Rata-rata</small>
                                                        <span class="fs-4 fw-bold text-success">{{ $skorRata }}</span>
                                                        <span class="text-muted small">/ 5</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="p-2.5 rounded-3 border mb-3 text-center" style="background: #f0fdf4; border-color: #86efac !important;">
                                                <span class="small fw-bold text-success d-block" style="font-size: 0.72rem;">Status Capaian:</span>
                                                <span class="fw-bold text-success" style="font-size: 0.95rem;">
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
                                            </div>

                                            <div class="mt-auto">
                                                <label class="small fw-bold text-secondary mb-1">
                                                    <i class="bi bi-chat-quote-fill me-1 text-primary"></i>Kesimpulan / Rekomendasi Asesor:
                                                </label>
                                                <div class="p-2.5 bg-white border rounded-3 small text-dark fst-italic" style="min-height: 52px;">
                                                    {{ !empty($rap->catatan_pelatih) ? $rap->catatan_pelatih : 'Belum ada catatan kesimpulan dari asesor.' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
