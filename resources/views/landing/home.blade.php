@extends('layouts.landing')
@section('title', 'Superseed Academy | Sekolah Futsal Modern')

@section('content')

{{-- ===================== HERO SECTION ===================== --}}
<section class="relative bg-gradient-to-br from-emerald-900 via-teal-900 to-emerald-800 pt-20 pb-32 lg:pt-32 lg:pb-40 overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-emerald-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        <div class="absolute top-40 -left-20 w-72 h-72 bg-teal-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-20 left-1/2 w-80 h-80 bg-yellow-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-8 items-center">
            
            <!-- Text Content -->
            <div class="text-center lg:text-left fade-in">
                <span class="inline-flex items-center gap-2 py-1.5 px-4 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-yellow-300 text-sm font-semibold tracking-wide mb-8 shadow-[0_0_15px_rgba(250,204,21,0.3)]">
                    <span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span> Pendaftaran Angkatan 2024 Dibuka
                </span>
                
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight mb-6 tracking-tight drop-shadow-lg">
                    Bangun Fundamental <br class="hidden lg:block">
                    Futsal <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-amber-500">Terbaik.</span>
                </h1>
                
                <p class="text-lg text-emerald-50 mb-10 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-light text-justify">
                    Masa depan atlet hebat dimulai dari fondasi yang tepat. Di Super Seed Academy, Akademisi Anda akan berlatih dengan kurikulum pembinaan futsal berbasis Long-Term Athlete Development (LTAD), didampingi pelatih berlisensi, serta didukung fasilitas modern untuk mengembangkan teknik, karakter, kecerdasan bermain, dan mental juara. Saatnya memberikan proses pembinaan terbaik agar setiap Akademsi tumbuh menjadi atlet yang percaya diri dan siap menghadapi tantangan di dalam maupun di luar lapangan.
                </p>
                
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                    <a href="{{ route('landing.registration') }}" class="group relative w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-yellow-400 to-amber-500 hover:from-yellow-500 hover:to-amber-600 text-emerald-950 font-bold rounded-xl transition-all duration-300 text-center shadow-[0_0_20px_rgba(251,191,36,0.4)] hover:shadow-[0_0_30px_rgba(245,158,11,0.6)] transform hover:-translate-y-1">
                        <span class="flex items-center justify-center gap-2">
                            Daftar Sekarang <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </span>
                    </a>
                    <a href="{{ route('landing.coaches') }}" class="w-full sm:w-auto px-8 py-4 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white font-semibold rounded-xl transition-all duration-300 text-center transform hover:-translate-y-1">
                        Lihat Profil Pelatih
                    </a>
                </div>
            </div>

            <!-- Image Content (Banner Slider) -->
            <div class="relative flex justify-center lg:justify-end fade-in" style="transition-delay: 200ms;">
                <div class="relative w-full max-w-md">
                    <!-- Glassmorphism frame -->
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/30 to-teal-900/10 backdrop-blur-xl rounded-3xl transform translate-x-4 translate-y-4 -z-10 border border-white/10"></div>
                    
                    <!-- Slider Container -->
                    <div class="relative rounded-3xl shadow-2xl overflow-hidden w-full h-[400px] lg:h-[520px] border border-white/20 group" id="heroSlider">
                        <div class="absolute inset-0 bg-emerald-900/20 group-hover:bg-transparent transition-colors duration-500 z-10 pointer-events-none"></div>
                        @if(isset($banners) && $banners->count() > 0)
                            @foreach($banners as $index => $banner)
                                <img src="{{ storage_img_url($banner->image_path) }}" 
                                     alt="Superseed Academy Banner" 
                                     class="slider-image absolute inset-0 w-full h-full object-cover transition-all duration-1000 ease-in-out transform group-hover:scale-105 {{ $index === 0 ? 'opacity-100 z-0' : 'opacity-0 z-0' }}">
                            @endforeach
                        @else
                            <img src="https://images.unsplash.com/photo-1518605368461-1ee71c143926?q=80&w=800&auto=format&fit=crop" 
                                 alt="Pemain Futsal Anak" 
                                 class="absolute inset-0 w-full h-full object-cover opacity-100 z-0 transform group-hover:scale-105 transition-transform duration-1000">
                        @endif
                    </div>
                    
                    <!-- Stats Card Floating -->
                    <div class="absolute -bottom-6 -left-8 bg-white/95 backdrop-blur-lg border border-emerald-100 p-5 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] flex items-center gap-5 z-20 transform hover:-translate-y-2 transition-transform duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-yellow-300 to-amber-400 rounded-xl flex items-center justify-center shadow-inner">
                            <i class="bi bi-people-fill text-emerald-900 text-2xl"></i>
                        </div>
                        <div>
                            <div class="font-black text-3xl text-emerald-950 drop-shadow-sm">250+</div>
                            <div class="text-xs text-emerald-600 uppercase tracking-widest font-bold">Siswa Aktif</div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    
    <!-- Curved Bottom Divider -->
    <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none z-10">
        <svg class="relative block w-full h-[50px] lg:h-[100px]" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C59.71,118,152.47,143.22,321.39,56.44Z" class="fill-slate-50"></path>
        </svg>
    </div>
