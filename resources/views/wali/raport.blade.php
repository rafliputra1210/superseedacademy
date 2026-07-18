@extends('layouts.wali')
@section('title', 'Raport & Evaluasi Perkembangan Anak')

@section('content')
@if(!$athlete)
    <div class="alert alert-warning text-center p-5 card-custom">Belum ada data anak terhubung dengan akun Anda.</div>
@else

@if($myAthletes->count() > 1)
<div class="mb-4 bg-white p-3 rounded-4 shadow-sm d-flex align-items-center justify-content-between border flex-wrap gap-2 hover-elevate">
    <span class="small fw-semibold"><i class="bi bi-person-check-fill text-brand-green me-2"></i>Menampilkan Raport Evaluasi untuk: <strong>{{ $athlete->nama }}</strong></span>
    <form action="{{ route('wali.raport') }}" method="GET" class="d-flex gap-2">
        <select name="child_id" class="form-select form-select-sm border-success-subtle fw-medium" onchange="this.form.submit()">
            @foreach($myAthletes as $child)
                <option value="{{ $child->id }}" {{ ($athlete->id == $child->id) ? 'selected' : '' }}>{{ $child->nama }} (U-{{ $child->nomor_punggung ?? 'XX' }})</option>
            @endforeach
        </select>
    </form>
</div>
@endif

<div class="card card-custom p-4 mb-4 text-white hover-elevate" style="background: linear-gradient(135deg, var(--brand-green) 0%, #064e3b 100%) !important; border: none;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <span class="text-uppercase text-xs tracking-wider opacity-75 fw-bold" style="letter-spacing: 1px;">Evaluasi Berkala Sekolah Sepak Bola</span>
            <h4 class="mb-0 mt-1 fw-bold">Raport Perkembangan Skill: {{ $athlete->nama }}</h4>
        </div>
        <span class="badge bg-warning text-dark fs-6 fw-bold px-3 py-2">Kelompok Usia U-{{ $athlete->nomor_punggung ?? 'XX' }}</span>
    </div>
</div>

@php
    $tabs = [
        [
            'id' => 'active',
            'title' => 'Raport Aktif',
            'icon' => 'bi-file-earmark-check',
            'reports' => $reports->where('is_archived', false),
            'empty_message' => 'Belum ada raport aktif untuk saat ini.',
            'active' => true
        ],
        [
            'id' => 'archived',
            'title' => 'Arsip Raport',
            'icon' => 'bi-archive',
            'reports' => $reports->where('is_archived', true),
            'empty_message' => 'Belum ada raport yang diarsipkan.',
            'active' => false
        ]
    ];
@endphp

<ul class="nav nav-pills mb-4 gap-2" id="raportTab" role="tablist">
    @foreach($tabs as $tab)
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $tab['active'] ? 'active bg-success text-white' : 'bg-white text-secondary border' }} px-4 py-2 fw-bold rounded-3 shadow-sm" 
                id="{{ $tab['id'] }}-tab" 
                data-bs-toggle="pill" 
                data-bs-target="#{{ $tab['id'] }}-pane" 
                type="button" role="tab" 
                aria-controls="{{ $tab['id'] }}-pane" 
                aria-selected="{{ $tab['active'] ? 'true' : 'false' }}">
            <i class="bi {{ $tab['icon'] }} me-2"></i>{{ $tab['title'] }}
            <span class="badge {{ $tab['active'] ? 'bg-white text-success' : 'bg-secondary text-white' }} ms-1">{{ $tab['reports']->count() }}</span>
        </button>
    </li>
    @endforeach
</ul>

