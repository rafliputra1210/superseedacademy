@extends('layouts.landing')
@section('title', 'Tim Pelatih | Superseed Academy')

@section('content')
<section class="py-20 bg-slate-50 border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center fade-in">
        <h1 class="text-3xl md:text-5xl font-black text-brand-navy mb-4 tracking-tight">Tim Pelatih Kami</h1>
        <p class="text-slate-600 text-lg max-w-2xl mx-auto">
            Berlatih di bawah bimbingan para ahli profesional yang telah berpengalaman dan mengantongi lisensi kepelatihan resmi.
        </p>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @forelse($coaches as $coach)
            <div class="bg-white border border-slate-100 rounded-xl overflow-hidden shadow-sm hover:shadow-hover transition-all duration-300 group fade-in">
                <div class="relative h-64 overflow-hidden bg-slate-100 flex items-center justify-center">
                    @if(isset($coach->foto) && $coach->foto)
                        <img src="{{ storage_img_url($coach->foto) }}" alt="{{ $coach->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <i class="bi bi-person-bounding-box text-6xl text-slate-300"></i>
                    @endif
                    <!-- License Badge -->
                    <div class="absolute top-4 right-4 bg-brand-blue text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">
                        Lisensi {{ $coach->lisensi ?? 'Resmi' }}
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-brand-navy mb-1">{{ $coach->nama }}</h3>
                    <p class="text-brand-blue font-semibold text-sm mb-4">{{ $coach->spesialisasi ?? 'Pelatih Utama' }}</p>
                    
                    <div class="flex items-center justify-between border-t border-slate-50 pt-4 mt-2">
                        <span class="text-slate-500 text-xs flex items-center gap-1">
                            <i class="bi bi-clock-history"></i> {{ $coach->pengalaman ?? '5+ Tahun' }} Exp.
                        </span>
                        <div class="flex gap-2">
                            <a href="#" class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:text-brand-blue hover:bg-brand-light transition-colors">
                                <i class="bi bi-instagram text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12 text-slate-500">
                Belum ada data pelatih.
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection