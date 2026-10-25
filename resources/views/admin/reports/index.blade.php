@extends('layouts.admin')

@section('title', 'Rekapitulasi & Penerbitan Raport')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-brand-navy mb-1">
                <i class="bi bi-award-fill text-brand-blue me-2"></i>Rekapitulasi & Penerbitan Raport
            </h4>
            <p class="text-secondary small mb-0">
                Pantau progres penilaian dari asesor dan terbitkan akumulasi raport final per atlet.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Tombol Kelola Kategori Penilaian -->
            <a href="{{ route('admin.assessment_categories.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-sliders2"></i>
                <span>Kategori Penilaian</span>
                <span class="badge bg-secondary text-white ms-1">{{ $categories->count() }}</span>
            </a>

            <!-- Tombol Atur & Ganti Periode Instansi -->
            <button type="button" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalPeriode">
                <i class="bi bi-calendar-range-fill"></i>
                <span>Atur & Ganti Periode</span>
            </button>

            <!-- Dropdown Filter Periode -->
            <form action="{{ route('admin.reports.index') }}" method="GET" class="d-flex align-items-center gap-1">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-event text-primary"></i></span>
                    <select name="periode" class="form-select bg-white border-start-0 fw-semibold" onchange="this.form.submit()" style="min-width: 210px;">
                        <optgroup label="Periode Terdaftar (Semester / Bulanan)">
                            @foreach($periods as $p)
                                <option value="{{ $p->nama }}" {{ $periodeSekarang === $p->nama ? 'selected' : '' }}>
                                    {{ $p->nama }} {{ $p->is_active ? '★ (Aktif)' : '' }}
                                </option>
                            @endforeach
                        </optgroup>
                        @php
                            $extraPeriods = $historyPeriods->diff($periods->pluck('nama'));
                        @endphp
                        @if($extraPeriods->count() > 0)
                            <optgroup label="Riwayat Periode Lainnya">
                                @foreach($extraPeriods as $hp)
                                    <option value="{{ $hp }}" {{ $periodeSekarang === $hp ? 'selected' : '' }}>
                                        {{ $hp }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Banner: Periode Saat Ini & Status Keaktifan -->
    <div class="card card-custom p-3 shadow-sm border-0 mb-4 bg-white">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="small fw-bold text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Periode Raport Ditampilkan:</span>
                <span class="badge bg-primary px-3 py-1.5 fs-6 font-monospace">
                    <i class="bi bi-calendar-check me-1"></i>{{ $periodeSekarang }}
                </span>
                @if($activePeriod && $periodeSekarang === $activePeriod->nama)
                    <span class="badge px-2.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1" style="background-color: #dcfce7; color: #15803d; font-weight: 600; font-size: 0.75rem;">
                        <i class="bi bi-check-circle-fill"></i> Periode Aktif Instansi
                    </span>
                @else
                    @php
                        $matchedPeriod = $periods->where('nama', $periodeSekarang)->first();
                    @endphp
                    @if($matchedPeriod)
                        <form action="{{ route('admin.reports.periods.set-active') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="period_id" value="{{ $matchedPeriod->id }}">
                            <button type="submit" class="btn btn-sm btn-outline-success py-1 px-2.5 rounded-pill shadow-none fw-semibold" style="font-size: 0.75rem;" title="Jadikan periode ini sebagai acuan penilaian aktif asesor">
                                <i class="bi bi-pin-angle-fill me-1"></i>Jadikan Periode Aktif Instansi
                            </button>
                        </form>
                    @endif
                @endif
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap text-secondary small">
                <span>Periode Aktif Instansi: <strong class="text-dark">{{ $periodeAktifName }}</strong></span>
                <span class="mx-1">&bull;</span>
                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalPeriode" class="text-primary text-decoration-none fw-semibold">
                    <i class="bi bi-pencil-square me-1"></i>Ubah Periode
                </a>
            </div>
        </div>
    </div>

    <!-- Tabel Rekapitulasi -->
    <div class="card card-custom shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h6 class="fw-bold mb-0 text-brand-navy">Matriks Penilaian Asesor & Status Raport</h6>
                <small class="text-muted">Periode: <strong>{{ $periodeSekarang }}</strong> | Asesor Terdaftar: {{ $assessors->count() }} orang</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="small text-muted fw-semibold me-1">Filter:</span>
                <div class="btn-group btn-group-sm shadow-none" role="group">
                    <button type="button" class="btn btn-sm btn-primary active filter-btn" onclick="filterStatusRow('all', this)">
                        Semua ({{ $athletes->count() }})
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success filter-btn" onclick="filterStatusRow('active', this)">
                        <i class="bi bi-check-circle me-1"></i>Aktif ({{ $raportFinal->where('is_archived', false)->count() }})
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary filter-btn" onclick="filterStatusRow('archived', this)">
                        <i class="bi bi-archive me-1"></i>Arsip ({{ $raportFinal->where('is_archived', true)->count() }})
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0" id="raportTable">
                    <thead style="background-color: #f8fafc; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">
                        <tr>
                            <th rowspan="2" class="align-middle text-start ps-4 border-end" style="min-width: 200px;">Nama Siswa Atlet</th>
                            <th colspan="{{ max(1, $assessors->count()) }}" class="py-2 border-end bg-light text-dark fw-bold">
                                Status Penilaian per Asesor
                            </th>
                            <th rowspan="2" class="align-middle border-end" style="width: 150px;">Status Raport</th>
                            <th rowspan="2" class="align-middle pe-4 text-end" style="width: 240px;">Aksi Super Admin</th>
                        </tr>
                        <tr>
                            @forelse($assessors as $asesor)
                                <th class="py-2 border-bottom bg-white fw-semibold small text-truncate" style="max-width: 130px;" title="{{ $asesor->name }}">
                                    {{ Str::limit($asesor->name, 12) }}
                                </th>
                            @empty
                                <th class="py-2 text-muted fw-normal">Belum ada akun asesor</th>
                            @endforelse
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($athletes as $index => $atlet)
                            @php
                                $nilaiAtletIni = $allScores->where('athlete_id', $atlet->id);
                                $jumlahDinilai = $nilaiAtletIni->count();
                                $sudahTerbit = $raportFinal->where('athlete_id', $atlet->id)->first();
                                $rowStatus = 'unissued';
                                if ($sudahTerbit) {
                                    $rowStatus = $sudahTerbit->is_archived ? 'archived' : 'active';
                                }
                            @endphp
                            <tr class="raport-row" data-status="{{ $rowStatus }}">
                                <td class="text-start ps-4 border-end">
                                    <div class="fw-bold text-dark">{{ $atlet->nama }}</div>
                                    <div class="text-muted small">
                                        KU-{{ $atlet->kelompok_umur }} &bull; {{ $atlet->kelompok_latihan ?? '-' }}
                                    </div>
                                </td>

                                <!-- Checklist Per Asesor -->
                                @if($assessors->count() > 0)
                                    @foreach($assessors as $asesor)
                                        @php
                                            $cekPenilaian = $nilaiAtletIni->where('assessor_id', $asesor->id)->first();
                                        @endphp
                                        <td>
                                            @if($cekPenilaian)
                                                <span class="badge rounded-circle p-1" style="background-color: #dcfce7; color: #15803d;" title="Sudah dinilai oleh {{ $asesor->name }}">
                                                    <i class="bi bi-check-lg fs-6"></i>
                                                </span>
                                            @else
                                                <span class="badge rounded-circle p-1" style="background-color: #f1f5f9; color: #94a3b8;" title="Belum dinilai">
                                                    <i class="bi bi-dash fs-6"></i>
                                                </span>
                                            @endif
                                        </td>
                                    @endforeach
                                @else
                                    <td class="text-muted small">-</td>
                                @endif

                                <!-- Status Raport Final -->
                                <td class="border-end">
                                    @if($sudahTerbit)
                                        @if($sudahTerbit->is_archived)
                                            <span class="badge px-2.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1" style="background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;" title="Raport masuk tab Arsip pada portal wali murid">
                                                <i class="bi bi-archive-fill text-secondary"></i> Diarsipkan
                                            </span>
                                        @else
                                            <span class="badge px-2.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1" style="background-color: #dcfce7; color: #15803d; font-weight: 600;" title="Raport aktif di portal wali murid">
                                                <i class="bi bi-check-circle-fill"></i> Terbit (Aktif)
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge px-2.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1" style="background-color: #fee2e2; color: #b91c1c; font-weight: 600;">
                                            <i class="bi bi-hourglass-split"></i> Belum Terbit
                                        </span>
                                    @endif
                                </td>

                                <!-- Tombol Aksi -->
                                <td class="pe-4 text-end">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        @if($sudahTerbit)
                                            <!-- Tombol Detail Nilai Final -->
                                            <button type="button" class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalDetailRaport{{ $atlet->id }}"
                                                    title="Lihat Rincian Nilai">
                                                <i class="bi bi-eye"></i>
                                                <span>Detail</span>
                                            </button>

                                            <!-- Tombol Re-generate -->
                                            <form action="{{ route('admin.reports.generate-final', $atlet->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="periode_bulan" value="{{ $periodeSekarang }}">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1" 
                                                        onclick="return confirm('Perbarui ulang akumulasi raport {{ $atlet->nama }} dari {{ $jumlahDinilai }} penilaian asesor?')">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                    <span>Update</span>
                                                </button>
                                            </form>

                                            <!-- Tombol Toggle Arsip -->
                                            <form action="{{ route('admin.reports.toggle-archive', $sudahTerbit->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @if($sudahTerbit->is_archived)
                                                    <button type="submit" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1" 
                                                            onclick="return confirm('Aktifkan kembali raport {{ $atlet->nama }}? Raport akan muncul di tab Raport Aktif wali murid.')"
                                                            title="Kembalikan ke status aktif">
                                                        <i class="bi bi-box-arrow-up"></i>
                                                        <span>Aktifkan</span>
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-sm btn-outline-warning d-inline-flex align-items-center gap-1" 
                                                            onclick="return confirm('Arsipkan raport {{ $atlet->nama }}? Raport akan dipindahkan ke tab Arsip Raport pada portal wali murid.')"
                                                            title="Pindahkan ke arsip">
                                                        <i class="bi bi-archive"></i>
                                                        <span>Arsip</span>
                                                    </button>
                                                @endif
                                            </form>
                                        @else
                                            <form action="{{ route('admin.reports.generate-final', $atlet->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="periode_bulan" value="{{ $periodeSekarang }}">
                                                <button type="submit" class="btn btn-sm {{ $jumlahDinilai > 0 ? 'btn-primary shadow-sm' : 'btn-light border text-muted' }} d-inline-flex align-items-center gap-1" 
                                                    {{ $jumlahDinilai == 0 ? 'disabled' : '' }}
                                                    onclick="return confirm('Generate nilai rata-rata dari {{ $jumlahDinilai }} asesor dan Terbitkan ke Portal Wali?')">
                                                    <i class="bi bi-cpu"></i>
                                                    <span>Generate & Terbitkan ({{ $jumlahDinilai }})</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    @if($sudahTerbit)
                                        <!-- Modal Detail Nilai Raport -->
                                        <div class="modal fade" id="modalDetailRaport{{ $atlet->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header border-bottom">
                                                        <div>
                                                            <h6 class="modal-title fw-bold text-brand-navy mb-0">
                                                                <i class="bi bi-award-fill text-primary me-2"></i>Raport Final: {{ $atlet->nama }}
                                                            </h6>
                                                            <small class="text-muted">Periode: {{ $periodeSekarang }}</small>
                                                        </div>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <h6 class="small fw-bold text-uppercase text-secondary mb-3">
                                                            <i class="bi bi-bar-chart-fill text-primary me-1"></i>Ringkasan Rata-rata per Kategori:
                                                        </h6>
                                                        <div class="row g-2 mb-4">
                                                            @if(!empty($sudahTerbit->scores_summary) && is_array($sudahTerbit->scores_summary))
                                                                @foreach($sudahTerbit->scores_summary as $item)
                                                                    <div class="col-6 col-md-3">
                                                                        <div class="p-2.5 rounded-3 bg-light border text-center">
                                                                            <span class="small fw-semibold text-dark d-block text-truncate">{{ $item['nama'] ?? 'Kategori' }}</span>
                                                                            <span class="badge bg-primary fs-6 mt-1">{{ $item['nilai'] ?? 0 }} <small style="font-size: 0.65rem;">/ 5</small></span>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <div class="col-6 col-md-3"><div class="p-2.5 rounded-3 bg-light border text-center"><span class="small fw-semibold text-dark d-block">Teknik</span><span class="badge bg-primary fs-6 mt-1">{{ $sudahTerbit->nilai_teknik_final }}</span></div></div>
                                                                <div class="col-6 col-md-3"><div class="p-2.5 rounded-3 bg-light border text-center"><span class="small fw-semibold text-dark d-block">Taktik</span><span class="badge bg-primary fs-6 mt-1">{{ $sudahTerbit->nilai_taktik_final }}</span></div></div>
                                                                <div class="col-6 col-md-3"><div class="p-2.5 rounded-3 bg-light border text-center"><span class="small fw-semibold text-dark d-block">Fisik</span><span class="badge bg-primary fs-6 mt-1">{{ $sudahTerbit->nilai_fisik_final }}</span></div></div>
                                                                <div class="col-6 col-md-3"><div class="p-2.5 rounded-3 bg-light border text-center"><span class="small fw-semibold text-dark d-block">Mental</span><span class="badge bg-primary fs-6 mt-1">{{ $sudahTerbit->nilai_mental_final }}</span></div></div>
                                                            @endif
                                                        </div>

                                                        <!-- Rincian Sub Poin per Kategori -->
                                                        @if(!empty($sudahTerbit->scores_summary) && is_array($sudahTerbit->scores_summary))
                                                            <h6 class="small fw-bold text-uppercase text-secondary mb-3">
                                                                <i class="bi bi-list-check text-primary me-1"></i>Rincian Penilaian Aspek (Skor 1–5):
                                                            </h6>
                                                            <div class="d-flex flex-column gap-3 mb-4">
                                                                @foreach($sudahTerbit->scores_summary as $kIdx => $catSummary)
                                                                    @if(!empty($catSummary['sub_points']) && is_array($catSummary['sub_points']))
                                                                        <div class="border rounded-3 overflow-hidden">
                                                                            <div class="bg-light px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                                                                                <span class="fw-bold text-dark small">Penilaian {{ $catSummary['nama'] }}</span>
                                                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.72rem;">Rata-rata: {{ $catSummary['nilai'] }} / 5</span>
                                                                            </div>
                                                                            <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.8rem;">
                                                                                <thead class="table-light">
                                                                                    <tr>
                                                                                        <th class="text-center" style="width: 40px;">No</th>
                                                                                        <th>Aspek {{ $catSummary['nama'] }}</th>
                                                                                        <th class="text-center" style="width: 100px;">Skor (1–5)</th>
                                                                                        <th>Catatan / Observasi</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach($catSummary['sub_points'] as $spIdx => $subItem)
                                                                                        <tr>
                                                                                            <td class="text-center text-muted fw-bold">{{ $spIdx + 1 }}</td>
                                                                                            <td class="fw-medium text-dark">{{ $subItem['aspek'] }}</td>
                                                                                            <td class="text-center">
                                                                                                <span class="badge bg-brand-navy">{{ $subItem['skor'] }}</span>
                                                                                            </td>
                                                                                            <td class="text-muted fst-italic">{{ $subItem['catatan'] ?: '-' }}</td>
                                                                                        </tr>
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        @endif

                                                        @if($sudahTerbit->catatan_pelatih)
                                                            <div class="mt-3">
                                                                <label class="small fw-bold text-dark"><i class="bi bi-chat-quote me-1 text-primary"></i>Catatan Gabungan Asesor:</label>
                                                                <div class="p-3 bg-light rounded-3 small text-secondary">
                                                                    {{ $sudahTerbit->catatan_pelatih }}
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer border-top bg-light py-2">
                                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 3 + max(1, $assessors->count()) }}" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                    Belum ada data atlet yang terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PENGATURAN PERIODE RAPORT INSTANSI -->