<div class="tab-content" id="raportTabContent">
    @foreach($tabs as $tab)
    <div class="tab-pane fade {{ $tab['active'] ? 'show active' : '' }}" id="{{ $tab['id'] }}-pane" role="tabpanel" aria-labelledby="{{ $tab['id'] }}-tab">
        <div class="space-y-4">
            @forelse($tab['reports'] as $raport)
            @php 
                $teknis = json_decode($raport->aspek_teknis, true) ?? [];
                $taktikal = json_decode($raport->aspek_taktikal, true) ?? [];
                $fisik = json_decode($raport->aspek_fisik_mental, true) ?? [];
            @endphp
            <div class="card card-custom p-4 border-top border-4 hover-elevate mb-4" style="border-top-color: var(--brand-green) !important;">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-brand-navy fs-6 px-3 py-1 fw-bold text-white">No: {{ $raport->nomor_asesmen ?: '-' }}</span>
                        <span class="text-xs text-muted"><i class="bi bi-calendar-check me-1"></i>Tanggal Asesmen: {{ $raport->tanggal_asesmen ? \Carbon\Carbon::parse($raport->tanggal_asesmen)->format('d M Y') : \Carbon\Carbon::parse($raport->created_at)->format('d M Y') }}</span>
                    </div>
                    <span class="badge bg-brand-light text-brand-green border border-success-subtle fw-bold px-3 py-1">Asesor: {{ $raport->asesor_nama }}</span>
                </div>

                <h6 class="font-weight-bold text-success small text-uppercase mb-3"><i class="bi bi-bar-chart-fill me-1"></i>A. Aspek Teknis</h6>
                <div class="table-responsive mb-4 bg-white p-3 rounded-3 shadow-sm border">
                    <table class="table table-borderless table-sm align-middle mb-0">
                        <tbody>
                            @foreach($teknis as $item)
                            <tr class="{{ !$loop->last ? 'border-bottom' : '' }}">
                                <td class="w-50 py-2"><span class="fw-semibold text-dark">{{ $item['aspek'] ?? '-' }}</span></td>
                                <td class="w-25 py-2">
                                    <div class="d-flex align-items-center gap-1 text-warning fs-6">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= ($item['skor'] ?? 0) ? '-fill' : '' }}"></i>
                                        @endfor
                                    </div>
                                </td>
                                <td class="w-25 py-2 text-muted small fst-italic text-end">{{ $item['catatan'] ?? '' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <h6 class="font-weight-bold text-success small text-uppercase mb-3"><i class="bi bi-diagram-3-fill me-1"></i>B. Aspek Taktikal</h6>
                <div class="table-responsive mb-4 bg-white p-3 rounded-3 shadow-sm border">
                    <table class="table table-borderless table-sm align-middle mb-0">
                        <tbody>
                            @foreach($taktikal as $item)
                            <tr class="{{ !$loop->last ? 'border-bottom' : '' }}">
                                <td class="w-50 py-2"><span class="fw-semibold text-dark">{{ $item['aspek'] ?? '-' }}</span></td>
                                <td class="w-25 py-2">
                                    <div class="d-flex align-items-center gap-1 text-warning fs-6">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= ($item['skor'] ?? 0) ? '-fill' : '' }}"></i>
                                        @endfor
                                    </div>
                                </td>
                                <td class="w-25 py-2 text-muted small fst-italic text-end">{{ $item['catatan'] ?? '' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <h6 class="font-weight-bold text-success small text-uppercase mb-3"><i class="bi bi-heart-pulse-fill me-1"></i>C. Aspek Fisik & Mental</h6>
                <div class="table-responsive mb-4 bg-white p-3 rounded-3 shadow-sm border">
                    <table class="table table-borderless table-sm align-middle mb-0">
                        <tbody>
                            @foreach($fisik as $item)
                            <tr class="{{ !$loop->last ? 'border-bottom' : '' }}">
                                <td class="w-50 py-2"><span class="fw-semibold text-dark">{{ $item['aspek'] ?? '-' }}</span></td>
                                <td class="w-25 py-2">
                                    <div class="d-flex align-items-center gap-1 text-warning fs-6">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= ($item['skor'] ?? 0) ? '-fill' : '' }}"></i>
                                        @endfor
                                    </div>
                                </td>
                                <td class="w-25 py-2 text-muted small fst-italic text-end">{{ $item['catatan'] ?? '' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <h6 class="font-weight-bold text-success small text-uppercase mb-2"><i class="bi bi-search me-1"></i>D. Observasi Kualitatif:</h6>
                <div class="row g-3 small mb-4">
                    <div class="col-md-6">
                        <div class="bg-white p-3 rounded-3 border h-100 shadow-sm">
                            <strong class="text-dark d-block mb-1 font-weight-bold"><i class="bi bi-graph-up-arrow text-primary me-1"></i> Kekuatan Utama:</strong>
                            <p class="text-gray-700 mb-0" style="line-height: 1.5;">{{ $raport->kekuatan_utama ?: '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-white p-3 rounded-3 border h-100 shadow-sm">
                            <strong class="text-warning d-block mb-1 font-weight-bold"><i class="bi bi-exclamation-triangle text-warning me-1"></i> Area Peningkatan:</strong>
                            <p class="text-gray-700 mb-0" style="line-height: 1.5;">{{ $raport->area_peningkatan ?: '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-warning bg-opacity-10 border-start border-4 border-warning p-3 rounded-3 small shadow-sm">
                    <strong class="text-dark d-block mb-1 font-weight-bold"><i class="bi bi-chat-quote-fill text-warning me-1"></i> Catatan Perilaku:</strong>
                    <p class="mb-0 text-dark font-italic fs-6" style="line-height: 1.5;">"{{ $raport->catatan_perilaku ?: '-' }}"</p>
                </div>
            </div>
            @empty
            <div class="card card-custom text-center py-5 text-muted">
                <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                <h6 class="font-weight-bold text-dark">Belum Ada Raport</h6>
                <p class="small mb-0 text-muted">{{ $tab['empty_message'] }}</p>
            </div>
            @endforelse
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection