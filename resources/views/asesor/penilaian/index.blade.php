@extends('layouts.admin')

@section('title', 'Penilaian Atlet')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-brand-navy mb-1">
                <i class="bi bi-clipboard-check-fill text-brand-blue me-2"></i>Penilaian Atlet
            </h4>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="text-secondary small">Periode Evaluasi:</span>
                <span class="badge bg-primary px-2.5 py-1 font-monospace">{{ $periodeSekarang }}</span>
                @if(isset($activePeriodName) && $periodeSekarang === $activePeriodName)
                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold" style="font-size: 0.72rem;">
                        <i class="bi bi-check-circle-fill me-1"></i>Periode Aktif Instansi
                    </span>
                @endif
            </div>
        </div>
        @if(isset($periods) && $periods->count() > 0)
        <div>
            <form action="{{ route('asesor.penilaian.index') }}" method="GET" class="d-flex align-items-center gap-2">
                <span class="small fw-semibold text-secondary">Pilih Periode:</span>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-range text-primary"></i></span>
                    <select name="periode" class="form-select bg-white border-start-0 fw-bold" onchange="this.form.submit()" style="min-width: 220px;">
                        @foreach($periods as $p)
                            <option value="{{ $p->nama }}" {{ $periodeSekarang === $p->nama ? 'selected' : '' }}>
                                {{ $p->nama }} {{ $p->is_active ? '★ (Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
        @endif
    </div>

    <!-- Ringkasan Statistik Status Penilaian -->
    @php
        $totalAtlet = count($athletes);
        $sudahDinilai = count($penilaianSelesai);
        $belumDinilai = max(0, $totalAtlet - $sudahDinilai);
        $persentase = $totalAtlet > 0 ? round(($sudahDinilai / $totalAtlet) * 100) : 0;
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom p-3 h-100 shadow-sm border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-medium">Total Atlet</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ $totalAtlet }}</div>
                    </div>
                    <div class="bg-brand-light text-brand-blue rounded-3 p-3">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom p-3 h-100 shadow-sm border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-medium">Sudah Dinilai</div>
                        <div class="fs-4 fw-bold text-success mt-1">{{ $sudahDinilai }}</div>
                    </div>
                    <div class="rounded-3 p-3" style="background-color: #ecfdf5; color: #10b981;">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom p-3 h-100 shadow-sm border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-medium">Belum Dinilai</div>
                        <div class="fs-4 fw-bold text-danger mt-1">{{ $belumDinilai }}</div>
                    </div>
                    <div class="rounded-3 p-3" style="background-color: #fef2f2; color: #ef4444;">
                        <i class="bi bi-hourglass-split fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom p-3 h-100 shadow-sm border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="w-100 me-2">
                        <div class="d-flex justify-content-between align-items-center text-secondary small fw-medium">
                            <span>Progres Anda</span>
                            <span class="fw-bold text-brand-blue">{{ $persentase }}%</span>
                        </div>
                        <div class="progress mt-2" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $persentase }}%" aria-valuenow="{{ $persentase }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="rounded-3 p-3" style="background-color: #eff6ff; color: #3b82f6;">
                        <i class="bi bi-graph-up-arrow fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Penilaian -->
    <div class="card card-custom shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
            <div>
                <h6 class="fw-bold mb-0 text-brand-navy">Daftar Atlet & Evaluasi</h6>
                <small class="text-muted">Pilih atlet untuk menginput atau memperbarui nilai evaluasi</small>
            </div>
            <div>
                <div class="input-group input-group-sm" style="max-width: 260px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="searchTable" class="form-control bg-light border-start-0" placeholder="Cari nama atlet...">
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="athleteTable">
                    <thead style="background-color: #f8fafc; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">
                        <tr>
                            <th class="ps-4 py-3" style="width: 60px;">No</th>
                            <th class="py-3">Nama Atlet</th>
                            <th class="py-3">Kel. Umur</th>
                            <th class="py-3">Kel. Latihan</th>
                            <th class="py-3">Status Penilaian</th>
                            <th class="pe-4 py-3 text-end" style="width: 160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($athletes as $index => $atlet)
                        @php
                            $sudah = in_array($atlet->id, $penilaianSelesai);
                        @endphp
                        <tr>
                            <td class="ps-4 text-muted small fw-medium">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-brand-light text-brand-blue d-flex align-items-center justify-content-center me-3 fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem;">
                                        {{ strtoupper(substr($atlet->nama, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark athlete-name">{{ $atlet->nama }}</div>
                                        <div class="text-muted small font-monospace athlete-code">{{ $atlet->kode_barcode ?: ('#' . str_pad($atlet->id, 4, '0', STR_PAD_LEFT)) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1 font-monospace">
                                    KU-{{ $atlet->kelompok_umur }}
                                </span>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-3 py-1" style="background-color: #e0f2fe; color: #0284c7; font-weight: 600;">
                                    {{ $atlet->kelompok_latihan ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @if($sudah)
                                    <span class="badge px-2.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1" style="background-color: #dcfce7; color: #15803d; font-weight: 600;">
                                        <i class="bi bi-check-circle-fill"></i> Sudah Dinilai
                                    </span>
                                @else
                                    <span class="badge px-2.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1" style="background-color: #fee2e2; color: #b91c1c; font-weight: 600;">
                                        <i class="bi bi-exclamation-circle-fill"></i> Belum Dinilai
                                    </span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('asesor.penilaian.create', ['athlete' => $atlet->id, 'periode' => $periodeSekarang]) }}" 
                                   class="btn btn-sm {{ $sudah ? 'btn-outline-primary' : 'btn-primary shadow-sm' }} d-inline-flex align-items-center gap-1 px-3">
                                    @if($sudah)
                                        <i class="bi bi-pencil-square"></i> Edit Nilai
                                    @else
                                        <i class="bi bi-star-fill"></i> Beri Nilai
                                    @endif
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Belum ada data atlet yang tersedia.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Live Search Filter (Nama & No. Induk Atlet)
    document.getElementById('searchTable')?.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#athleteTable tbody tr');
        rows.forEach(row => {
            const nameEl = row.querySelector('.athlete-name');
            const codeEl = row.querySelector('.athlete-code');
            const name = nameEl ? nameEl.textContent.toLowerCase() : '';
            const code = codeEl ? codeEl.textContent.toLowerCase() : '';
            row.style.display = (name.includes(query) || code.includes(query)) ? '' : 'none';
        });
    });
</script>
@endsection