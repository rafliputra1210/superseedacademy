@extends('layouts.admin')

@section('title', 'Form Penilaian - ' . $athlete->nama)

@section('content')
<div class="container-fluid px-0" style="max-width: 900px;">
    <!-- Breadcrumb & Back Button -->
    <div class="mb-4">
        <a href="{{ route('asesor.penilaian.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-2 mb-3">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali ke Daftar Atlet</span>
        </a>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h4 class="fw-bold text-brand-navy mb-1">Form Penilaian Atlet</h4>
                <p class="text-secondary small mb-0">Silakan input penilaian aspek performa dan catatan evaluasi.</p>
            </div>
            <div>
                <span class="badge bg-primary px-3 py-2 rounded-pill font-monospace">
                    <i class="bi bi-calendar-check me-1"></i> {{ $periodeSekarang }}
                </span>
            </div>
        </div>
    </div>

    <!-- Info Atlet Card -->
    <div class="card card-custom shadow-sm border-0 mb-4 p-3 bg-white">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-brand-light text-brand-blue d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 52px; height: 52px;">
                {{ strtoupper(substr($athlete->nama, 0, 1)) }}
            </div>
            <div>
                <h5 class="fw-bold text-brand-navy mb-1">{{ $athlete->nama }}</h5>
                <div class="d-flex flex-wrap align-items-center gap-2 text-secondary small">
                    <span>ID: #{{ str_pad($athlete->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <span>&bull;</span>
                    <span class="badge bg-light text-dark border">KU-{{ $athlete->kelompok_umur }}</span>
                    <span>&bull;</span>
                    <span class="badge" style="background-color: #e0f2fe; color: #0284c7;">{{ $athlete->kelompok_latihan ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Penilaian -->
    <div class="card card-custom shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-brand-navy">
                    <i class="bi bi-sliders text-brand-blue me-2"></i>Komponen Penilaian (Skala 0 - 100)
                </h6>
                <small class="text-muted"><span class="text-danger">*</span> Wajib diisi</small>
            </div>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('asesor.penilaian.store', $athlete->id) }}" method="POST">
                @csrf
                
                <!-- Input Periode (Hidden) -->
                <input type="hidden" name="periode_bulan" value="{{ $periodeSekarang }}">

                <!-- Dynamic Categories -->
                <div class="row g-4 mb-4">
                    @forelse($categories as $index => $cat)
                        @php
                            $currentScore = null;
                            if (old('scores.' . $cat->id) !== null) {
                                $currentScore = old('scores.' . $cat->id);
                            } elseif ($nilaiSebelumnya) {
                                $currentScore = $nilaiSebelumnya->getCategoryScore($cat->id, $cat->nama);
                            }
                        @endphp
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark d-flex justify-content-between align-items-center">
                                <span>{{ $index + 1 }}. Nilai {{ $cat->nama }} <span class="text-danger">*</span></span>
                                <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.7rem;">0 - 100</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary"><i class="bi bi-star-fill text-warning"></i></span>
                                <input type="number" name="scores[{{ $cat->id }}]" 
                                       class="form-control @error('scores.' . $cat->id) is-invalid @enderror" 
                                       min="0" max="100" placeholder="Masukkan skor (0 - 100)"
                                       value="{{ $currentScore }}" required>
                            </div>
                            @if($cat->deskripsi)
                                <div class="form-text text-muted small mt-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-info-circle me-1"></i>{{ $cat->deskripsi }}
                                </div>
                            @endif
                            @error('scores.' . $cat->id)
                                <small class="text-danger mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-warning d-flex align-items-center gap-2 mb-0">
                                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                                <div>Belum ada kategori penilaian aktif yang ditentukan oleh Administrator.</div>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Catatan Asesor -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">
                        Catatan & Evaluasi Perkembangan <span class="text-muted fw-normal small">(Opsional)</span>
                    </label>
                    <textarea name="catatan_asesor" class="form-control @error('catatan_asesor') is-invalid @enderror" rows="4" 
                              placeholder="Tuliskan catatan khusus atau rekomendasi perkembangan untuk atlet ini...">{{ old('catatan_asesor', $nilaiSebelumnya->catatan_asesor ?? '') }}</textarea>
                    @error('catatan_asesor')
                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Action Button -->
                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <a href="{{ route('asesor.penilaian.index') }}" class="btn btn-light px-4">Batal</a>
                    <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-2" {{ $categories->isEmpty() ? 'disabled' : '' }}>
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Simpan Penilaian</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection