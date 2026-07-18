@extends('layouts.landing')
@section('title', 'Prestasi | Superseed Academy')

@section('content')
<section class="py-20 bg-slate-50 border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center fade-in">
        <h1 class="text-3xl md:text-5xl font-black text-brand-navy mb-4 tracking-tight">Prestasi Kami</h1>
        <p class="text-slate-600 text-lg max-w-2xl mx-auto">
            Dedikasi dan kerja keras di lapangan membuahkan hasil. Inilah rekam jejak kebanggaan siswa-siswi Superseed Academy.
        </p>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($achievements as $achievement)
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-hover transition-shadow fade-in flex flex-col h-full">
                <div class="flex items-center gap-4 mb-4 pb-4 border-b border-slate-100">
                    <div class="w-16 h-16 rounded overflow-hidden bg-brand-light flex items-center justify-center shrink-0">
                        @if($achievement->foto)
                            <img src="{{ storage_img_url($achievement->foto) }}" alt="Ikon" class="w-full h-full object-cover">
                        @else
                            <i class="bi bi-award-fill text-brand-blue text-2xl"></i>
                        @endif
                    </div>
                    <div>
                        <div class="font-bold text-brand-navy text-lg leading-tight">{{ $achievement->judul }}</div>
                        <div class="text-brand-blue text-xs mt-1 font-semibold">{{ $achievement->tingkat ?: 'Penghargaan' }}</div>
                    </div>
                </div>
                <div class="flex-grow">
                    <p class="text-slate-700 leading-relaxed text-sm">
                        {{ $achievement->deskripsi }}
                    </p>
                </div>
                <div class="mt-4 pt-4 text-xs text-slate-400 font-semibold flex items-center gap-2">
                    <i class="bi bi-calendar2-check"></i> Diraih pada: {{ \Carbon\Carbon::parse($achievement->tanggal)->translatedFormat('d M Y') }}
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-16 bg-slate-50 rounded-xl border border-slate-100">
                <i class="bi bi-trophy text-4xl text-slate-300 mb-3"></i>
                <p class="text-slate-500">Belum ada data prestasi yang tercatat saat ini.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection