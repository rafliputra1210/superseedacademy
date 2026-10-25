@extends('layouts.wali')
@section('title', 'Raport & Evaluasi Perkembangan Anak')

@section('content')
@if(!$athlete)
    <div class="alert alert-warning text-center p-5 card-custom rounded-4">
        <i class="bi bi-person-x fs-1 d-block mb-2 text-warning"></i>
        <h5 class="fw-bold">Belum Ada Data Anak Terhubung</h5>
        <p class="text-muted small mb-0">Silakan hubungi administrator akademi untuk menghubungkan data anak dengan akun Anda.</p>
    </div>
@else

{{-- 1. PENGALIH DATA ANAK (JIKA WALI MEMILIKI LEBIH DARI 1 ANAK) --}}
@if($myAthletes->count() > 1)
<div class="mb-4 bg-white p-3 rounded-4 shadow-sm d-flex align-items-center justify-content-between border flex-wrap gap-2 hover-elevate">
    <span class="small fw-semibold text-dark">
        <i class="bi bi-person-check-fill text-brand-green me-2"></i>Menampilkan Raport untuk: <strong>{{ $athlete->nama }}</strong>
    </span>
    <form action="{{ route('wali.raport') }}" method="GET" class="d-flex gap-2">
        <select name="child_id" class="form-select form-select-sm border-success-subtle fw-medium" onchange="this.form.submit()">
            @foreach($myAthletes as $child)
                <option value="{{ $child->id }}" {{ ($athlete->id == $child->id) ? 'selected' : '' }}>
                    {{ $child->nama }} (KU-{{ $child->kelompok_umur ?? '-' }})
                </option>
            @endforeach
        </select>
    </form>
</div>
@endif

{{-- 2. BANNER INFORMASI ATLET --}}
<div class="card card-custom p-4 mb-4 text-white hover-elevate shadow-sm" style="background: linear-gradient(135deg, var(--brand-green) 0%, #064e3b 100%) !important; border: none; border-radius: 16px;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-white text-brand-green d-flex align-items-center justify-content-center fw-bold fs-3 shadow-sm" style="width: 58px; height: 58px;">
                {{ strtoupper(substr($athlete->nama, 0, 1)) }}
            </div>
            <div>
                <span class="text-uppercase text-xs tracking-wider opacity-75 fw-bold" style="letter-spacing: 1px;">Raport Evaluasi Siswa &bull; Super Seed Academy</span>
                <h4 class="mb-0 mt-0.5 fw-bold">{{ $athlete->nama }}</h4>
                <div class="small opacity-90 mt-1 d-flex flex-wrap align-items-center gap-2">
                    <span class="font-monospace">{{ $athlete->kode_barcode ?: ('ID: #' . str_pad($athlete->id, 4, '0', STR_PAD_LEFT)) }}</span>
                    <span>&bull;</span>
                    <span>Posisi: {{ $athlete->posisi_bermain ?? '-' }}</span>
                    <span>&bull;</span>
                    <span>Kelompok: {{ $athlete->kelompok_latihan ?? '-' }}</span>
                </div>
            </div>
        </div>
        <div>
            <span class="badge bg-warning text-dark fs-6 fw-bold px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-shield-check me-1"></i> KU-{{ $athlete->kelompok_umur ?? '-' }}
            </span>
        </div>
    </div>
</div>

{{-- 3. TABS: RAPORT AKTIF & ARSIP RAPORT --}}
@php
    $tabConfigs = [
        [
            'id'            => 'active',
            'title'         => 'Raport Aktif',
            'icon'          => 'bi-file-earmark-check-fill',
            'list'          => $activeRaports,
            'is_active'     => true,
            'empty_message' => 'Belum ada raport aktif untuk saat ini. Raport resmi akan muncul di sini setelah diterbitkan oleh tim pelatih.'
        ],
        [
            'id'            => 'archived',
            'title'         => 'Arsip Raport',
            'icon'          => 'bi-archive-fill',
            'list'          => $archivedRaports,
            'is_active'     => false,
            'empty_message' => 'Belum ada raport yang diarsipkan. Raport periode lampau yang diarsipkan oleh admin akan tersimpan di sini.'
        ]
    ];
@endphp

<ul class="nav nav-pills mb-4 gap-2" id="raportTab" role="tablist">
    @foreach($tabConfigs as $tab)
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab['is_active'] ? 'active bg-success text-white' : 'bg-white text-secondary border' }} px-4 py-2.5 fw-bold rounded-3 shadow-sm d-flex align-items-center gap-2" 
                    id="{{ $tab['id'] }}-tab" 
                    data-bs-toggle="pill" 
                    data-bs-target="#{{ $tab['id'] }}-pane" 
                    type="button" role="tab" 
                    aria-controls="{{ $tab['id'] }}-pane" 
                    aria-selected="{{ $tab['is_active'] ? 'true' : 'false' }}">
                <i class="bi {{ $tab['icon'] }}"></i>
                <span>{{ $tab['title'] }}</span>
                <span class="badge {{ $tab['is_active'] ? 'bg-white text-success' : 'bg-secondary text-white' }} ms-1 rounded-pill">
                    {{ $tab['list']->count() }}
                </span>
            </button>
        </li>
    @endforeach
