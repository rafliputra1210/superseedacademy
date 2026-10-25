@extends('layouts.admin') <!-- Sesuaikan dengan layout admin Anda -->

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="font-weight-bold">Rekapitulasi Nilai Asesor</h3>
        <span class="badge bg-primary text-white" style="font-size: 1rem;">Periode: {{ $periodeSekarang }}</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead class="bg-dark text-white">
                    <tr>
                        <th rowspan="2" class="align-middle">No</th>
                        <th rowspan="2" class="align-middle text-start">Nama Atlet</th>
                        <th colspan="{{ $assessors->count() }}">Status Penilaian Asesor</th>
                        <th rowspan="2" class="align-middle">Aksi Raport</th>
                    </tr>
                    <tr>
                        @foreach($assessors as $asesor)
                            <th>{{ $asesor->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($athletes as $index => $atlet)
                        @php
                            // Ambil nilai khusus untuk atlet ini di baris tabel ini
                            $nilaiAtletIni = $allScores->where('athlete_id', $atlet->id);
                            $jumlahDinilai = $nilaiAtletIni->count();
                        @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-start font-weight-bold">{{ $atlet->nama }}</td>
                        
                        <!-- Pengecekan per Asesor -->
                        @foreach($assessors as $asesor)
                            @php
                                $sudahDinilai = $nilaiAtletIni->where('assessor_id', $asesor->id)->first();
                            @endphp
                            <td>
                                @if($sudahDinilai)
                                    <span class="text-success"><i class="bi bi-check-circle-fill"></i> Selesai</span>
                                @else
                                    <span class="text-danger"><i class="bi bi-x-circle"></i> Belum</span>
                                @endif
                            </td>
                        @endforeach

                        <!-- Tombol Generate Raport -->
                        <td>
                            <form action="{{ route('admin.reports.generate-final', $atlet->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="periode_bulan" value="{{ $periodeSekarang }}">
                                <button type="submit" class="btn btn-sm {{ $jumlahDinilai == $assessors->count() ? 'btn-success' : 'btn-warning' }}" 
                                    onclick="return confirm('Generate nilai final dari {{ $jumlahDinilai }} asesor yang sudah masuk?')">
                                    <i class="bi bi-cpu"></i> Generate Raport
                                </button>
                            </form>
                            <small class="d-block mt-1 text-muted">{{ $jumlahDinilai }}/{{ $assessors->count() }} Asesor masuk</small>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection