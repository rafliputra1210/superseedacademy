@extends('layouts.admin')
@section('title', 'Input Uang Kas & Keuangan')

@section('content')
<div class="card card-custom bg-white p-4 max-w-2xl shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h5 class="mb-0 font-weight-bold">Form Input Kas & Keuangan</h5>
            <p class="text-muted small mb-0">Catat pembayaran uang kas dari siswa atau pengeluaran operasional akademi.</p>
        </div>
        <a href="{{ route('admin.finances.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <form action="{{ route('admin.finances.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Tanggal Pembayaran / Transaksi <span class="text-danger">*</span></label>
                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label font-weight-bold">Jenis Arus Kas <span class="text-danger">*</span></label>
                <select name="jenis" id="jenis_kas" class="form-select font-weight-bold text-success" required onchange="toggleSiswaSection()">
                    <option value="pemasukan" selected>🟢 Pemasukan (Uang Masuk / Kas Siswa)</option>
                    <option value="pengeluaran">🔴 Pengeluaran (Uang Keluar / Operasional)</option>
                </select>
            </div>

            <div class="col-md-12">
                <label class="form-label font-weight-bold">Kategori Transaksi <span class="text-danger">*</span></label>
                <select name="kategori" id="kategori_kas" class="form-select font-weight-bold border-success" required onchange="toggleSiswaSection()">
                    <optgroup label="💰 Tagihan Siswa (Pemasukan)">
                        <option value="Iuran Uang Kas Bulanan Siswa" selected>⭐ Iuran Uang Kas Bulanan Siswa</option>
                        <option value="Biaya Pendaftaran Liga">🏆 Biaya Pendaftaran Liga</option>
                        <option value="Biaya Pembuatan Jersey">👕 Biaya Pembuatan Jersey</option>
                        <option value="Biaya Turnamen / Event">🎯 Biaya Turnamen / Event</option>
                        <option value="Pendaftaran Siswa Baru">📋 Pendaftaran Siswa Baru</option>
                        <option value="Donasi / Sponsorship / Subjek Lain">Donasi / Sponsorship / Lainnya</option>
                    </optgroup>
                    <optgroup label="🔴 Pengeluaran Operasional">
                        <option value="Sewa Lapangan & Stadion">Sewa Lapangan & Stadion</option>
                        <option value="Honor Coach & Asisten">Honor Coach & Asisten</option>
                        <option value="Pembelian Peralatan Latihan">Pembelian Peralatan Latihan</option>
                        <option value="Operasional Lain-lain">Operasional Lain-lain</option>
                    </optgroup>
                </select>
            </div>

            <div id="section_kas_siswa" class="col-12">
                <div class="p-3 bg-success bg-opacity-10 rounded-3 border border-success border-opacity-50 row g-3">
                    <div class="col-12">
                        <span class="badge bg-success mb-2"><i class="bi bi-person-check-fill me-1"></i> Data Pembayar Kas Siswa</span>
                    </div>
                    
                    <div class="col-md-7">
                        <label class="form-label small font-weight-bold text-dark">Pilih Nama Siswa / Atlet <span class="text-danger">*</span></label>
                        <select name="athlete_id" id="athlete_id" class="form-select">
                            <option value="">-- Pilih Siswa yang Membayar --</option>
                            @foreach($athletes as $atlet)
                                <option value="{{ $atlet->id }}">{{ $atlet->nama }} — (U-{{ $atlet->nomor_punggung ?? 'XX' }} | {{ $atlet->posisi_bermain ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label small font-weight-bold text-dark">Untuk Bulan Tagihan <span class="text-danger">*</span></label>
                        <select name="bulan_tagihan" class="form-select">
                            @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $bulan)
                                <option value="{{ $bulan }} {{ date('Y') }}" {{ $bulan == 'Juli' ? 'selected' : '' }}>{{ $bulan }} {{ date('Y') }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label font-weight-bold">Nominal (Rp) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text font-weight-bold bg-light">Rp</span>
                    <input type="number" name="nominal" class="form-control font-weight-bold fs-5 text-success" required placeholder="50000" min="0" value="50000">
                </div>
                <div class="form-text text-xs">Masukkan nominal sesuai jenis transaksi.</div>
            </div>

            <div class="col-md-6">
                <label class="form-label font-weight-bold">Status Pembayaran <span class="text-danger">*</span></label>
                <select name="status_bayar" id="status_bayar" class="form-select font-weight-bold" required onchange="toggleJatuhTempo()">
                    <option value="lunas" selected>✅ Lunas (Sudah Dibayar)</option>
                    <option value="belum_lunas">🔴 Belum Lunas (Tagihan Terbuka)</option>
                </select>
                <div class="form-text text-xs">Pilih "Belum Lunas" untuk mencatat tagihan yang belum dibayar siswa.</div>
            </div>

            <div class="col-md-6" id="section_jatuh_tempo" style="display:none;">
                <label class="form-label font-weight-bold">Tanggal Jatuh Tempo</label>
                <input type="date" name="tanggal_jatuh_tempo" class="form-control border-warning">
                <div class="form-text text-xs text-warning fw-semibold">Batas waktu pembayaran tagihan ini.</div>
            </div>

            <div class="col-md-6">
                <label class="form-label font-weight-bold">Catatan / Keterangan Tambahan</label>
                <textarea name="keterangan" class="form-control" rows="2" placeholder="Opsional (Otomatis dibuatkan oleh sistem jika kosong)..."></textarea>
            </div>
        </div>

        <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
            <a href="{{ route('admin.finances.index') }}" class="btn btn-light border px-4">Batal</a>
            <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow"><i class="bi bi-save me-1"></i> Simpan Pembayaran Kas</button>
        </div>
    </form>
</div>

<script>
    function toggleSiswaSection() {
        const jenis = document.getElementById('jenis_kas').value;
        const kategori = document.getElementById('kategori_kas').value;
        const sectionSiswa = document.getElementById('section_kas_siswa');
        const inputSiswa = document.getElementById('athlete_id');

        // Munculkan pilihan siswa hanya jika jenisnya pemasukan dan kategorinya Iuran Kas Siswa
        const kategoriSiswa = [
            'Iuran Uang Kas Bulanan Siswa',
            'Biaya Pendaftaran Liga',
            'Biaya Pembuatan Jersey',
            'Biaya Turnamen / Event',
            'Pendaftaran Siswa Baru'
        ];
        if (jenis === 'pemasukan' && kategoriSiswa.includes(kategori)) {
            sectionSiswa.style.display = 'block';
            inputSiswa.required = true;
        } else {
            sectionSiswa.style.display = 'none';
            inputSiswa.required = false;
            inputSiswa.value = '';
        }
    }

    function toggleJatuhTempo() {
        const status = document.getElementById('status_bayar').value;
        const section = document.getElementById('section_jatuh_tempo');
        section.style.display = (status === 'belum_lunas') ? 'block' : 'none';
    }

    window.onload = function() {
        toggleSiswaSection();
        toggleJatuhTempo();
    };
</script>
@endsection