</section>

{{-- ===================== LOGO / TRUST SECTION ===================== --}}
<section class="py-10 bg-slate-50 fade-in relative z-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest mb-8">Fokus Utama Akademi Kami</p>
        <div class="flex flex-wrap justify-center items-center gap-8 md:gap-20">
            <div class="flex items-center gap-3 font-bold text-emerald-900 opacity-80 hover:opacity-100 hover:text-emerald-700 transition-colors group cursor-default">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <i class="bi bi-shield-check text-lg"></i> 
                </div>
                Lisensi Resmi
            </div>
            <div class="flex items-center gap-3 font-bold text-emerald-900 opacity-80 hover:opacity-100 hover:text-emerald-700 transition-colors group cursor-default">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <i class="bi bi-book text-lg"></i>
                </div> 
                Long-Term Athlete Development (LTAD)
            </div>
            <div class="flex items-center gap-3 font-bold text-emerald-900 opacity-80 hover:opacity-100 hover:text-emerald-700 transition-colors group cursor-default">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <i class="bi bi-activity text-lg"></i>
                </div>
                Pembinaan
            </div>
            <div class="flex items-center gap-3 font-bold text-emerald-900 opacity-80 hover:opacity-100 hover:text-emerald-700 transition-colors group cursor-default">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <i class="bi bi-person-badge text-lg"></i>
                </div>
                Pelatih Lisensi
            </div>
        </div>
    </div>
</section>

{{-- ===================== FEATURES SECTION ===================== --}}
<section class="py-24 bg-white fade-in relative">
    <!-- Subtle background pattern -->
    <div class="absolute inset-0 opacity-[0.02]" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'#064e3b\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-2xl mx-auto mb-20">
            <span class="inline-block py-1 px-3 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold tracking-widest uppercase mb-4 border border-emerald-100">
                Keunggulan
            </span>
            <h3 class="text-3xl md:text-5xl font-black text-emerald-950 mb-6 tracking-tight">Mengapa Memilih <span class="text-emerald-600">Kami?</span></h3>
            <p class="text-slate-500 text-lg">Program latihan terukur yang disesuaikan dengan tahapan usia, dipadukan dengan manajemen modern yang memanjakan siswa dan wali murid.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @php
                $features = [
                    ['icon' => 'bi-layout-text-window-reverse', 'title' => 'Portal Wali Murid', 'desc' => 'Sistem digital untuk memantau kehadiran, nilai raport latihan, dan pengumuman secara real-time.'],
                    ['icon' => 'bi-award', 'title' => 'Kurikulum Modern', 'desc' => 'Program latihan periodik berstandar Lisensi yang mencakup aspek teknis, taktis, fisik, dan mental.'],
                    ['icon' => 'bi-heart-pulse', 'title' => 'Pendampingan Medis', 'desc' => 'Penanganan cedera pertama dan pemantauan kondisi fisik siswa oleh tim medis dan fisioterapi.'],
                    ['icon' => 'bi-people', 'title' => 'Pelatih Pro', 'desc' => 'Dilatih oleh mantan pemain profesional yang memiliki lisensi kepelatihan resmi tingkat Asia.'],
                    ['icon' => 'bi-trophy', 'title' => 'Kompetisi Rutin', 'desc' => 'Siswa secara rutin diikutsertakan dalam liga regional maupun turnamen nasional untuk mengasah mental.'],
                    ['icon' => 'bi-building-check', 'title' => 'Fasilitas Premium', 'desc' => 'Penggunaan lapangan berstandar dengan peralatan latihan lengkap untuk mendukung progres siswa.'],
                ];
            @endphp

            @foreach($features as $f)
            <div class="group bg-white rounded-2xl p-8 hover:bg-emerald-700 transition-all duration-500 shadow-[0_5px_30px_-15px_rgba(0,0,0,0.1)] hover:shadow-2xl border border-emerald-50 transform hover:-translate-y-2">
                <div class="w-14 h-14 bg-emerald-50 rounded-xl flex items-center justify-center mb-6 group-hover:bg-yellow-400 transition-colors duration-300">
                    <i class="bi {{ $f['icon'] }} text-emerald-600 text-2xl group-hover:text-emerald-950 transition-colors duration-300"></i>
                </div>
                <h4 class="text-xl font-bold text-emerald-950 mb-3 group-hover:text-white transition-colors duration-300">{{ $f['title'] }}</h4>
                <p class="text-slate-500 text-sm leading-relaxed group-hover:text-emerald-50 transition-colors duration-300 text-justify">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== HOW IT WORKS SECTION ===================== --}}
