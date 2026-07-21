@extends('layouts.admin')
@section('title', 'Manajemen Data Atlet & Akun Wali Murid')

@section('content')

{{-- ===================== STYLES ===================== --}}
<style>
    /* --- Pagination Override & Fixes --- */
    .pagination-wrapper nav div:first-child {
        display: none; /* Menyembunyikan teks info default Laravel yang tumpang tindih */
    }
    .pagination { 
        gap: 4px; 
        margin-bottom: 0; 
        flex-wrap: wrap;
    }
    .page-link {
        border-radius: 8px !important;
        border: 1px solid #e2e8f0;
        color: #334155;
        font-size: 0.825rem;
        font-weight: 600;
        padding: 6px 12px;
        transition: all .15s ease-in-out;
        line-height: 1.4;
        box-shadow: none !important;
    }
    .page-link:hover { 
        background: #0A192F; 
        color: #fff; 
        border-color: #0A192F; 
    }
    .page-item.active .page-link { 
        background: #0A192F; 
        border-color: #0A192F; 
        color: #fff; 
    }
    .page-item.disabled .page-link { 
        background: #f8fafc; 
        color: #94a3b8; 
        border-color: #e2e8f0;
    }

    /* --- Table Styling & Hover --- */
    .athlete-table th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: none;
    }
    .athlete-table tbody tr {
        transition: background-color .15s ease;
    }
    .athlete-table tbody tr:hover { 
        background-color: #f8fafc; 
    }

    /* --- Badge Chips --- */
    .chip { 
        display: inline-flex; 
        align-items: center; 
        font-size: 0.7rem; 
        padding: 3px 10px; 
        border-radius: 20px; 
        font-weight: 600; 
        line-height: 1.5; 
        white-space: nowrap;
    }
    .chip-ku  { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
    .chip-kl  { background: #e0f2fe; color: #075985; border: 1px solid #7dd3fc; }
    .chip-pos { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

    /* --- Status Badge --- */
    .badge-aktif  { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
    .badge-noakun { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

    /* --- Action Buttons --- */
    .btn-act {
        width: 34px; 
        height: 34px; 
        border-radius: 8px;
        display: inline-flex; 
        align-items: center; 
        justify-content: center;
        font-size: 0.85rem; 
        border-width: 1px; 
        border-style: solid;
        transition: transform .12s ease, box-shadow .12s ease;
        text-decoration: none;
    }
    .btn-act:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 4px 8px rgba(0,0,0,.08); 
    }
    .btn-act-edit  { background: #fff7ed; color: #d97706; border-color: #fed7aa; }
    .btn-act-del   { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
    .btn-act-print { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }
    .btn-act-pay   { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
</style>

{{-- ===================== HEADER CARD ===================== --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
    <div class="card-body p-4">

        {{-- Title + Search row --}}
        <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3 mb-3">

            {{-- Judul --}}
            <div class="d-flex align-items-center gap-3">
                <span style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, #0A192F, #1e3a5f); display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="bi bi-people-fill text-white fs-5"></i>
                </span>
                <div>
                    <h4 class="fw-bold text-dark mb-1">Daftar Atlet Superseed Academy</h4>
                    <p class="text-muted mb-0" style="font-size: 0.875rem;">
                        Kelola biodata siswa &amp; pantau akun portal orang tua / wali murid.
                    </p>
                </div>
            </div>

            {{-- Search --}}
            <form action="{{ route('admin.athletes.index') }}" method="GET" class="flex-shrink-0">
                <div class="input-group shadow-sm" style="min-width: 320px;">
                    <span class="input-group-text bg-white border-end-0 border-secondary-subtle">
                        <i class="bi bi-search text-secondary" style="font-size: 0.875rem;"></i>
                    </span>
                    <input
                        type="text"
                        name="cari"
                        class="form-control border-start-0 border-secondary-subtle"
                        placeholder="Cari nama, no. punggung, KU…"
                        value="{{ request('cari') }}"
                        autocomplete="off">
                    <button class="btn btn-primary px-3 fw-medium" type="submit" style="background: #0A192F; border-color: #0A192F;">Cari</button>
                    @if(request('cari'))
                        <a href="{{ route('admin.athletes.index') }}" class="btn btn-outline-secondary d-flex align-items-center" title="Hapus filter">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>

        </div>

        {{-- Divider --}}
        <hr class="border-secondary-subtle my-3">

        {{-- Action Buttons --}}
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('admin.athletes.create') }}" class="btn btn-primary btn-sm shadow-sm d-flex align-items-center gap-2 px-3 py-2" style="background: #0A192F; border-color: #0A192F; border-radius: 8px;">
                <i class="bi bi-plus-circle-fill"></i> <span>Tambah Siswa</span>
            </a>

            <a href="{{ route('admin.athletes.print-all') }}" target="_blank" class="btn btn-dark btn-sm shadow-sm d-flex align-items-center gap-2 px-3 py-2" style="border-radius: 8px;">
                <i class="bi bi-printer-fill text-warning"></i> <span>Cetak Semua ID Card</span>
            </a>

            <a href="{{ route('admin.athletes.export') }}" class="btn btn-success btn-sm shadow-sm d-flex align-items-center gap-2 px-3 py-2" style="border-radius: 8px;">
                <i class="bi bi-file-earmark-excel-fill"></i> <span>Export Excel</span>
            </a>

            <button type="button" class="btn btn-warning btn-sm text-dark fw-medium shadow-sm d-flex align-items-center gap-2 px-3 py-2" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalImportAthlete">
                <i class="bi bi-cloud-upload-fill"></i> <span>Import Excel</span>
            </button>
        </div>

    </div>
</div>

{{-- ===================== TABEL DATA ATLET ===================== --}}
<div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
    <div class="table-responsive">
        <table class="table athlete-table align-middle mb-0" style="font-size: 0.875rem;">
            <thead style="background: linear-gradient(135deg, #0A192F, #1e3a5f); color: #fff;">
                <tr>
                    <th class="text-center py-3 px-3" style="width: 50px;">#</th>
                    <th class="py-3 px-3">Nama &amp; Info Siswa</th>
                    <th class="text-center py-3 px-3" style="width: 120px;">No. Punggung</th>
                    <th class="py-3 px-3" style="white-space: nowrap;">Tgl Lahir</th>
                    <th class="py-3 px-3" style="white-space: nowrap;">Kontak WA</th>
                    <th class="py-3 px-3">Akun Portal Wali</th>
                    <th class="text-center py-3 px-3" style="width: 130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($athletes as $item)
                <tr style="border-bottom: 1px solid #f1f5f9;">

                    {{-- Nomor urut --}}
                    <td class="text-center px-3 py-3">
                        <span class="text-muted fw-semibold" style="font-size: 0.825rem;">
                            {{ ($athletes->currentPage() - 1) * $athletes->perPage() + $loop->iteration }}
                        </span>
                    </td>

                    {{-- Nama & Info --}}
                    <td class="px-3 py-3">
                        <div class="d-flex align-items-center gap-3">
                            {{-- Avatar --}}
                            @if($item->foto)
                                <img src="{{ asset($item->foto) }}" alt="{{ $item->nama }}" style="width: 46px; height: 46px; border-radius: 12px; object-fit: cover; border: 1px solid #e2e8f0; flex-shrink: 0;">
                            @else
                                <div style="width: 46px; height: 46px; flex-shrink: 0; border-radius: 12px; background: linear-gradient(135deg, #dbeafe, #93c5fd); display: flex; align-items: center; justify-content: center; font-weight: 800; color: #1d4ed8; font-size: 1.1rem; border: 1px solid #bfdbfe;">
                                    {{ strtoupper(substr($item->nama, 0, 1)) }}
                                </div>
                            @endif
                            
                            {{-- Info --}}
                            <div>
                                <div class="fw-bold text-dark mb-1" style="font-size: 0.925rem; line-height: 1.3;">{{ $item->nama }}</div>
                                <div class="d-flex flex-wrap gap-1">
                                    @if($item->kelompok_umur)
                                        <span class="chip chip-ku">{{ $item->kelompok_umur }}</span>
                                    @endif
                                    @if($item->kelompok_latihan)
                                        <span class="chip chip-kl">{{ $item->kelompok_latihan }}</span>
                                    @endif
                                    @if($item->posisi_bermain)
                                        <span class="chip chip-pos">{{ $item->posisi_bermain }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- Nomor Punggung --}}
                    <td class="text-center px-3 py-3">
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, #0A192F, #1e3a5f); display: flex; align-items: center; justify-content: center; margin: 0 auto; box-shadow: 0 2px 6px rgba(10,25,47,.15);">
                            <span style="color: #fff; font-weight: 800; font-size: 0.9rem;">{{ $item->nomor_punggung ?? '–' }}</span>
                        </div>
                    </td>

                    {{-- Tanggal Lahir --}}
                    <td class="px-3 py-3" style="white-space: nowrap;">
                        <div class="d-flex align-items-center gap-2 text-dark" style="font-size: 0.85rem;">
                            <i class="bi bi-calendar3 text-primary"></i>
                            <span>{{ $item->tanggal_lahir ? \Carbon\Carbon::parse($item->tanggal_lahir)->format('d M Y') : '–' }}</span>
                        </div>
                    </td>

                    {{-- Kontak WA --}}
                    <td class="px-3 py-3">
                        <div class="d-flex align-items-center gap-2 mb-1" style="font-size: 0.85rem;">
                            <i class="bi bi-whatsapp text-success fs-6"></i>
                            <span class="fw-semibold text-dark">{{ $item->nomor_wa_ortu ?: '–' }}</span>
                        </div>
                        @if($item->nomor_wa)
                        <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.78rem;">
                            <i class="bi bi-phone"></i>
                            <span>{{ $item->nomor_wa }}</span>
                        </div>
                        @endif
                    </td>

                    {{-- Akun Wali --}}
                    <td class="px-3 py-3">
                        @if($item->user)
                            <div class="d-flex align-items-center gap-2">
                                <span class="chip badge-aktif flex-shrink-0">
                                    <i class="bi bi-check-circle-fill me-1"></i>Aktif
                                </span>
                                <div style="min-width: 0;">
                                    <div class="fw-semibold text-dark text-truncate" style="font-size: 0.85rem; max-width: 150px;">{{ $item->user->name }}</div>
                                    <code style="font-size: 0.75rem; color: #0066FF; background: #eff6ff; padding: 2px 6px; border-radius: 4px; font-weight: 600; display: inline-block; margin-top: 2px;">{{ $item->user->username }}</code>
                                </div>
                            </div>
                        @else
                            <div>
                                <span class="chip badge-noakun mb-1">
                                    <i class="bi bi-x-circle-fill me-1"></i>Belum Ada Akun
                                </span>
                                <a href="{{ route('admin.athletes.edit', $item->id) }}" class="d-block text-decoration-none fw-medium" style="font-size: 0.78rem; color: #0066FF;">
                                    <i class="bi bi-plus-circle me-1"></i>Buatkan Akun
                                </a>
                            </div>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="text-center px-3 py-3">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('admin.finances.create', ['athlete_id' => $item->id]) }}" class="btn-act btn-act-pay" title="Input Pembayaran">
                                <i class="bi bi-cash-coin"></i>
                            </a>
                            <a href="{{ route('admin.athletes.edit', $item->id) }}" class="btn-act btn-act-edit" title="Edit Siswa">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <a href="{{ route('admin.athletes.print-all') }}?id={{ $item->id }}" target="_blank" class="btn-act btn-act-print" title="Cetak ID Card">
                                <i class="bi bi-printer-fill"></i>
                            </a>
                            <form action="{{ route('admin.athletes.destroy', $item->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Yakin ingin menghapus data siswa ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-act btn-act-del" title="Hapus">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </form>
                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="py-4">
                            <i class="bi bi-person-x fs-1 text-muted opacity-50 d-block mb-3"></i>
                            <h6 class="fw-bold text-dark">Belum ada data siswa yang terdaftar</h6>
                            <p class="text-muted small mb-0">Gunakan tombol <strong>Tambah Siswa</strong> di atas untuk mulai menambahkan data siswa.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination yang Dirapikan --}}
    @if($athletes->hasPages())
    <div class="px-4 py-3 border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-3" style="background: #f8fafc;">
        <p class="text-muted small mb-0 text-center text-md-start">
            Menampilkan <strong class="text-dark">{{ $athletes->firstItem() }}</strong>–<strong class="text-dark">{{ $athletes->lastItem() }}</strong> dari <strong class="text-dark">{{ $athletes->total() }}</strong> siswa
        </p>
        <div class="pagination-wrapper">
            {{-- Menggunakan view pagination bootstrap-5 jika tersedia, atau fallback default yang sudah di-override --}}
            {{ $athletes->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @endif
</div>

{{-- ===================== MODAL IMPOR EXCEL ===================== --}}
<div class="modal fade" id="modalImportAthlete" tabindex="-1" aria-labelledby="modalImportAthleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">

            {{-- Header --}}
            <div class="modal-header border-0 py-3 px-4" style="background: linear-gradient(135deg, #0A192F, #1e3a5f);">
                <div class="d-flex align-items-center gap-3">
                    <span style="width: 38px; height: 38px; border-radius: 10px; background: rgba(255,255,255,.15); display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-cloud-upload-fill text-warning fs-5"></i>
                    </span>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0 fs-6" id="modalImportAthleteLabel">Import Data Siswa</h5>
                        <p class="text-white-50 mb-0" style="font-size: 0.75rem;">Upload file Excel untuk impor massal</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.athletes.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">

                    {{-- Info box --}}
                    <div class="rounded-3 p-3 mb-4 d-flex gap-3" style="background: #eff6ff; border: 1px solid #bfdbfe;">
                        <i class="bi bi-info-circle-fill text-primary mt-1 fs-6 flex-shrink-0"></i>
                        <div style="font-size: 0.825rem;">
                            <div class="fw-bold text-primary mb-1">Aturan Upload File</div>
                            <ul class="mb-0 ps-3 text-secondary">
                                <li>Format file: <strong>.xlsx</strong>, <strong>.xls</strong>, atau <strong>.csv</strong></li>
                                <li>Baris pertama harus berisi nama kolom (header)</li>
                                <li>Akun Wali Murid akan <strong>dibuat otomatis</strong> dari nama &amp; tanggal lahir</li>
                            </ul>
                        </div>
                    </div>

                    {{-- File input --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                            Pilih File Excel <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="file_excel" class="form-control" accept=".xlsx,.xls,.csv" required>
                    </div>

                    {{-- Column reference --}}
                    <div class="rounded-3 p-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <div class="fw-semibold text-dark mb-2" style="font-size: 0.825rem;">
                            <i class="bi bi-table text-secondary me-1"></i> Kolom Header yang Dibutuhkan
                        </div>
                        <div class="d-flex flex-wrap gap-1" style="font-size: 0.75rem;">
                            <code class="px-2 py-1 rounded" style="background: #dbeafe; color: #1d4ed8; font-weight: 700;">nama_siswa</code>
                            <code class="px-2 py-1 rounded" style="background: #f1f5f9; color: #475569;">nomor_punggung</code>
                            <code class="px-2 py-1 rounded" style="background: #f1f5f9; color: #475569;">posisi_bermain</code>
                            <code class="px-2 py-1 rounded" style="background: #dbeafe; color: #1d4ed8; font-weight: 700;">tanggal_lahir</code>
                            <span class="text-muted align-self-center" style="font-size: 0.7rem;">(YYYY-MM-DD)</span>
                            <code class="px-2 py-1 rounded" style="background: #dbeafe; color: #1d4ed8; font-weight: 700;">nomor_wa_ortu</code>
                            <code class="px-2 py-1 rounded" style="background: #f1f5f9; color: #475569;">nomor_wa_siswa</code>
                            <code class="px-2 py-1 rounded" style="background: #f1f5f9; color: #475569;">alamat</code>
                            <code class="px-2 py-1 rounded" style="background: #f1f5f9; color: #475569;">nama_orang_tua</code>
                        </div>
                        <p class="text-muted mb-0 mt-2" style="font-size: 0.725rem;">
                            <i class="bi bi-circle-fill text-primary" style="font-size: .4rem; vertical-align: middle;"></i>
                            Kolom berwarna biru = <strong>wajib diisi</strong>
                        </p>
                    </div>

                </div>

                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-sm fw-bold text-dark px-4 d-flex align-items-center gap-2">
                        <i class="bi bi-cloud-arrow-up-fill"></i> <span>Mulai Impor</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection