HTML
@extends('layouts.landing')
@section('title', $news->judul)

@section('content')
<!-- BANNER FOTO UTAMA -->
<div class="relative bg-gray-900 text-white py-24 px-4 text-center overflow-hidden">
    @if($news->foto)
        <img src="{{ storage_img_url($news->foto) }}" class="absolute inset-0 w-full h-full object-cover opacity-30 filter blur-sm scale-105" alt="Banner">
    @endif
    <div class="relative z-10 max-w-4xl mx-auto">
        <span class="bg-amber-500 text-gray-950 font-extrabold text-xs px-3 py-1.5 rounded-full uppercase tracking-widest inline-block mb-4 shadow">
            {{ $news->kategori }}
        </span>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight tracking-tight mb-4">
            {{ $news->judul }}
        </h1>
        <div class="flex items-center justify-center gap-4 text-sm text-gray-300 font-medium">
            <span>📅 {{ \Carbon\Carbon::parse($news->tanggal)->format('d M Y') }}</span>
            <span>•</span>
            <span>👁️ {{ $news->views }} Kali Dilihat</span>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10 -mt-16 relative z-20">
        
        <!-- FOTO DALAM ARTIKEL -->
        @if($news->foto)
            <div class="rounded-xl overflow-hidden mb-8 shadow-md">
                <img src="{{ storage_img_url($news->foto) }}" class="w-full max-h-[450px] object-cover" alt="{{ $news->judul }}">
            </div>
        @endif

        <!-- KONTEN ARTIKEL -->
        <div class="prose max-w-none text-gray-700 leading-relaxed text-base md:text-lg whitespace-pre-line font-normal space-y-4 border-b border-gray-100 pb-8 text-justify hyphens-auto">

            {{ $news->konten }}
        </div>

        <!-- FITUR SHARE SOSIAL MEDIA -->
        <div class="py-6 flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-gray-100">
            <span class="font-bold text-gray-900 text-sm flex items-center gap-2">
                <span>🚀</span> Bagikan Artikel Ini:
            </span>
            <div class="flex flex-wrap gap-2">
                <!-- Share WhatsApp -->
                <a href="https://api.whatsapp.com/send?text={{ urlencode($news->judul . ' - Baca di: ' . url()->current()) }}" target="_blank" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-1 shadow-sm">
                    💬 WhatsApp
                </a>
                <!-- Copy Link Button -->
                <button onclick="copyArticleLink()" id="btnCopy" 
                        class="bg-gray-100 hover:bg-gray-200 text-gray-800 border border-gray-300 px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-1">
                    🔗 <span id="textCopy">Salin Link</span>
                </button>
            </div>
        </div>

        <!-- FITUR KOMENTAR INTERAKTIF -->
        <div class="pt-8">
            <div class="text-center py-8 text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200 text-sm">
                Fitur komentar sedang dalam tahap pengembangan.
            </div>
        </div>

    </div>
</div>

<!-- Script Copy Link -->
<script>
    function copyArticleLink() {
        navigator.clipboard.writeText(window.location.href).then(function() {
            const textCopy = document.getElementById('textCopy');
            textCopy.innerText = 'Tersalin! ✅';
            textCopy.classList.add('text-green-600');
            setTimeout(() => {
                textCopy.innerText = 'Salin Link';
                textCopy.classList.remove('text-green-600');
            }, 2000);
        });
    }
</script>
@endsection