<section class="py-24 bg-gradient-to-br from-emerald-900 to-teal-900 fade-in relative overflow-hidden">
    <!-- Overlay Pattern -->
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-yellow-400 via-transparent to-transparent"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-20">
            <span class="inline-block py-1 px-3 rounded-full bg-white/10 backdrop-blur-md text-yellow-300 text-xs font-bold tracking-widest uppercase mb-4 border border-yellow-300/30">
                Langkah Mudah
            </span>
            <h3 class="text-3xl md:text-5xl font-black text-white mb-4">Cara Bergabung Bersama Kami</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 md:gap-8 text-center relative">
            <!-- Connecting Line for Desktop -->
            <div class="hidden md:block absolute top-10 left-[10%] right-[10%] h-0.5 bg-gradient-to-r from-transparent via-yellow-500/50 to-transparent z-0"></div>

            @php
                $steps = [
                    ['num' => '1', 'title' => 'Daftar Online', 'desc' => 'Isi formulir pendaftaran melalui website kami'],
                    ['num' => '2', 'title' => 'Verifikasi', 'desc' => 'Lengkapi dokumen administrasi dan pembayaran'],
                    ['num' => '3', 'title' => 'Assessment', 'desc' => 'Ikuti sesi latihan perdana untuk evaluasi kemampuan'],
                    ['num' => '4', 'title' => 'Mulai Latihan', 'desc' => 'Penempatan kelas dan resmi menjadi siswa akademi'],
                ];
            @endphp

            @foreach($steps as $index => $step)
            <div class="relative z-10 px-4 group">
                <div class="w-20 h-20 mx-auto bg-emerald-950 border-2 border-emerald-400 text-white rounded-full flex items-center justify-center font-black text-3xl shadow-[0_0_20px_rgba(16,185,129,0.3)] mb-6 group-hover:scale-110 group-hover:bg-yellow-400 group-hover:border-yellow-300 group-hover:text-emerald-950 transition-all duration-300">
                    {{ $step['num'] }}
                </div>
                <h4 class="text-white font-bold text-xl mb-3 group-hover:text-yellow-300 transition-colors">{{ $step['title'] }}</h4>
                <p class="text-emerald-100/70 text-sm font-light">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== FILOSOFI & VISI ===================== --}}
