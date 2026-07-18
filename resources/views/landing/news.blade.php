@extends('layouts.landing')
@section('title', 'Berita & Artikel')

@section('content')
<div class="bg-gradient-to-r from-emerald-900 via-emerald-800 to-emerald-950 text-white py-16 px-4 text-center">
    <h1 class="text-3xl md:text-5xl font-extrabold uppercase tracking-tight">Kabar & Artikel Akademi</h1>
    <p class="text-emerald-200 mt-2 text-lg">Ikuti perkembangan turnamen, profil atlet, dan edukasi kepelatihan terkini</p>
    
    <!-- Form Search -->
    <form action="{{ route('landing.news') }}" method="GET" class="mt-6 max-w-md mx-auto flex">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari judul berita..." class="w-full px-4 py-3 rounded-l-full text-gray-900 text-sm focus:outline-none shadow">
        <button type="submit" class="bg-amber-500 hover:bg-amber-600 font-bold px-6 py-3 rounded-r-full text-gray-950 text-sm shadow transition">Cari</button>
    </form>
</div>

<div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @forelse($news as $item)
        <a href="{{ route('landing.news.detail', $item->slug) }}" class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100 hover:shadow-xl transition duration-300 flex flex-col justify-between group">
            <div>
                <div class="h-52 bg-gray-200 overflow-hidden relative">
                    @if($item->foto)
                        <img src="{{ storage_img_url($item->foto) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="{{ $item->judul }}">
                    @else
                        <div class="w-full h-full bg-emerald-900 flex items-center justify-center text-4xl">📰</div>
                    @endif
                    <span class="absolute top-3 left-3 bg-amber-500 text-gray-950 text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-wider shadow">
                        {{ $item->kategori }}
                    </span>
                </div>

                <div class="p-6">
                    <div class="flex items-center gap-3 text-xs text-gray-400 mb-2 font-medium">
                        <span>📅 {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</span>
                        <span>•</span>
                        <span>👁️ {{ $item->views }} x dilihat</span>
                    </div>
                    <h3 class="font-bold text-xl text-gray-900 group-hover:text-emerald-700 transition line-clamp-2 leading-snug">
                        {{ $item->judul }}
                    </h3>
                    <p class="text-gray-600 text-sm mt-2 line-clamp-3 leading-relaxed">
                        {{ strip_tags($item->konten) }}
                    </p>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center text-sm font-bold text-emerald-700">
                <span>Baca Selengkapnya &rarr;</span>
            </div>
        </a>
        @empty
        <div class="col-span-3 text-center py-16 bg-white rounded-2xl border border-dashed border-gray-300">
            <p class="text-gray-400 text-lg">Belum ada berita yang diterbitkan saat ini.</p>
        </div>
        @endforelse
    </div>
    
    <div class="mt-10">{{ $news->links() }}</div>
</div>
@endsection