@extends('layouts.admin')

@section('title', 'Form Penilaian - ' . $athlete->nama)

@section('content')
<div class="container-fluid px-0" style="max-width: 900px;">
    <!-- Breadcrumb & Back Button -->
    <div class="mb-4">
        <a href="{{ route('asesor.penilaian.index', ['periode' => $periodeSekarang]) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-2 mb-3">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali ke Daftar Atlet</span>
        </a>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h4 class="fw-bold text-brand-navy mb-1">Form Penilaian Atlet</h4>
                <p class="text-secondary small mb-0">Silakan input penilaian aspek performa dan catatan evaluasi.</p>
            </div>
            <div>
                <span class="badge bg-primary px-3 py-2 rounded-pill font-monospace fs-6 shadow-sm">
                    <i class="bi bi-calendar-check me-1"></i> Periode: {{ $periodeSekarang }}
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
                    <span class="font-monospace fw-semibold text-secondary">{{ $athlete->kode_barcode ?: ('ID: #' . str_pad($athlete->id, 4, '0', STR_PAD_LEFT)) }}</span>
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
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                <div>
                    <h6 class="fw-bold mb-1 text-brand-navy">
                        <i class="bi bi-ui-checks text-brand-blue me-2"></i>Komponen Penilaian Aspek (Skala 1 – 5)
                    </h6>
                    <div class="text-muted small">
                        <strong>Keterangan Skala:</strong> 1 = Sangat Kurang &bull; 2 = Kurang &bull; 3 = Cukup &bull; 4 = Baik &bull; 5 = Sangat Baik
                    </div>
                </div>
                <small class="text-muted"><span class="text-danger">*</span> Wajib diisi</small>
            </div>
        </div>
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('asesor.penilaian.store', $athlete->id) }}" method="POST">
                @csrf
                
                <!-- Input Periode (Hidden) -->
                <input type="hidden" name="periode_bulan" value="{{ $periodeSekarang }}">

                <!-- Loop per Kategori Penilaian -->
                <div class="d-flex flex-column gap-4 mb-4">
                    @forelse($categories as $index => $cat)
                        @php
                            $subs = $cat->sub_points_list;
                            $prevSubs = $nilaiSebelumnya ? $nilaiSebelumnya->getCategorySubPoints($cat->id) : [];
                        @endphp
                        <div class="card border shadow-none rounded-3 overflow-hidden">
                            <!-- Category Title (Header B. Penilaian ...) -->
                            <div class="card-header py-2.5 px-3 bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0 fs-6">
                                        <span class="text-primary me-1">{{ chr(65 + $index) }}.</span> Penilaian {{ $cat->nama }}
                                    </h6>
                                    <div class="text-muted small" style="font-size: 0.75rem;">
                                        (1 = Sangat Kurang, 2 = Kurang, 3 = Cukup, 4 = Baik, 5 = Sangat Baik)
                                        @if($cat->deskripsi)
                                            &bull; <span class="fst-italic">{{ $cat->deskripsi }}</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="badge bg-white text-secondary border font-monospace" style="font-size: 0.72rem;">
                                    {{ count($subs) > 0 ? count($subs) . ' Aspek' : 'Skor Tunggal' }}
                                </span>
                            </div>

                            <div class="card-body p-0">
                                @if(count($subs) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover align-middle mb-0">
                                            <thead style="background-color: #e2e8f0; font-size: 0.8rem; color: #334155;">
                                                <tr>
                                                    <th class="text-center py-2" style="width: 50px;">No</th>
                                                    <th class="py-2">Aspek {{ $cat->nama }}</th>
                                                    <th class="text-center py-2" style="width: 170px;">Skor (1–5) <span class="text-danger">*</span></th>
                                                    <th class="py-2">Catatan / Observasi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($subs as $sIdx => $subPoint)
                                                    @php
                                                        $prevScore = 3;
                                                        $prevNote = '';
                                                        if (!empty($prevSubs)) {
                                                            foreach ($prevSubs as $ps) {
                                                                if (($ps['aspek'] ?? '') === $subPoint || (!isset($ps['aspek']) && isset($prevSubs[$sIdx]))) {
                                                                    $prevScore = $ps['skor'] ?? 3;
                                                                    $prevNote = $ps['catatan'] ?? '';
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        $oldScore = old("sub_scores.{$cat->id}.{$sIdx}.skor", $prevScore);
                                                        $oldNote = old("sub_scores.{$cat->id}.{$sIdx}.catatan", $prevNote);
                                                    @endphp
                                                    <tr>
                                                        <td class="text-center fw-bold text-secondary">{{ $sIdx + 1 }}</td>
                                                        <td>
                                                            <span class="fw-semibold text-dark">{{ $subPoint }}</span>
                                                            <input type="hidden" name="sub_scores[{{ $cat->id }}][{{ $sIdx }}][aspek]" value="{{ $subPoint }}">
                                                        </td>
                                                        <td>
                                                            <select name="sub_scores[{{ $cat->id }}][{{ $sIdx }}][skor]" class="form-select form-select-sm fw-bold text-center border-primary-subtle" required>
                                                                <option value="5" {{ (string)$oldScore === '5' ? 'selected' : '' }}>5 = Sangat Baik</option>
                                                                <option value="4" {{ (string)$oldScore === '4' ? 'selected' : '' }}>4 = Baik</option>
                                                                <option value="3" {{ (string)$oldScore === '3' ? 'selected' : '' }}>3 = Cukup</option>
                                                                <option value="2" {{ (string)$oldScore === '2' ? 'selected' : '' }}>2 = Kurang</option>
                                                                <option value="1" {{ (string)$oldScore === '1' ? 'selected' : '' }}>1 = Sangat Kurang</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="sub_scores[{{ $cat->id }}][{{ $sIdx }}][catatan]" 
                                                                   class="form-control form-control-sm" 
                                                                   placeholder="Tulis catatan / observasi khusus..."
                                                                   value="{{ $oldNote }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <!-- Fallback single score -->
                                    <div class="p-3">
                                        <div class="row align-items-center g-3">
                                            <div class="col-md-5">
                                                <label class="form-label fw-bold small text-dark mb-0">Skor {{ $cat->nama }} (1–5) <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-md-7">
                                                <select name="scores[{{ $cat->id }}]" class="form-select form-select-sm fw-bold text-center">
                                                    @php $currentSingle = old('scores.' . $cat->id, $nilaiSebelumnya ? $nilaiSebelumnya->getCategoryScore($cat->id, $cat->nama) : 3); @endphp
                                                    <option value="5" {{ (string)$currentSingle === '5' ? 'selected' : '' }}>5 = Sangat Baik</option>
                                                    <option value="4" {{ (string)$currentSingle === '4' ? 'selected' : '' }}>4 = Baik</option>
                                                    <option value="3" {{ (string)$currentSingle === '3' ? 'selected' : '' }}>3 = Cukup</option>
                                                    <option value="2" {{ (string)$currentSingle === '2' ? 'selected' : '' }}>2 = Kurang</option>
                                                    <option value="1" {{ (string)$currentSingle === '1' ? 'selected' : '' }}>1 = Sangat Kurang</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-warning d-flex align-items-center gap-2 mb-0">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                            <div>Belum ada kategori penilaian aktif yang ditentukan oleh Administrator.</div>
                        </div>
                    @endforelse
                </div>

                <!-- Catatan Asesor -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">
                        Catatan & Evaluasi Perkembangan Keseluruhan <span class="text-muted fw-normal small">(Opsional)</span>
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