<div class="modal fade" id="modalPeriode" tabindex="-1" aria-labelledby="modalPeriodeLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header bg-light py-3 border-bottom">
                <div>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="modalPeriodeLabel">
                        <i class="bi bi-calendar-range text-primary me-2"></i>Pengaturan Periode Raport & Evaluasi
                    </h6>
                    <small class="text-muted">Tetapkan periode per semester, bulanan, atau sesuai ketentuan instansi</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Tab Navigasi Modal -->
            <ul class="nav nav-tabs px-3 pt-2 bg-light border-bottom gap-2" id="periodModalTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold small py-2 px-3" id="tab-switch-period" data-bs-toggle="tab" data-bs-target="#pane-switch-period" type="button" role="tab">
                        <i class="bi bi-arrow-repeat me-1"></i> Ganti Periode Aktif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold small py-2 px-3" id="tab-add-period" data-bs-toggle="tab" data-bs-target="#pane-add-period" type="button" role="tab">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Periode Baru
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold small py-2 px-3" id="tab-list-period" data-bs-toggle="tab" data-bs-target="#pane-list-period" type="button" role="tab">
                        <i class="bi bi-list-check me-1"></i> Daftar Semua Periode ({{ $periods->count() }})
                    </button>
                </li>
            </ul>

            <div class="modal-body p-4">
                <div class="tab-content" id="periodModalTabContent">

                    <!-- TAB 1: GANTI PERIODE AKTIF -->
                    <div class="tab-pane fade show active" id="pane-switch-period" role="tabpanel">
                        <div class="alert alert-info border-0 d-flex align-items-center gap-3 p-3 mb-4 rounded-3" style="background-color: #eff6ff; color: #1e40af;">
                            <i class="bi bi-info-circle-fill fs-4 flex-shrink-0"></i>
                            <div class="small">
                                <strong>Periode Aktif Instansi Saat Ini:</strong> 
                                <span class="badge bg-primary ms-1 font-monospace fs-6">{{ $periodeAktifName }}</span><br>
                                Periode aktif adalah acuan resmi yang otomatis digunakan oleh para Asesor saat memberi penilaian atlet dan menjadi acuan utama saat menerbitkan raport final.
                            </div>
                        </div>

                        <form action="{{ route('admin.reports.periods.set-active') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark small">Pilih Periode untuk Diaktifkan:</label>
                                <select name="period_id" class="form-select form-select-lg fw-semibold" required>
                                    @foreach($periods as $p)
                                        <option value="{{ $p->id }}" {{ $p->is_active ? 'selected' : '' }}>
                                            {{ $p->nama }} &bull; [Tipe: {{ ucfirst($p->tipe) }}] {{ $p->is_active ? '★ (Sedang Aktif)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold">
                                    <i class="bi bi-check-circle me-1"></i> Terapkan Sebagai Periode Aktif
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 2: TAMBAH PERIODE BARU -->
                    <div class="tab-pane fade" id="pane-add-period" role="tabpanel">
                        <form action="{{ route('admin.reports.periods.store') }}" method="POST">
                            @csrf
                            <!-- Pilih Tipe Periode -->
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark small mb-2">Format / Ketentuan Periode Instansi:</label>
                                <div class="row g-2">
                                    <div class="col-6 col-sm-3">
                                        <input type="radio" class="btn-check" name="tipe" id="tipe_sem_ganjil" value="semester" checked onchange="presetPeriod('sem_ganjil')">
                                        <label class="btn btn-outline-primary w-100 text-center py-2 h-100 d-flex flex-column align-items-center justify-content-center" for="tipe_sem_ganjil">
                                            <i class="bi bi-mortarboard fs-5 mb-1"></i>
                                            <span class="small fw-bold">Semester Ganjil</span>
                                        </label>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <input type="radio" class="btn-check" name="tipe" id="tipe_sem_genap" value="semester" onchange="presetPeriod('sem_genap')">
                                        <label class="btn btn-outline-primary w-100 text-center py-2 h-100 d-flex flex-column align-items-center justify-content-center" for="tipe_sem_genap">
                                            <i class="bi bi-mortarboard-fill fs-5 mb-1"></i>
                                            <span class="small fw-bold">Semester Genap</span>
                                        </label>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <input type="radio" class="btn-check" name="tipe" id="tipe_bulanan" value="bulanan" onchange="presetPeriod('bulanan')">
                                        <label class="btn btn-outline-primary w-100 text-center py-2 h-100 d-flex flex-column align-items-center justify-content-center" for="tipe_bulanan">
                                            <i class="bi bi-calendar-month fs-5 mb-1"></i>
                                            <span class="small fw-bold">Bulanan</span>
                                        </label>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <input type="radio" class="btn-check" name="tipe" id="tipe_kustom" value="kustom" onchange="presetPeriod('kustom')">
                                        <label class="btn btn-outline-primary w-100 text-center py-2 h-100 d-flex flex-column align-items-center justify-content-center" for="tipe_kustom">
                                            <i class="bi bi-gear fs-5 mb-1"></i>
                                            <span class="small fw-bold">Kustom Bebas</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Input Nama Periode -->
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark small">Nama Periode Raport <span class="text-danger">*</span></label>
                                <input type="text" name="nama" id="input_nama_periode" class="form-control fw-bold" placeholder="Contoh: Semester Ganjil 2026/2027" required>
                                <small class="text-muted">Nama ini akan tercetak pada raport final dan dipilih oleh asesor.</small>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary small">Tahun Ajaran / Tahun (Opsional)</label>
                                    @php
                                        $cy = date('Y');
                                        $ny = (int)$cy + 1;
                                        $defaultTa = "{$cy}/{$ny}";
                                    @endphp
                                    <input type="text" name="tahun_ajaran" id="input_tahun_ajaran" class="form-control" value="{{ $defaultTa }}" placeholder="Contoh: {{ $defaultTa }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary small">Keterangan Tambahan (Opsional)</label>
                                    <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Evaluasi Tengah Tahun...">
                                </div>
                            </div>

                            <!-- Checkbox Langsung Jadikan Aktif -->
                            <div class="form-check form-switch mb-3 p-2 bg-light rounded-3 ps-5 border">
                                <input class="form-check-input ms-n4" type="checkbox" name="is_active" id="check_is_active" value="1" checked>
                                <label class="form-check-label fw-bold text-dark small" for="check_is_active">
                                    Langsung jadikan periode ini sebagai <u>Periode Aktif Instansi</u>
                                </label>
                                <div class="text-muted" style="font-size: 0.75rem;">Asesor dan admin akan langsung diarahkan ke periode baru ini.</div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-sm btn-success px-4 fw-bold">
                                    <i class="bi bi-plus-circle me-1"></i> Simpan Periode Baru
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 3: DAFTAR SEMUA PERIODE -->
                    <div class="tab-pane fade" id="pane-list-period" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead class="table-light text-secondary small">
                                    <tr>
                                        <th>Nama Periode</th>
                                        <th>Tipe</th>
                                        <th>Status</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($periods as $per)
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $per->nama }}</div>
                                                @if($per->tahun_ajaran)
                                                    <div class="text-muted small" style="font-size: 0.75rem;">TA: {{ $per->tahun_ajaran }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border font-monospace text-capitalize">
                                                    {{ $per->tipe }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($per->is_active)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold">
                                                        <i class="bi bi-check-circle-fill me-1"></i> Aktif Instansi
                                                    </span>
                                                @else
                                                    <form action="{{ route('admin.reports.periods.set-active') }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="period_id" value="{{ $per->id }}">
                                                        <button type="submit" class="btn btn-xs btn-outline-primary py-0 px-2 rounded-pill shadow-none" style="font-size: 0.72rem;">
                                                            Aktifkan
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if(!$per->is_active)
                                                    <form action="{{ route('admin.reports.periods.destroy', $per->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus periode {{ $per->nama }}?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Hapus Periode">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted small fst-italic" style="font-size: 0.72rem;">Sedang Aktif</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-3 text-muted">Belum ada periode tersimpan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
function presetPeriod(type) {
    const inputNama = document.getElementById('input_nama_periode');
    const inputTa = document.getElementById('input_tahun_ajaran');
    const taVal = inputTa ? inputTa.value.trim() : '{{ date('Y') }}/{{ (int)date('Y')+1 }}';
    const yearOnly = '{{ date('Y') }}';
    
    if (type === 'sem_ganjil') {
        inputNama.value = 'Semester Ganjil ' + taVal;
    } else if (type === 'sem_genap') {
        inputNama.value = 'Semester Genap ' + taVal;
    } else if (type === 'bulanan') {
        inputNama.value = '{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}';
    } else if (type === 'kustom') {
        inputNama.value = '';
        inputNama.focus();
    }
}

// Inisialisasi default nama periode di form tambah
document.addEventListener('DOMContentLoaded', function() {
    presetPeriod('sem_ganjil');
});
function filterStatusRow(type, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => {
        b.classList.remove('active');
        if (b.classList.contains('btn-primary')) {
            b.classList.remove('btn-primary');
            b.classList.add('btn-outline-primary');
        } else if (b.classList.contains('btn-success')) {
            b.classList.remove('btn-success');
            b.classList.add('btn-outline-success');
        } else if (b.classList.contains('btn-secondary')) {
            b.classList.remove('btn-secondary');
            b.classList.add('btn-outline-secondary');
        }
    });

    btn.classList.add('active');
    if (btn.classList.contains('btn-outline-primary')) {
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-primary');
    } else if (btn.classList.contains('btn-outline-success')) {
        btn.classList.remove('btn-outline-success');
        btn.classList.add('btn-success');
    } else if (btn.classList.contains('btn-outline-secondary')) {
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-secondary');
    }

    const rows = document.querySelectorAll('.raport-row');
    rows.forEach(row => {
        const status = row.getAttribute('data-status');
        if (type === 'all') {
            row.style.display = '';
        } else if (type === 'active') {
            row.style.display = (status === 'active') ? '' : 'none';
        } else if (type === 'archived') {
            row.style.display = (status === 'archived') ? '' : 'none';
        }
    });
}
</script>
@endsection