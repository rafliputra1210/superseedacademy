@extends('layouts.admin')
@section('title', 'Edit Uang Kas & Keuangan')

@section('content')
<div class="card card-custom bg-white p-4 max-w-2xl shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h5 class="mb-0 font-weight-bold">Edit Form Kas & Keuangan</h5>
            <p class="text-muted small mb-0">Ubah detail data kas atau keuangan.</p>
        </div>
        <a href="{{ route('admin.finances.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <form action="{{ route('admin.finances.update', $finance->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label font-weight-bold">Tanggal Pembayaran / Transaksi <span class="text-danger">*</span></label>
                <input type="date" name="tanggal" class="form-control" value="{{ \Carbon\Carbon::parse($finance->tanggal)->format('Y-m-d') }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label font-weight-bold">Jenis Arus Kas <span class="text-danger">*</span></label>
                <select name="jenis" id="jenis_kas" class="form-select font-weight-bold text-success" required onchange="toggleSiswaSection()">
                    <option value="pemasukan" {{ $finance->jenis == 'pemasukan' ? 'selected' : '' }}>🟢 Pemasukan (Uang Masuk / Kas Siswa)</option>
                    <option value="pengeluaran" {{ $finance->jenis == 'pengeluaran' ? 'selected' : '' }}>🔴 Pengeluaran (Uang Keluar / Operasional)</option>
                </select>
            </div>

            <div class="col-md-12">
                <label class="form-label font-weight-bold">Kategori Transaksi <span class="text-danger">*</span></label>
                <input type="text" name="kategori" id="kategori_kas" list="kategori_list" class="form-control font-weight-bold border-success" required oninput="toggleSiswaSection()" value="{{ $finance->kategori }}" placeholder="Ketik atau pilih kategori transaksi...">
                <datalist id="kategori_list">
                    <option value="Iuran Uang Kas Bulanan Siswa">
                    <option value="Biaya Pendaftaran Liga">
                    <option value="Biaya Pembuatan Jersey">
                    <option value="Biaya Turnamen / Event">
                    <option value="Pendaftaran Siswa Baru">
                    <option value="Donasi / Sponsorship / Lainnya">
                    <option value="Sewa Lapangan & Stadion">
                    <option value="Honor Coach & Asisten">
                    <option value="Pembelian Peralatan Latihan">
                    <option value="Operasional Lain-lain">
                </datalist>
                <div class="form-text text-xs text-muted">Ketik <strong>Iuran Uang Kas Bulanan Siswa</strong> (atau kategori siswa lainnya) untuk memunculkan pilihan nama siswa.</div>
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
                                <option value="{{ $atlet->id }}" {{ $finance->athlete_id == $atlet->id ? 'selected' : '' }}>{{ $atlet->nama }} — (U-{{ $atlet->nomor_punggung ?? 'XX' }} | {{ $atlet->posisi_bermain ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label small font-weight-bold text-dark">Untuk Bulan Tagihan <span class="text-danger">*</span></label>
                        <select name="bulan_tagihan" id="bulan_tagihan" class="form-select">
                            <option value="">-- Pilih Bulan --</option>
                            @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $bulan)
                                @php $valBulan = $bulan . ' ' . date('Y'); @endphp
                                <option value="{{ $valBulan }}" {{ $finance->bulan_tagihan == $valBulan ? 'selected' : '' }}>{{ $valBulan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label font-weight-bold">Nominal (Rp) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text font-weight-bold bg-light">Rp</span>
                    <input type="number" name="nominal" class="form-control font-weight-bold fs-5 text-success" required placeholder="50000" min="0" value="{{ $finance->nominal }}">
                </div>
                <div class="form-text text-xs">Masukkan nominal sesuai jenis transaksi.</div>
            </div>

            <div class="col-md-6">
                <label class="form-label font-weight-bold">Status Pembayaran <span class="text-danger">*</span></label>
                <select name="status_bayar" id="status_bayar" class="form-select font-weight-bold" required onchange="toggleJatuhTempo(); toggleMetodePembayaran();">
                    <option value="lunas" {{ $finance->status_bayar == 'lunas' ? 'selected' : '' }}>✅ Lunas (Sudah Dibayar)</option>
                    <option value="belum_lunas" {{ $finance->status_bayar == 'belum_lunas' ? 'selected' : '' }}>🔴 Belum Lunas (Tagihan Terbuka)</option>
                </select>
                <div class="form-text text-xs">Pilih "Belum Lunas" untuk mencatat tagihan yang belum dibayar siswa.</div>
            </div>

            <div class="col-md-6" id="section_metode_pembayaran">
                <label class="form-label font-weight-bold">Metode Pembayaran</label>
                <div class="d-flex gap-2">
                    <div class="flex-fill">
                        <input type="radio" class="btn-check" name="metode_pembayaran" id="metode_cash" value="cash" autocomplete="off" onchange="toggleNamaTransfer()"
                            {{ ($finance->metode_pembayaran == 'cash' || is_null($finance->metode_pembayaran)) ? 'checked' : '' }}>
                        <label class="btn btn-outline-secondary w-100 fw-bold" for="metode_cash">
                            <i class="bi bi-cash-stack me-1"></i> 💵 Cash (Tunai)
                        </label>
                    </div>
                    <div class="flex-fill">
                        <input type="radio" class="btn-check" name="metode_pembayaran" id="metode_transfer" value="transfer" autocomplete="off" onchange="toggleNamaTransfer()"
                            {{ $finance->metode_pembayaran == 'transfer' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary w-100 fw-bold" for="metode_transfer">
                            <i class="bi bi-bank me-1"></i> 🏦 Transfer Bank
                        </label>
                    </div>
                </div>
                <div class="form-text text-xs text-muted mt-1">Pilih metode penerimaan atau pembayaran transaksi ini.</div>
            </div>

            <div class="col-md-6" id="section_nama_transfer" style="display:none;">
                <label class="form-label font-weight-bold text-primary"><i class="bi bi-person-badge me-1"></i> Atas Nama / Pengirim Transfer</label>
                <input type="text" name="nama_pengirim_transfer" id="nama_pengirim_transfer" class="form-control border-primary" value="{{ $finance->nama_pengirim_transfer }}" placeholder="Contoh: BCA a.n. Budi Santoso / Rekening Orang Tua">
                <div class="form-text text-xs text-primary">Ketik nama pemilik rekening atau pengirim transfer (Opsional).</div>
            </div>

            <div class="col-md-6" id="section_jatuh_tempo" style="display:none;">
                <label class="form-label font-weight-bold">Tanggal Jatuh Tempo</label>
                <input type="date" name="tanggal_jatuh_tempo" class="form-control border-warning" value="{{ $finance->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($finance->tanggal_jatuh_tempo)->format('Y-m-d') : '' }}">
                <div class="form-text text-xs text-warning fw-semibold">Batas waktu pembayaran tagihan ini.</div>
            </div>

            <div class="col-md-6">
                <label class="form-label font-weight-bold">Catatan / Keterangan Tambahan</label>
                <textarea name="keterangan" class="form-control" rows="2" placeholder="Opsional (Otomatis dibuatkan oleh sistem jika kosong)...">{{ $finance->keterangan }}</textarea>
            </div>
        </div>

        <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
            <a href="{{ route('admin.finances.index') }}" class="btn btn-light border px-4">Batal</a>
            <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
        </div>
    </form>
</div>

<script>
    function toggleSiswaSection() {
        const jenis = document.getElementById('jenis_kas').value;
        const kategori = document.getElementById('kategori_kas').value.toLowerCase();
        const sectionSiswa = document.getElementById('section_kas_siswa');
        const inputBulanTagihan = document.getElementById('bulan_tagihan');

        // Munculkan pilihan siswa jika kategori yang diketik mengandung kata kunci berikut
        const kataKunciSiswa = [
            'iuran', 'kas', 'liga', 'jersey', 'turnamen', 'event', 'siswa', 'pendaftaran', 'tagihan'
        ];
        
        let isKategoriSiswa = false;
        if (kategori.length >= 2) {
            isKategoriSiswa = kataKunciSiswa.some(kat => kategori.includes(kat));
        }

        if (jenis === 'pemasukan' && isKategoriSiswa) {
            sectionSiswa.style.display = 'block';
            if (inputBulanTagihan) inputBulanTagihan.disabled = false;
        } else {
            sectionSiswa.style.display = 'none';
            if (inputBulanTagihan) inputBulanTagihan.disabled = true;
        }
    }

    function toggleJatuhTempo() {
        const status = document.getElementById('status_bayar').value;
        const section = document.getElementById('section_jatuh_tempo');
        section.style.display = (status === 'belum_lunas') ? 'block' : 'none';
    }

    function toggleMetodePembayaran() {
        const status = document.getElementById('status_bayar').value;
        const section = document.getElementById('section_metode_pembayaran');
        section.style.display = (status === 'belum_lunas') ? 'none' : 'block';
        toggleNamaTransfer();
    }

    function toggleNamaTransfer() {
        const status = document.getElementById('status_bayar').value;
        const isTransfer = document.getElementById('metode_transfer').checked;
        const sectionNamaTF = document.getElementById('section_nama_transfer');
        if (sectionNamaTF) {
            sectionNamaTF.style.display = (isTransfer && status === 'lunas') ? 'block' : 'none';
        }
    }

    window.onload = function() {
        toggleSiswaSection();
        toggleJatuhTempo();
        toggleMetodePembayaran();
        toggleNamaTransfer();
    };

    document.addEventListener("DOMContentLoaded", function () {
        if (document.getElementById("athlete_id")) {
            new TomSelect("#athlete_id", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });
        }
    });
</script>
@endsection