<section class="py-24 bg-emerald-950 text-white fade-in relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'#ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-yellow-500 rounded-full mix-blend-overlay filter blur-3xl opacity-20"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-emerald-500 rounded-full mix-blend-overlay filter blur-3xl opacity-30"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Title Header -->
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-4xl md:text-5xl font-black mb-4 tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-amber-500">SUPER SEED ACADEMY</h2>
            <h3 class="text-2xl md:text-3xl font-bold mb-2">Grow the Seed, Lead the Game.</h3>
            <p class="text-lg text-emerald-200 font-light italic">Menumbuhkan Potensi, Memimpin Masa Depan.</p>
        </div>

        <!-- Main Philosophy Text -->
        <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-3xl p-8 md:p-12 mb-16 shadow-2xl leading-relaxed text-emerald-50 space-y-6 text-justify">
            <p>Di Super Seed Academy, kami percaya bahwa setiap Akademisi adalah benih luar biasa yang telah membawa potensi unik sejak langkah pertamanya. Potensi itu tidak lahir menjadi prestasi dengan sendirinya. Ia harus ditanam pada lingkungan yang tepat, dirawat dengan kesabaran, dibimbing dengan ilmu, dan ditempa melalui proses yang konsisten.</p>
            <p class="font-bold text-yellow-400 text-xl border-l-4 border-yellow-400 pl-4 py-1">Itulah makna "Grow the Seed."</p>
            <p>Kami tidak sekadar mengajarkan cara mengoper, menggiring, atau mencetak gol. Kami membangun fondasi karakter, kecerdasan berpikir, keberanian mengambil keputusan, disiplin, tanggung jawab, dan rasa hormat. Kami percaya bahwa seorang atlet hebat adalah mereka yang mampu menjadi teladan, baik di dalam maupun di luar lapangan.</p>
            <p>Ketika benih itu tumbuh dengan akar yang kuat, ia tidak hanya siap menghadapi pertandingan, tetapi juga siap menghadapi kehidupan.</p>
            <p class="font-bold text-yellow-400 text-xl border-l-4 border-yellow-400 pl-4 py-1">Itulah makna "Lead the Game."</p>
            <p>Memimpin permainan bukan sekadar menjadi pencetak gol atau kapten tim. Memimpin berarti mampu membaca situasi, mengambil keputusan dengan tepat, mengangkat semangat rekan setim, menghormati lawan, serta tetap rendah hati saat menang dan tetap tangguh saat kalah.</p>
            <p>Bagi kami, kemenangan bukanlah tujuan akhir. Kemenangan hanyalah hasil dari proses yang dilakukan dengan benar setiap hari. Karena itu, kami lebih menghargai perkembangan daripada sekadar hasil, karakter daripada popularitas, dan konsistensi daripada pencapaian sesaat.</p>
        </div>

        <!-- Pilar Filosofi SEED -->
        <div class="mb-20">
            <h3 class="text-3xl font-bold text-center mb-10 text-white">Pilar Filosofi Super Seed Academy</h3>
            <p class="text-center text-yellow-400 font-black text-2xl tracking-widest mb-12">S • E • E • D</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Skill -->
                <div class="bg-emerald-900/50 border border-emerald-700 p-6 rounded-2xl hover:bg-emerald-800 transition-colors">
                    <div class="w-14 h-14 rounded-full bg-emerald-700 flex items-center justify-center text-2xl font-black text-yellow-400 mb-4 shadow-lg">S</div>
                    <h4 class="text-xl font-bold mb-3 text-white">Skill</h4>
                    <p class="text-emerald-100 text-sm">Mengembangkan kemampuan teknik, taktik, fisik, dan kecerdasan bermain sesuai tahapan usia.</p>
                </div>
                <!-- Education -->
                <div class="bg-emerald-900/50 border border-emerald-700 p-6 rounded-2xl hover:bg-emerald-800 transition-colors">
                    <div class="w-14 h-14 rounded-full bg-emerald-700 flex items-center justify-center text-2xl font-black text-yellow-400 mb-4 shadow-lg">E</div>
                    <h4 class="text-xl font-bold mb-3 text-white">Education</h4>
                    <p class="text-emerald-100 text-sm">Menjadikan futsal sebagai media pembelajaran nilai-nilai kehidupan, kepemimpinan, komunikasi, dan tanggung jawab.</p>
                </div>
                <!-- Excellence -->
                <div class="bg-emerald-900/50 border border-emerald-700 p-6 rounded-2xl hover:bg-emerald-800 transition-colors">
                    <div class="w-14 h-14 rounded-full bg-emerald-700 flex items-center justify-center text-2xl font-black text-yellow-400 mb-4 shadow-lg">E</div>
                    <h4 class="text-xl font-bold mb-3 text-white">Excellence</h4>
                    <p class="text-emerald-100 text-sm">Mendorong setiap atlet untuk terus berkembang menjadi versi terbaik dirinya, bukan sekadar lebih baik dari orang lain.</p>
                </div>
                <!-- Discipline -->
                <div class="bg-emerald-900/50 border border-emerald-700 p-6 rounded-2xl hover:bg-emerald-800 transition-colors">
                    <div class="w-14 h-14 rounded-full bg-emerald-700 flex items-center justify-center text-2xl font-black text-yellow-400 mb-4 shadow-lg">D</div>
                    <h4 class="text-xl font-bold mb-3 text-white">Discipline</h4>
                    <p class="text-emerald-100 text-sm">Menanamkan kebiasaan positif, kerja keras, konsistensi, dan komitmen sebagai fondasi setiap prestasi.</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-start">
            <!-- Visi Pembinaan -->
            <div class="bg-emerald-900/50 border border-emerald-800 rounded-3xl p-8">
                <h3 class="text-2xl font-bold text-yellow-400 mb-6 flex items-center gap-3"><i class="bi bi-eye-fill"></i> Visi Pembinaan</h3>
                <p class="text-emerald-50 leading-relaxed mb-4 text-justify">Super Seed Academy hadir untuk membangun generasi atlet yang memiliki <span class="font-bold text-white">IQ bermain, EQ dalam bekerja sama, dan CQ (Character Quotient)</span> sebagai identitas utama.</p>
                <p class="text-emerald-50 leading-relaxed mb-4 text-justify">Kami tidak hanya ingin melahirkan pemain yang hebat, tetapi juga pemimpin yang mampu memberikan dampak positif bagi tim, keluarga, sekolah, dan masyarakat.</p>
                <p class="text-emerald-200 font-medium italic text-center">"Karena bagi kami, lapangan futsal hanyalah awal dari perjalanan yang lebih besar."</p>
            </div>

            <!-- Janji Kami -->
            <div class="bg-yellow-500 rounded-3xl p-8 text-emerald-950">
                <h3 class="text-2xl font-bold mb-6 flex items-center gap-3"><i class="bi bi-patch-check-fill"></i> Janji Kami</h3>
                <p class="leading-relaxed font-medium mb-6 text-justify">Kami berkomitmen membina setiap atlet melalui proses yang profesional, terukur, dan berkelanjutan dengan menjunjung tinggi integritas, sportivitas, dan semangat belajar tanpa henti.</p>
                <ul class="space-y-3 font-bold text-lg">
                    <li class="flex items-center gap-3"><i class="bi bi-check-circle-fill text-emerald-800"></i> Setiap latihan adalah investasi.</li>
                    <li class="flex items-center gap-3"><i class="bi bi-check-circle-fill text-emerald-800"></i> Setiap pertandingan adalah pembelajaran.</li>
                    <li class="flex items-center gap-3"><i class="bi bi-check-circle-fill text-emerald-800"></i> Setiap tantangan adalah kesempatan untuk bertumbuh.</li>
                </ul>
            </div>
        </div>

        <!-- Footer / Closing Statement -->
        <div class="mt-20 text-center border-t border-emerald-800 pt-16">
            <h2 class="text-3xl font-black text-white mb-2">SUPER SEED ACADEMY</h2>
            <h3 class="text-xl font-bold text-yellow-400 mb-6">Grow the Seed. Lead the Game.</h3>
            <p class="text-emerald-300 font-medium tracking-wide mb-10 text-sm uppercase">Menanam karakter • Mengembangkan potensi • Mencetak pemimpin • Membangun juara</p>
            
            <div class="inline-block bg-gradient-to-r from-emerald-800 to-teal-800 p-6 rounded-2xl border border-emerald-600 shadow-2xl transform hover:scale-105 transition-transform duration-300">
                <p class="text-sm font-bold text-emerald-300 uppercase tracking-widest mb-2">SLOGAN</p>
                <p class="text-xl md:text-2xl font-black text-white">Siapa Kita??? <span class="text-yellow-400">SATU KITA...!!! SATU CITA....!!!</span></p>
                <p class="text-2xl md:text-3xl font-black text-yellow-400 mt-2">SUPER SEED .... <span class="text-white">LUAR BIASA!!!</span></p>
            </div>
        </div>
    </div>