</ul>

<div class="tab-content" id="raportTabContent">
    @foreach($tabConfigs as $tab)
        <div class="tab-pane fade {{ $tab['is_active'] ? 'show active' : '' }}" 
             id="{{ $tab['id'] }}-pane" 
             role="tabpanel" 
             aria-labelledby="{{ $tab['id'] }}-tab">

            @forelse($tab['list'] as $rf)
                <div class="card card-custom p-3 p-md-4 bg-white border shadow-sm rounded-4 mb-4 hover-elevate">
                    {{-- Header Raport --}}
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary px-3 py-2 fw-bold font-monospace fs-6 rounded-3">
                                <i class="bi bi-calendar-event me-1"></i>Periode: {{ $rf->periode }}
                            </span>
                            @if($rf->is_archived)
                                <span class="badge px-3 py-2 rounded-pill d-inline-flex align-items-center gap-1" style="background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-weight: 600;">
                                    <i class="bi bi-archive-fill text-secondary"></i> Diarsipkan
                                </span>
                            @else
                                <span class="badge px-3 py-2 rounded-pill d-inline-flex align-items-center gap-1" style="background-color: #dcfce7; color: #15803d; font-weight: 600;">
                                    <i class="bi bi-check-circle-fill"></i> Resmi Diterbitkan (Aktif)
                                </span>
                            @endif
                        </div>
                        <div class="text-secondary small">
                            <i class="bi bi-clock-history me-1"></i>Diperbarui: {{ $rf->updated_at ? \Carbon\Carbon::parse($rf->updated_at)->translatedFormat('d F Y') : '-' }}
                        </div>
                    </div>

                    {{-- 3.A. RINGKASAN SKOR PER KATEGORI (SKALA 1 - 5) --}}
                    <div class="mb-4">
                        <h6 class="fw-bold text-brand-navy mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-bar-chart-fill text-brand-green"></i>
                            <span>Ringkasan Nilai Kategori (Skala 1 – 5)</span>
                        </h6>
                        <div class="row g-3">
                            @if(!empty($rf->scores_summary) && is_array($rf->scores_summary))
                                @foreach($rf->scores_summary as $item)
                                    @php $scoreVal = (float)($item['nilai'] ?? 0); @endphp
                                    <div class="col-6 col-md-3">
                                        <div class="p-3 rounded-3 bg-light border text-center h-100 shadow-none hover-elevate">
                                            <span class="small fw-bold text-secondary text-uppercase d-block mb-1 text-truncate" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                                                {{ $item['nama'] ?? 'Kategori' }}
                                            </span>
                                            <div class="fs-4 fw-bold text-dark my-1">
                                                {{ $scoreVal }} <span class="text-muted fw-normal" style="font-size: 0.85rem;">/ 5</span>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center gap-0.5 text-warning my-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="bi bi-star{{ $i <= round($scoreVal) ? '-fill' : '' }}" style="font-size: 0.8rem;"></i>
                                                @endfor
                                            </div>
                                            <span class="badge rounded-pill px-2 py-0.5 text-xs {{ $scoreVal >= 4 ? 'bg-success-subtle text-success' : ($scoreVal >= 3 ? 'bg-primary-subtle text-primary' : 'bg-warning-subtle text-warning') }}" style="font-size: 0.7rem;">
                                                {{ $scoreVal >= 4.5 ? 'Sangat Baik' : ($scoreVal >= 3.5 ? 'Baik' : ($scoreVal >= 2.5 ? 'Cukup' : 'Perlu Ditingkatkan')) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-6 col-md-3"><div class="p-3 rounded-3 bg-light border text-center"><span class="small fw-semibold text-secondary d-block">Teknik</span><div class="fs-4 fw-bold text-dark my-1">{{ $rf->nilai_teknik_final }}</div></div></div>
                                <div class="col-6 col-md-3"><div class="p-3 rounded-3 bg-light border text-center"><span class="small fw-semibold text-secondary d-block">Taktik</span><div class="fs-4 fw-bold text-dark my-1">{{ $rf->nilai_taktik_final }}</div></div></div>
                                <div class="col-6 col-md-3"><div class="p-3 rounded-3 bg-light border text-center"><span class="small fw-semibold text-secondary d-block">Fisik</span><div class="fs-4 fw-bold text-dark my-1">{{ $rf->nilai_fisik_final }}</div></div></div>
                                <div class="col-6 col-md-3"><div class="p-3 rounded-3 bg-light border text-center"><span class="small fw-semibold text-secondary d-block">Mental</span><div class="fs-4 fw-bold text-dark my-1">{{ $rf->nilai_mental_final }}</div></div></div>
                            @endif
                        </div>
                    </div>

                    {{-- 3.B. TABEL RINCIAN ASPEK SUB POIN PENILAIAN (SESUAI DOKUMEN/GAMBAR) --}}
                    @if(!empty($rf->scores_summary) && is_array($rf->scores_summary))
                        <div class="mb-4">
                            <h6 class="fw-bold text-brand-navy mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-card-checklist text-brand-green"></i>
                                <span>Rincian Aspek Penilaian & Observasi Lapangan</span>
                            </h6>

                            <div class="d-flex flex-column gap-4">
                                @foreach($rf->scores_summary as $catIdx => $catSum)
                                    @php
                                        $subItems = $catSum['sub_points'] ?? [];
                                        $catScore = (float)($catSum['nilai'] ?? 0);
                                    @endphp
                                    <div class="border rounded-3 overflow-hidden bg-white shadow-none">
                                        {{-- Header Judul Kategori (B. Penilaian Skill Teknis) --}}
                                        <div class="px-3 py-2.5 bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-0 fs-6">
                                                    <span class="badge bg-brand-green text-white me-1.5 font-monospace px-2 py-0.5">{{ chr(65 + $loop->index) }}</span>
                                                    Penilaian {{ $catSum['nama'] }}
                                                </h6>
                                                <div class="text-muted small mt-0.5" style="font-size: 0.75rem;">
                                                    <strong>(1 = Sangat Kurang, 2 = Kurang, 3 = Cukup, 4 = Baik, 5 = Sangat Baik).</strong>
                                                </div>
                                            </div>
                                            <span class="badge bg-white text-brand-green border border-success-subtle px-2.5 py-1 font-monospace" style="font-size: 0.75rem;">
                                                Rata-rata: {{ $catScore }} / 5
                                            </span>
                                        </div>

                                        {{-- Tabel Sub Poin --}}
                                        @if(!empty($subItems) && count($subItems) > 0)
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.84rem;">
                                                    <thead style="background-color: #f1f5f9; color: #334155; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.03em;">
                                                        <tr>
                                                            <th class="text-center py-2" style="width: 50px;">No</th>
                                                            <th class="py-2">Aspek {{ $catSum['nama'] }}</th>
                                                            <th class="text-center py-2" style="width: 140px;">Skor (1–5)</th>
                                                            <th class="py-2">Catatan / Observasi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($subItems as $spIdx => $subItem)
                                                            @php $sVal = (float)($subItem['skor'] ?? 0); @endphp
                                                            <tr>
                                                                <td class="text-center fw-bold text-secondary">{{ $spIdx + 1 }}</td>
                                                                <td class="fw-semibold text-dark">{{ $subItem['aspek'] }}</td>
                                                                <td class="text-center">
                                                                    <div class="d-flex align-items-center justify-content-center gap-1 text-warning">
                                                                        @for($i = 1; $i <= 5; $i++)
                                                                            <i class="bi bi-star{{ $i <= round($sVal) ? '-fill' : '' }}" style="font-size: 0.8rem;"></i>
                                                                        @endfor
                                                                        <span class="fw-bold text-dark ms-1" style="font-size: 0.8rem;">({{ $sVal }})</span>
                                                                    </div>
                                                                </td>
                                                                <td class="text-muted fst-italic">
                                                                    {{ !empty($subItem['catatan']) ? $subItem['catatan'] : '-' }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="p-3 text-muted small fst-italic">
                                                Belum ada rincian sub-poin khusus untuk aspek ini. Rata-rata skor kategori: <strong>{{ $catScore }}</strong>.
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- 3.C. CATATAN & REKOMENDASI PELATIH --}}
                    @if($rf->catatan_pelatih)
                        <div class="p-3 bg-light rounded-3 border-start border-4 border-success">
                            <div class="small fw-bold text-dark mb-1 d-flex align-items-center gap-1.5">
                                <i class="bi bi-chat-quote-fill text-success fs-5"></i>
                                <span>Catatan & Evaluasi Perkembangan dari Tim Pelatih:</span>
                            </div>
                            <p class="small text-secondary mb-0 fst-italic ps-4 lh-base">
                                "{{ $rf->catatan_pelatih }}"
                            </p>
                        </div>
                    @endif

                </div>
            @empty
                <div class="card card-custom p-5 text-center bg-white border shadow-sm rounded-4 my-3">
                    <div class="mb-3">
                        <span class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center text-muted" style="width: 64px; height: 64px;">
                            <i class="bi {{ $tab['icon'] }} fs-2 text-secondary opacity-50"></i>
                        </span>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">{{ $tab['title'] }} Kosong</h6>
                    <p class="text-secondary small mb-0 mx-auto" style="max-width: 460px;">
                        {{ $tab['empty_message'] }}
                    </p>
                </div>
            @endforelse

        </div>
    @endforeach
</div>

@endif
@endsection