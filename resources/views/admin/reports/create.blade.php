@extends('layouts.admin')
@section('title', 'Input Form Asesmen Pemain')

@section('content')
<div class="card card-custom bg-white p-4 shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h5 class="mb-0 font-weight-bold"><i class="bi bi-file-earmark-text text-warning me-2"></i>Form Asesmen Pemain & Observasi Lapangan</h5>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <form action="{{ route('admin.reports.store') }}" method="POST">
        @csrf
        <!-- A. DATA PEMAIN -->
        <h6 class="font-weight-bold text-success mb-3">A. Data Pemain & Asesmen</h6>
        <div class="row g-3 mb-4 bg-light p-3 rounded border">
            <div class="col-md-6">
                <label class="form-label font-weight-bold small">Nama Pemain <span class="text-danger">*</span></label>
                <select name="athlete_id" class="form-select font-weight-bold" required>
                    <option value="">-- Pilih Pemain --</option>
                    @foreach($athletes as $atlet)
                        <option value="{{ $atlet->id }}">{{ $atlet->nama }} — (Posisi: {{ $atlet->posisi_bermain ?? '-' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold small">Nama Asesor Penilai (Coach) <span class="text-danger">*</span></label>
                <input type="text" name="asesor_nama" class="form-control" required value="{{ Auth::user()->name }}">
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold small">Nomor Asesmen</label>
                <input type="text" name="nomor_asesmen" class="form-control" placeholder="Contoh: ASM/2026/07/001">
            </div>
            <div class="col-md-6">
                <label class="form-label font-weight-bold small">Tanggal Asesmen <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_asesmen" class="form-control" required value="{{ date('Y-m-d') }}">
            </div>
        </div>

        <div class="alert alert-info py-2 small">
            <strong>Skala Penilaian:</strong> 1 = Sangat Kurang | 2 = Kurang | 3 = Cukup | 4 = Baik | 5 = Sangat Baik
        </div>

        <!-- B. SKILL TEKNIS -->
        <h6 class="font-weight-bold text-success mt-4 mb-2">B. Penilaian Skill Teknis</h6>
        <table class="table table-bordered table-sm align-middle mb-4">
            <thead class="table-success text-center">
                <tr><th style="width: 50px;">No</th><th>Aspek Teknik</th><th style="width: 120px;">Skor (1-5)</th><th>Catatan / Observasi</th></tr>
            </thead>
            <tbody>
                @php $teknis = ['Kontrol bola (Ball Control)', 'Akurasi passing dan first touch', 'Kecepatan dribbling dan arah gerak', 'Teknik shooting dan finishing', 'Kemampuan bertahan (defending)', 'Kemampuan intercept dan membaca bola']; @endphp
                @foreach($teknis as $i => $aspek)
                <tr>
                    <td class="text-center font-weight-bold">{{ $i+1 }}</td>
                    <td class="small">{{ $aspek }}<input type="hidden" name="teknis_aspek[]" value="{{ $aspek }}"></td>
                    <td><input type="number" name="teknis_skor[]" class="form-control form-control-sm text-center font-weight-bold text-primary" min="1" max="5" value="3" required></td>
                    <td><input type="text" name="teknis_catatan[]" class="form-control form-control-sm" placeholder="Catatan..."></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- C. TAKTIKAL -->
        <h6 class="font-weight-bold text-success mt-4 mb-2">C. Penilaian Taktikal & Pemahaman Permainan</h6>
        <table class="table table-bordered table-sm align-middle mb-4">
            <thead class="table-warning text-center">
                <tr><th style="width: 50px;">No</th><th>Aspek Taktikal</th><th style="width: 120px;">Skor (1-5)</th><th>Catatan / Observasi</th></tr>
            </thead>
            <tbody>
                @php $taktikal = ['Posisi saat menyerang dan bertahan', 'Pemahaman transisi permainan', 'Kemampuan rotasi dan membuka ruang', 'Komunikasi dengan rekan tim', 'Pengambilan keputusan di bawah tekanan']; @endphp
                @foreach($taktikal as $i => $aspek)
                <tr>
                    <td class="text-center font-weight-bold">{{ $i+1 }}</td>
                    <td class="small">{{ $aspek }}<input type="hidden" name="taktik_aspek[]" value="{{ $aspek }}"></td>
                    <td><input type="number" name="taktik_skor[]" class="form-control form-control-sm text-center font-weight-bold text-primary" min="1" max="5" value="3" required></td>
                    <td><input type="text" name="taktik_catatan[]" class="form-control form-control-sm" placeholder="Catatan..."></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- D. FISIK & MENTAL -->
        <h6 class="font-weight-bold text-success mt-4 mb-2">D. Penilaian Fisik dan Mental</h6>
        <table class="table table-bordered table-sm align-middle mb-4">
            <thead class="table-info text-center">
                <tr><th style="width: 50px;">No</th><th>Aspek Fisik & Mental</th><th style="width: 120px;">Skor (1-5)</th><th>Catatan / Observasi</th></tr>
            </thead>
            <tbody>
                @php $fisik = ['Daya tahan dan stamina', 'Kecepatan reaksi dan kelincahan', 'Fokus dan konsentrasi', 'Disiplin mengikuti instruksi pelatih', 'Respon terhadap tekanan dan situasi sulit', 'Semangat kerja sama dan motivasi tim']; @endphp
                @foreach($fisik as $i => $aspek)
                <tr>
                    <td class="text-center font-weight-bold">{{ $i+1 }}</td>
                    <td class="small">{{ $aspek }}<input type="hidden" name="fisik_aspek[]" value="{{ $aspek }}"></td>
                    <td><input type="number" name="fisik_skor[]" class="form-control form-control-sm text-center font-weight-bold text-primary" min="1" max="5" value="3" required></td>
                    <td><input type="text" name="fisik_catatan[]" class="form-control form-control-sm" placeholder="Catatan..."></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- E. OBSERVASI KUALITATIF -->
        <h6 class="font-weight-bold text-success mt-4 mb-3">E. Observasi Lapangan (Kualitatif)</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small font-weight-bold text-primary">Kekuatan Utama (Strengths)</label>
                <textarea name="kekuatan_utama" class="form-control" rows="3" placeholder="Tuliskan kekuatan utama pemain..."></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label small font-weight-bold text-danger">Area yang Perlu Ditingkatkan (Weaknesses)</label>
                <textarea name="area_peningkatan" class="form-control" rows="3" placeholder="Tuliskan kelemahan pemain..."></textarea>
            </div>
            <div class="col-md-12">
                <label class="form-label small font-weight-bold text-dark">Catatan Perilaku / Sikap di Lapangan</label>
                <textarea name="catatan_perilaku" class="form-control" rows="2" placeholder="Tuliskan catatan sikap dan perilaku pemain..."></textarea>
            </div>
        </div>

        <div class="mt-4 pt-3 border-top text-end">
            <button type="submit" class="btn btn-primary px-5 font-weight-bold"><i class="bi bi-save me-1"></i> Simpan Hasil Asesmen</button>
        </div>
    </form>
</div>
@endsection