<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#064e3b">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="Super Seed Academy">
    
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Super Seed Academy">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Seed Academy - @yield('title', 'Akademi futsal')</title>
    <meta name="description" content="Pusat pembinaan Futsal usia dini dan muda yang profesional dan terstruktur.">
    
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            blue: '#059669', /* Emerald 600 - renamed internally to green but keeping class name for compatibility */
                            navy: '#022c22', /* Emerald 950 */
                            light: '#ecfdf5', /* Emerald 50 */
                            gray: '#64748B'
                        }
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'hover': '0 10px 30px -5px rgba(5, 150, 105, 0.1)',
                    }
                }
            }
        }
    </script>
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #ffffff; color: #334155; }
        
        /* Navbar Scrolled State */
        #navbar { transition: all 0.3s ease; }
        .navbar-scrolled {
            background-color: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(8px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }

        /* Clean Link Hover */
        .nav-link { position: relative; color: #475569; transition: color 0.2s ease; font-weight: 500; }
        .nav-link:hover, .nav-link.active { color: #059669; }
        .nav-link::after {
            content: ''; position: absolute; width: 0; height: 2px; bottom: -4px; left: 0;
            background-color: #facc15; /* Yellow 400 */
            transition: width 0.2s ease;
        }
        .nav-link.active::after { width: 100%; }
        .nav-link:hover::after { width: 100%; }

        /* Minimal Dropdown Mobile */
        #mobile-menu {
            transition: max-height 0.3s ease, opacity 0.3s ease;
            max-height: 0; opacity: 0; overflow: hidden;
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
        }
        #mobile-menu.open {
            max-height: 500px; opacity: 1;
        }

        /* Simple Fade In */
        .fade-in { opacity: 0; transform: translateY(15px); transition: opacity 0.5s ease, transform 0.5s ease; }
        .fade-in.visible { opacity: 1; transform: translateY(0); }
    </style>
    @stack('styles')
</head>
<body class="antialiased flex flex-col min-h-screen">

    <!-- NAVBAR -->
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-slate-100 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-10">
                
                <!-- Logo -->
                <a href="{{ route('landing.home') }}" class="flex items-center gap-2">
    <!-- Mengganti div icon lama dengan tag img -->
    <img src="{{ asset('images/logo.png') }}" alt="Superseed Academy Logo" class="h-8 w-auto object-contain">
    
    <div class="flex flex-col justify-center">
        <span class="font-bold text-brand-navy leading-none tracking-tight text-lg">SUPER SEED</span>
        <span class="text-[10px] text-brand-gray font-semibold tracking-widest uppercase">Academy</span>
    </div>
</a>

                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center gap-8">
                    @php
                        $navItems = [
                            (object)['name' => 'landing.home', 'label' => 'Beranda'],
                            (object)['name' => 'landing.news', 'label' => 'Berita'],
                            (object)['name' => 'landing.coaches', 'label' => 'Pelatih'],
                            (object)['name' => 'landing.schedule', 'label' => 'Jadwal'],
                            (object)['name' => 'landing.achievements', 'label' => 'Prestasi'],
                            (object)['name' => 'landing.gallery', 'label' => 'Galeri'],
                        ];
                    @endphp
                    @foreach($navItems as $item)
                        <a href="{{ route($item->name) }}" class="nav-link text-sm {{ request()->routeIs($item->name) ? 'active' : '' }}">
                            {{ $item->label }}
                        </a>
                    @endforeach
                </div>

                <!-- Desktop CTA -->
                <div class="hidden md:flex items-center gap-4">
                    <a href="{{ route('login') }}" class="px-5 py-2.5 bg-yellow-400 hover:bg-yellow-500 text-emerald-950 text-sm font-bold rounded-md transition-colors shadow-sm">
                        Masuk
                    </a>
                </div>

                <!-- Mobile Toggle -->
                <button id="hamburger-btn" class="md:hidden text-brand-navy p-2 focus:outline-none">
                    <i id="hamburger-icon" class="bi bi-list text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu (Dropdown) -->
        <div id="mobile-menu" class="md:hidden absolute top-full left-0 w-full shadow-soft">
            <div class="px-4 py-4 flex flex-col gap-3">
                @foreach($navItems as $item)
                    <a href="{{ route($item->name) }}" class="text-base font-medium text-slate-700 hover:text-brand-blue py-2 border-b border-slate-50">
                        {{ $item->label }}
                    </a>
                @endforeach
                <a href="{{ route('landing.registration') }}" class="text-base font-medium text-slate-700 hover:text-brand-blue py-2 border-b border-slate-50">Pendaftaran</a>
                <div class="pt-4 flex flex-col gap-3">
                    <a href="{{ route('login') }}" class="w-full text-center py-3 bg-yellow-400 hover:bg-yellow-500 rounded-md text-emerald-950 font-bold">Masuk Akun</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="flex-grow pt-24">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="bg-brand-navy text-white pt-12 pb-8 mt-20 border-t-4 border-yellow-400 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'#ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12 text-center md:text-left">
                
                <!-- Column 1: Brand -->
                <div class="flex flex-col items-center md:items-start gap-4">
                    <a href="{{ route('landing.home') }}" class="flex items-center gap-2">
                        <img src="{{ asset('images/logo.png') }}" alt="Superseed Academy Logo" class="h-12 w-auto object-contain drop-shadow-md">
                        <div class="flex flex-col justify-center text-left">
                            <span class="font-black text-white leading-none tracking-tight text-xl">SUPER SEED</span>
                            <span class="text-[11px] text-yellow-400 font-bold tracking-widest uppercase">Academy</span>
                        </div>
                    </a>
                    <p class="text-emerald-50/70 text-sm max-w-xs leading-relaxed">
                        Pusat pembinaan Futsal usia dini dan muda yang profesional dan terstruktur.
                    </p>
                </div>

                <!-- Column 2: Social Media -->
                <div class="flex flex-col items-center md:items-start gap-4">
                    <h4 class="text-sm font-bold text-yellow-400 uppercase tracking-wider">Media Sosial</h4>
                    <p class="text-emerald-50/70 text-sm max-w-xs leading-relaxed">
                        Ikuti perkembangan dan kegiatan terbaru kami melalui media sosial.
                    </p>
                    <div class="flex gap-4">
                        <a href="https://www.instagram.com/super_seed_academy?igsh=MjY4eHRtMnd5bDA3" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-emerald-800/40 hover:bg-yellow-400 hover:text-emerald-950 flex items-center justify-center text-lg transition-all duration-300 text-white" title="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="https://www.tiktok.com/@super_seed_academy" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-emerald-800/40 hover:bg-yellow-400 hover:text-emerald-950 flex items-center justify-center text-lg transition-all duration-300 text-white" title="TikTok">
                            <i class="bi bi-tiktok"></i>
                        </a>
                        <a href="https://wa.me/628888061522" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-emerald-800/40 hover:bg-yellow-400 hover:text-emerald-950 flex items-center justify-center text-lg transition-all duration-300 text-white" title="WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    </div>
                </div>

                <!-- Column 3: Links & Copyright -->
                <div class="flex flex-col items-center md:items-start gap-4">
                    <h4 class="text-sm font-bold text-yellow-400 uppercase tracking-wider">Tautan Cepat</h4>
                    <div class="flex flex-col gap-2 text-sm font-medium">
                        <a href="#" class="text-emerald-50/70 hover:text-yellow-400 transition-colors">Kebijakan Privasi</a>
                        <a href="#" class="text-emerald-50/70 hover:text-yellow-400 transition-colors">Syarat Ketentuan</a>
                    </div>
                    <div class="pt-2 border-t border-emerald-800/40 w-full text-center md:text-left">
                        <p class="text-emerald-50/50 text-xs">
                            &copy; {{ date('Y') }} <span class="text-emerald-50/80 font-semibold">Super Seed Academy</span>. All rights reserved.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </footer>

    <!-- PWA Install Button -->
    <div class="fixed bottom-6 right-6 z-50">
        <button id="btnInstallPwa" class="hidden bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-extrabold px-4 py-2 rounded-full shadow-md transition transform active:scale-95 flex items-center gap-1.5 border border-green-400 text-sm animate-pulse">
            <span>📲</span> Install Aplikasi
        </button>
    </div>

    <!-- SCRIPTS -->
    <script>
    let deferredPrompt;
    const btnInstall = document.getElementById('btnInstallPwa');

    window.addEventListener('beforeinstallprompt', (e) => {
        // Cegah Chrome memunculkan prompt otomatis yang terlalu cepat
        e.preventDefault();
        // Simpan event agar bisa dipicu saat tombol diklik
        deferredPrompt = e;
        // Munculkan tombol Install di layar
        if (btnInstall) btnInstall.classList.remove('hidden');
    });

    if (btnInstall) {
        btnInstall.addEventListener('click', () => {
            // Sembunyikan tombol lagi setelah diklik
            btnInstall.classList.add('hidden');
            // Munculkan dialog konfirmasi install dari browser
            deferredPrompt.prompt();
            // Tunggu respon pengguna (setuju install atau batal)
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('🎉 Pengguna menyetujui instalasi PWA');
                } else {
                    console.log('❌ Pengguna menolak instalasi PWA');
                }
                deferredPrompt = null;
            });
        });
    }

    // Sembunyikan tombol jika aplikasi sudah berhasil terinstall
    window.addEventListener('appinstalled', () => {
        if (btnInstall) btnInstall.classList.add('hidden');
        console.log('✅ Aplikasi Super Seed Academy berhasil diinstall!');
    });

        // Navbar Scrolled
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 10) {
                navbar.classList.add('navbar-scrolled');
                navbar.classList.remove('py-4');
            } else {
                navbar.classList.remove('navbar-scrolled');
                navbar.classList.add('py-4');
            }
        });

        // Mobile Menu Toggle
        const btn = document.getElementById('hamburger-btn');
        const menu = document.getElementById('mobile-menu');
        const icon = document.getElementById('hamburger-icon');

        btn.addEventListener('click', () => {
            menu.classList.toggle('open');
            if(menu.classList.contains('open')) {
                icon.classList.remove('bi-list');
                icon.classList.add('bi-x-lg');
            } else {
                icon.classList.remove('bi-x-lg');
                icon.classList.add('bi-list');
            }
        });

        // Simple Intersection Observer for fade-in elements
        const observerOptions = { threshold: 0.1, rootMargin: "0px 0px -20px 0px" };
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
    </script>
    @stack('scripts')
<!-- PWA Service Worker Registration -->
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').then(reg => {
                console.log('Service Worker registered!', reg);
            }).catch(err => {
                console.log('Service Worker registration failed:', err);
            });
        });
    }
</script>
</body>
</html>