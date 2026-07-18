@extends('layouts.landing')
@section('title', 'Galeri Kegiatan | Superseed Academy')

@section('content')
<section class="py-20 bg-slate-50 border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center fade-in">
        <h1 class="text-3xl md:text-5xl font-black text-brand-navy mb-4 tracking-tight">Galeri Kegiatan</h1>
        <p class="text-slate-600 text-lg max-w-2xl mx-auto">
            Momen-momen berharga dalam setiap sesi latihan dan turnamen yang terekam kamera.
        </p>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            @forelse($galleries as $gallery)
            <div class="group relative aspect-square overflow-hidden rounded-xl bg-slate-100 fade-in border border-slate-200">
                <img src="{{ storage_img_url($gallery->foto) }}" alt="{{ $gallery->judul }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-brand-navy/90 via-brand-navy/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-5">
                    <h3 class="text-white font-bold text-lg leading-tight mb-1">{{ $gallery->judul }}</h3>
                    <span class="text-brand-light text-xs font-medium"><i class="bi bi-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($gallery->tanggal)->translatedFormat('d M Y') }}</span>
                    @if($gallery->deskripsi)
                    <p class="text-slate-300 text-xs mt-2 line-clamp-2">{{ $gallery->deskripsi }}</p>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-16 bg-slate-50 rounded-xl border border-slate-100">
                <i class="bi bi-images text-4xl text-slate-300 mb-3"></i>
                <p class="text-slate-500">Galeri foto belum tersedia.</p>
            </div>
            @endforelse
        </div>
        
        @if(isset($galleries) && $galleries->hasPages())
        <div class="mt-12 flex justify-center">
            {{ $galleries->links('pagination::tailwind') }}
        </div>
        @endif
    </div>
</section>
@endsection