</section>

{{-- ===================== BERITA TERBARU ===================== --}}
@if(isset($recentNews) && $recentNews->count() > 0)
<section class="py-24 bg-slate-50 fade-in">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-12">
            <div>
                <span class="inline-block py-1 px-3 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold tracking-widest uppercase mb-4">
                    Berita & Informasi
                </span>
                <h3 class="text-3xl md:text-5xl font-black text-emerald-950 tracking-tight">Kabar <span class="text-emerald-600">Terbaru</span></h3>
            </div>
            <a href="{{ route('landing.news') }}" class="hidden sm:inline-flex items-center gap-2 text-emerald-700 font-bold hover:text-emerald-900 hover:gap-3 transition-all">
                Lihat Semua Berita <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($recentNews as $newsItem)
            <div class="bg-white rounded-3xl overflow-hidden hover:shadow-2xl transition-all duration-500 group flex flex-col border border-emerald-50 transform hover:-translate-y-2">
                <div class="relative h-56 overflow-hidden bg-slate-200">
                    @if($newsItem->foto)
                        <img src="{{ storage_img_url($newsItem->foto) }}" alt="{{ $newsItem->judul }}" class="w-full h-full object-cover group-hover:scale-110 group-hover:rotate-1 transition-transform duration-700">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-400">
                            <i class="bi bi-image text-5xl"></i>
                        </div>
                    @endif
                    <!-- Overlay gradient -->
                    <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <div class="absolute top-4 left-4 bg-yellow-400/95 backdrop-blur-sm px-4 py-1.5 rounded-full text-xs font-bold text-emerald-950 shadow-lg border border-yellow-300 z-10">
                        {{ $newsItem->kategori }}
                    </div>
                </div>
                <div class="p-8 flex flex-col flex-grow relative">
                    <div class="flex items-center gap-4 text-xs text-slate-400 font-bold mb-4 uppercase tracking-wider">
                        <span class="flex items-center gap-1.5"><i class="bi bi-calendar-event text-emerald-600"></i> {{ \Carbon\Carbon::parse($newsItem->tanggal)->format('d M Y') }}</span>
                    </div>
                    <h4 class="text-xl font-bold text-emerald-950 mb-4 line-clamp-2 group-hover:text-emerald-600 transition-colors leading-snug">
                        <a href="{{ route('landing.news.detail', $newsItem->slug) }}">{{ $newsItem->judul }}</a>
                    </h4>
                    <p class="text-slate-500 text-sm line-clamp-3 mb-6 flex-grow leading-relaxed">
                        {{ Str::limit(strip_tags($newsItem->konten), 120) }}
                    </p>
                    <a href="{{ route('landing.news.detail', $newsItem->slug) }}" class="inline-flex items-center gap-2 text-sm font-bold text-emerald-700 hover:text-emerald-900 transition-colors mt-auto group/btn">
                        Baca Selengkapnya <i class="bi bi-arrow-right group-hover/btn:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-12 text-center sm:hidden">
            <a href="{{ route('landing.news') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-50 text-emerald-700 rounded-full font-bold hover:bg-emerald-100 transition-colors">
                Lihat Semua Berita <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
