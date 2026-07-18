@extends('layouts.wali')
@section('title', 'Asesmen & Observasi Lapangan')

@section('content')
@if(!$athlete)
    <div class="alert alert-warning text-center p-5 card-custom">Belum ada data anak terhubung dengan akun Anda.</div>
@else

<div class="card card-custom p-4 mb-4 bg-gradient text-white" style="background: linear-gradient(135deg, #064e3b 0%, #047857 100%);">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="mb-1 font-weight-bold">Form Asesmen Pemain & Observasi</h4>
            <span class="text-emerald-100 text-sm">Nama: <strong>{{ $athlete->nama }}</strong> | Posisi: <strong>{{ $athlete->posisi_bermain ?? '-' }}</strong></span>
        </div>
    </div>
</div>

<div class="space-y-6">
    @forelse($reports as $raport)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header Asesmen -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center flex-wrap gap-4">
            <div>
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">Tanggal Asesmen</span>
                <span class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($raport->tanggal_asesmen)->format('d F Y') }}</span>
            </div>
            <div>
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">Asesor / Pelatih</span>
                <span class="font-bold text-emerald-700">{{ $raport->asesor_nama }}</span>
            </div>
            <div class="text-right">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">No. Asesmen</span>
                <span class="font-mono text-sm bg-gray-200 px-2 py-1 rounded">{{ $raport->nomor_asesmen ?: '-' }}</span>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- KOLOM KIRI: Penilaian Skor (Progress Bar) -->
            <div class="space-y-6">
                <!-- B. Skill Teknis -->
                <div>
                    <h5 class="font-bold text-emerald-800 border-b-2 border-emerald-500 pb-2 mb-3">B. Penilaian Skill Teknis</h5>
                    <div class="space-y-3">
                        @foreach(json_decode($raport->aspek_teknis, true) ?? [] as $item)
                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1">
                                <span class="text-gray-700">{{ $item['aspek'] }}</span>
                                <span class="text-emerald-600">Skor: {{ $item['skor'] }}/5</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ ($item['skor'] / 5) * 100 }}%"></div>
                            </div>
                            @if($item['catatan']) <p class="text-[10px] text-gray-500 mt-1 italic">"{{ $item['catatan'] }}"</p> @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- C. Taktikal -->
                <div>
                    <h5 class="font-bold text-amber-600 border-b-2 border-amber-400 pb-2 mb-3">C. Penilaian Taktikal</h5>
                    <div class="space-y-3">
                        @foreach(json_decode($raport->aspek_taktikal, true) ?? [] as $item)
                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1">
                                <span class="text-gray-700">{{ $item['aspek'] }}</span>
                                <span class="text-amber-600">Skor: {{ $item['skor'] }}/5</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-amber-400 h-2 rounded-full" style="width: {{ ($item['skor'] / 5) * 100 }}%"></div>
                            </div>
                            @if($item['catatan']) <p class="text-[10px] text-gray-500 mt-1 italic">"{{ $item['catatan'] }}"</p> @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: Fisik/Mental & Observasi Lapangan -->
            <div class="space-y-6">
                <!-- D. Fisik & Mental -->
                <div>
                    <h5 class="font-bold text-blue-600 border-b-2 border-blue-400 pb-2 mb-3">D. Penilaian Fisik & Mental</h5>
                    <div class="space-y-3">
                        @foreach(json_decode($raport->aspek_fisik_mental, true) ?? [] as $item)
                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1">
                                <span class="text-gray-700">{{ $item['aspek'] }}</span>
                                <span class="text-blue-600">Skor: {{ $item['skor'] }}/5</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-400 h-2 rounded-full" style="width: {{ ($item['skor'] / 5) * 100 }}%"></div>
                            </div>
                            @if($item['catatan']) <p class="text-[10px] text-gray-500 mt-1 italic">"{{ $item['catatan'] }}"</p> @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- E. Observasi Lapangan -->
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 h-full">
                    <h5 class="font-bold text-gray-900 border-b-2 border-gray-300 pb-2 mb-4">E. Observasi Lapangan (Kualitatif)</h5>
                    
                    <div class="mb-4">
                        <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider flex items-center gap-1"><i class="bi bi-graph-up-arrow"></i> Kekuatan Utama (Strengths)</span>
                        <p class="text-sm text-gray-700 mt-1 leading-relaxed">{{ $raport->kekuatan_utama ?: '-' }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <span class="text-xs font-bold text-red-500 uppercase tracking-wider flex items-center gap-1"><i class="bi bi-graph-down-arrow"></i> Area Peningkatan (Weaknesses)</span>
                        <p class="text-sm text-gray-700 mt-1 leading-relaxed">{{ $raport->area_peningkatan ?: '-' }}</p>
                    </div>
                    
                    <div>
                        <span class="text-xs font-bold text-blue-600 uppercase tracking-wider flex items-center gap-1"><i class="bi bi-person-lines-fill"></i> Catatan Perilaku di Lapangan</span>
                        <p class="text-sm text-gray-700 mt-1 leading-relaxed italic border-l-2 border-blue-400 pl-3">"{{ $raport->catatan_perilaku ?: '-' }}"</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-12 bg-white rounded-xl border border-dashed border-gray-300 text-gray-400">
        <div class="text-5xl mb-3">📄</div>
        <h6 class="font-bold text-gray-600">Belum Ada Hasil Asesmen</h6>
        <p class="text-sm">Pelatih belum menginput Form Asesmen & Observasi untuk siswa ini.</p>
    </div>
    @endforelse
</div>
@endif
@endsection