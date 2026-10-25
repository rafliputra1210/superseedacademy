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
            <a href="{{ route('admin.assessment_categories.index') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-sliders2"></i>
                <span>Kelola Kategori Penilaian</span>
                <span class="badge bg-primary text-white ms-1">{{ $categories->count() }}</span>
            </a>

            <!-- Form Filter Periode -->
            <form action="{{ route('admin.reports.index') }}" method="GET" class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-event text-muted"></i></span>
                    <input type="text" name="periode" class="form-control bg-white border-start-0" value="{{ $periodeSekarang }}" placeholder="Contoh: Oktober 2026">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Kategori Penilaian Aktif -->
    <div class="card card-custom p-3 shadow-sm border-0 mb-4 bg-white">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="small fw-bold text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Kategori Penilaian Aktif:</span>
                @forelse($categories as $cat)
                    <span class="badge bg-light text-dark border px-2.5 py-1.5 font-monospace">
                        <i class="bi bi-check2 text-success me-1"></i>{{ $cat->nama }}
                    </span>
                @empty
                    <span class="badge bg-warning text-dark">Belum ada kategori aktif</span>
                @endforelse
            </div>
            <a href="{{ route('admin.assessment_categories.index') }}" class="small text-primary text-decoration-none fw-semibold">
                + Tambah / Edit Kategori
            </a>
        </div>
    </div>

    <!-- Tabel Rekapitulasi -->
    <div class="card card-custom shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold mb-0 text-brand-navy">Matriks Penilaian Asesor & Status Raport</h6>
                <small class="text-muted">Periode: <strong>{{ $periodeSekarang }}</strong> | Asesor Terdaftar: {{ $assessors->count() }} orang</small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0">
                    <thead style="background-color: #f8fafc; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">
                        <tr>
                            <th rowspan="2" class="align-middle text-start ps-4 border-end" style="min-width: 200px;">Nama Siswa Atlet</th>
                            <th colspan="{{ max(1, $assessors->count()) }}" class="py-2 border-end bg-light text-dark fw-bold">
                                Status Penilaian per Asesor
                            </th>
                            <th rowspan="2" class="align-middle border-end" style="width: 140px;">Status Raport</th>
                            <th rowspan="2" class="align-middle pe-4 text-end" style="width: 220px;">Aksi Super Admin</th>
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
                            @endphp
                            <tr>
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
                                        <span class="badge px-2.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1" style="background-color: #dcfce7; color: #15803d; font-weight: 600;">
                                            <i class="bi bi-check-circle-fill"></i> Sudah Terbit
                                        </span>
                                    @else
                                        <span class="badge px-2.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1" style="background-color: #fee2e2; color: #b91c1c; font-weight: 600;">
                                            <i class="bi bi-hourglass-split"></i> Belum Terbit
                                        </span>
                                    @endif
                                </td>

                                <!-- Tombol Aksi -->
                                <td class="pe-4 text-end">
                                    <div class="d-inline-flex align-items-center gap-2">
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
                                            <div class="modal-dialog modal-dialog-centered">
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
                                                        <h6 class="small fw-bold text-uppercase text-secondary mb-3">Akumulasi Rata-rata Nilai:</h6>
                                                        <div class="row g-2 mb-3">
                                                            @if(!empty($sudahTerbit->scores_summary) && is_array($sudahTerbit->scores_summary))
                                                                @foreach($sudahTerbit->scores_summary as $item)
                                                                    <div class="col-6">
                                                                        <div class="p-2.5 rounded-3 bg-light border d-flex justify-content-between align-items-center">
                                                                            <span class="small fw-semibold text-dark">{{ $item['nama'] ?? 'Kategori' }}</span>
                                                                            <span class="badge bg-primary fs-6">{{ $item['nilai'] ?? 0 }}</span>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <!-- Fallback Legacy Columns -->
                                                                <div class="col-6">
                                                                    <div class="p-2.5 rounded-3 bg-light border d-flex justify-content-between align-items-center">
                                                                        <span class="small fw-semibold text-dark">Teknik</span>
                                                                        <span class="badge bg-primary fs-6">{{ $sudahTerbit->nilai_teknik_final }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="p-2.5 rounded-3 bg-light border d-flex justify-content-between align-items-center">
                                                                        <span class="small fw-semibold text-dark">Taktik</span>
                                                                        <span class="badge bg-primary fs-6">{{ $sudahTerbit->nilai_taktik_final }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="p-2.5 rounded-3 bg-light border d-flex justify-content-between align-items-center">
                                                                        <span class="small fw-semibold text-dark">Fisik</span>
                                                                        <span class="badge bg-primary fs-6">{{ $sudahTerbit->nilai_fisik_final }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="p-2.5 rounded-3 bg-light border d-flex justify-content-between align-items-center">
                                                                        <span class="small fw-semibold text-dark">Mental</span>
                                                                        <span class="badge bg-primary fs-6">{{ $sudahTerbit->nilai_mental_final }}</span>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        @if($sudahTerbit->catatan_pelatih)
                                                            <div class="mt-3">
                                                                <label class="small fw-bold text-dark">Catatan Gabungan Asesor:</label>
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
@endsection