@endif

{{-- ===================== TESTIMONIALS ===================== --}}
<section class="py-24 bg-white fade-in">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
            <span class="inline-block py-1 px-3 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold tracking-widest uppercase mb-4 border border-emerald-100">
                Testimoni
            </span>
            <h3 class="text-3xl md:text-5xl font-black text-emerald-950">Dipercaya Oleh Orang Tua</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @php
                $testimonials = [
                    ['name' => 'Bapak Budi', 'role' => 'Wali Murid U-12', 'text' => 'Akses informasi sangat transparan melalui Portal Wali. Saya bisa tahu perkembangan anak tiap bulan tanpa harus ke lapangan.'],
                    ['name' => 'Ibu Ratna', 'role' => 'Wali Murid U-10', 'text' => 'Kurikulumnya sangat tertata. Anak saya yang dulu belum mengerti dasar bermain bola sekarang memiliki pondasi passing dan dribbling yang baik.'],
                    ['name' => 'Bapak Andi', 'role' => 'Wali Murid U-15', 'text' => 'Pelatihnya komunikatif dan sangat profesional. Disiplin anak saya juga meningkat pesat sejak bergabung dengan Superseed.']
                ];
            @endphp

            @foreach($testimonials as $t)
            <div class="bg-white border border-emerald-50 rounded-3xl p-8 shadow-[0_5px_20px_-15px_rgba(0,0,0,0.1)] hover:shadow-2xl transition-all duration-300 relative group">
                <div class="absolute top-6 right-8 text-emerald-50 group-hover:text-emerald-100 transition-colors">
                    <i class="bi bi-quote text-5xl"></i>
                </div>
                <div class="flex gap-1 mb-6 relative z-10">
                    @for($i=0; $i<5; $i++)
                        <i class="bi bi-star-fill text-yellow-400 text-sm"></i>
                    @endfor
                </div>
                <p class="text-slate-600 mb-10 text-base leading-relaxed relative z-10 italic">"{{ $t['text'] }}"</p>
                <div class="flex items-center gap-4 border-t border-slate-50 pt-6 mt-auto">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-100 to-emerald-50 flex items-center justify-center font-bold text-emerald-700 text-lg">
                        {{ substr($t['name'], 6, 1) }}
                    </div>
                    <div>
                        <div class="text-emerald-950 font-bold text-sm uppercase tracking-wider">{{ $t['name'] }}</div>
                        <div class="text-slate-400 text-xs mt-1">{{ $t['role'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== CTA ===================== --}}
<section class="relative py-24 bg-gradient-to-br from-emerald-700 to-emerald-900 fade-in overflow-hidden">
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-72 h-72 rounded-full bg-yellow-400 opacity-20 blur-3xl"></div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-emerald-400 opacity-20 blur-3xl"></div>
    
    <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
        <h2 class="text-4xl md:text-5xl font-black text-white mb-6 drop-shadow-md">Siap Bergabung Bersama Kami?</h2>
        <p class="text-emerald-100 text-xl mb-12 max-w-2xl mx-auto font-light">
            Mulailah langkah pertama Akademisi Anda menuju futsal profesional. Pendaftaran terbuka untuk usia 8 hingga 16 tahun.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
            <a href="{{ route('landing.registration') }}" class="w-full sm:w-auto px-10 py-4 bg-yellow-400 text-emerald-950 font-black rounded-full hover:bg-yellow-300 transition-all shadow-[0_10px_25px_rgba(250,204,21,0.3)] hover:shadow-[0_15px_35px_rgba(250,204,21,0.4)] hover:-translate-y-1">
                Daftar Sekarang
            </a>
            <a href="https://wa.me/628888061522" class="w-full sm:w-auto px-10 py-4 bg-emerald-900/40 backdrop-blur-md text-white font-bold rounded-full border border-emerald-400/30 hover:bg-emerald-800 transition-all flex items-center justify-center gap-2 hover:-translate-y-1">
                <i class="bi bi-whatsapp text-green-400"></i> Hubungi Admin
            </a>
        </div>
    </div>
</section>

@push('scripts')
<style>
    /* Custom Animations for the new design */
    @keyframes blob {
        0% { transform: translate(0px, 0px) scale(1); }
        33% { transform: translate(30px, -50px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
        100% { transform: translate(0px, 0px) scale(1); }
    }
    .animate-blob {
        animation: blob 7s infinite;
    }
    .animation-delay-2000 {
        animation-delay: 2s;
    }
    .animation-delay-4000 {
        animation-delay: 4s;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const images = document.querySelectorAll('.slider-image');
        if (images.length > 1) {
            let currentIndex = 0;
            
            setInterval(() => {
                images[currentIndex].classList.remove('opacity-100', 'z-10');
                images[currentIndex].classList.add('opacity-0', 'z-0');
                
                currentIndex = (currentIndex + 1) % images.length;
                
                images[currentIndex].classList.remove('opacity-0', 'z-0');
                images[currentIndex].classList.add('opacity-100', 'z-10');
            }, 4000); 
        }
    });
</script>
@endpush
@endsection