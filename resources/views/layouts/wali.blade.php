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
    <title>Portal Wali Murid - Super Seed Academy</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --brand-green: #047857; /* Emerald 700 */
            --brand-green-light: #d1fae5; /* Emerald 100 */
            --brand-gold: #f59e0b; /* Amber 500 */
            --brand-navy: #0f172a; /* Slate 900 */
            --brand-light: #f8fafc; /* Slate 50 */
            --brand-gray: #64748b; /* Slate 500 */
            
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.3);
        }
        
        body { 
            background-color: #f1f5f9; /* Slate 100 */
            font-family: 'Outfit', sans-serif; 
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }

        /* Tipografi Helper */
        .font-inter { font-family: 'Inter', sans-serif; }

        /* Navbar Custom (Glassmorphism) */
        .navbar-custom { 
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 20px -2px rgba(0,0,0,0.03); 
        }
        
        .nav-link-custom { 
            color: var(--brand-gray) !important; 
            font-weight: 600; 
            padding: 8px 18px !important; 
            border-radius: 10px; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            font-size: 0.95rem;
        }
        
        .nav-link-custom:hover, .nav-link-custom.active { 
            background: var(--brand-green-light); 
            color: var(--brand-green) !important; 
        }

        /* Card Custom (Premium Floating) */
        .card-custom { 
            border: 1px solid rgba(226, 232, 240, 0.8); 
            border-radius: 16px; 
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03), 0 8px 10px -6px rgba(0,0,0,0.01); 
            background: #ffffff; 
            transition: all 0.3s ease;
        }

        /* Animasi Interaktif */
        .hover-elevate { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .hover-elevate:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
        }

        /* Utilitas Warna Baru */
        .text-brand-green { color: var(--brand-green) !important; }
        .text-brand-navy { color: var(--brand-navy) !important; }
        .text-brand-gold { color: var(--brand-gold) !important; }
        .bg-brand-green { background-color: var(--brand-green) !important; }
        .bg-brand-light { background-color: var(--brand-light) !important; }
        
        .badge-posisi { background: var(--brand-gold); color: var(--brand-navy); font-weight: 800; font-size: 0.85rem;}
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom py-3 sticky-top">
        <div class="container" style="max-width: 1140px;">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2 text-brand-navy font-inter" style="letter-spacing: -0.5px;" href="{{ route('wali.dashboard') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Superseed Logo" class="me-2" style="height: 28px; width: auto; object-fit: contain;">
                Super Seed Academy <span class="badge bg-brand-light border text-brand-gray ms-1 fw-medium" style="font-size: 0.65rem;">WALI</span>
            </a>
            
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-1 text-brand-navy"></i>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto gap-2 align-items-lg-center mt-3 mt-lg-0">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('wali.dashboard') ? 'active' : '' }}" href="{{ route('wali.dashboard') }}">
                            <i class="bi bi-person-badge me-1"></i> Biodata
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('wali.absensi') ? 'active' : '' }}" href="{{ route('wali.absensi') }}">
                            <i class="bi bi-calendar-check-fill me-1"></i> Absensi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('wali.raport') ? 'active' : '' }}" href="{{ route('wali.raport') }}">
                            <i class="bi bi-award-fill me-1"></i> Raport
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('wali.keuangan') ? 'active' : '' }}" href="{{ route('wali.keuangan') }}">
                            <i class="bi bi-wallet2 me-1"></i> Uang Kas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('wali.pengumuman') ? 'active' : '' }}" href="{{ route('wali.pengumuman') }}">
                            <i class="bi bi-megaphone-fill me-1"></i> Pengumuman
                        </a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm px-4 py-2 w-100 fw-semibold text-white border-0 shadow-sm rounded-pill hover-elevate" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                                <i class="bi bi-box-arrow-right me-1"></i> Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container py-4 flex-grow-1" style="max-width: 1140px;">
        <!-- Banner Selamat Datang -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 bg-white p-4 rounded-4 border shadow-sm border-start border-4 hover-elevate" style="border-left-color: var(--brand-green) !important;">
            <div class="mb-2 mb-md-0">
                <span class="text-secondary small fw-medium text-uppercase tracking-wide" style="letter-spacing: 1px;">Selamat Datang di Portal Wali,</span>
                <h4 class="mb-0 fw-bold text-brand-navy mt-1">{{ Auth::user()->name }}</h4>
            </div>
            <span class="badge bg-brand-light text-brand-green border px-3 py-2 rounded-pill shadow-sm d-flex align-items-center gap-1">
                <i class="bi bi-shield-check fs-6"></i> <span class="fw-semibold">Akun Terverifikasi</span>
            </span>
        </div>

        @yield('content')
    </main>

    <!-- Footer Premium -->
    <footer class="mt-auto" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #94a3b8; padding: 2.5rem 0 1.25rem;">
        <div class="container" style="max-width: 1140px;">

            <!-- Baris Utama -->
            <div class="row g-4 pb-4" style="border-bottom: 1px solid rgba(255,255,255,0.08);">

                <!-- Kolom Brand -->
                <div class="col-md-5">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ asset('images/logo.png') }}"
                             alt="Super Seed Academy Logo"
                             style="height: 52px; width: 52px; object-fit: contain; border-radius: 12px; background: rgba(255,255,255,0.05); padding: 4px;">
                        <div>
                            <div style="font-weight: 800; color: #fff; font-size: 1.1rem; letter-spacing: 0.5px; line-height: 1.1;">SUPER SEED</div>
                            <div style="font-size: 0.72rem; color: #64748b; letter-spacing: 1.5px; text-transform: uppercase; margin-top: 2px;">ACADEMY</div>
                        </div>
                    </div>
                    <p style="font-size: 0.82rem; line-height: 1.7; color: #64748b; margin: 0; max-width: 300px;">
                        Portal Transparansi & Akademik resmi untuk orang tua/wali murid Super Seed Academy.
                    </p>
                </div>

                <!-- Kolom Navigasi -->
                <div class="col-md-3 offset-md-1">
                    <div style="font-weight: 700; color: #e2e8f0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem;">Menu Portal</div>
                    <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.6rem;">
                        <li><a href="{{ route('wali.dashboard') }}" style="color: #64748b; text-decoration: none; font-size: 0.83rem; transition: color .2s;" onmouseover="this.style.color='#10b981'" onmouseout="this.style.color='#64748b'"><i class="bi bi-person-badge me-2"></i>Biodata Siswa</a></li>
                        <li><a href="{{ route('wali.absensi') }}" style="color: #64748b; text-decoration: none; font-size: 0.83rem; transition: color .2s;" onmouseover="this.style.color='#10b981'" onmouseout="this.style.color='#64748b'"><i class="bi bi-calendar-check-fill me-2"></i>Absensi</a></li>
                        <li><a href="{{ route('wali.raport') }}" style="color: #64748b; text-decoration: none; font-size: 0.83rem; transition: color .2s;" onmouseover="this.style.color='#10b981'" onmouseout="this.style.color='#64748b'"><i class="bi bi-award-fill me-2"></i>Raport</a></li>
                        <li><a href="{{ route('wali.keuangan') }}" style="color: #64748b; text-decoration: none; font-size: 0.83rem; transition: color .2s;" onmouseover="this.style.color='#10b981'" onmouseout="this.style.color='#64748b'"><i class="bi bi-wallet2 me-2"></i>Uang Kas</a></li>
                        <li><a href="{{ route('wali.pengumuman') }}" style="color: #64748b; text-decoration: none; font-size: 0.83rem; transition: color .2s;" onmouseover="this.style.color='#10b981'" onmouseout="this.style.color='#64748b'"><i class="bi bi-megaphone-fill me-2"></i>Pengumuman</a></li>
                    </ul>
                </div>

                <!-- Kolom Info -->
                <div class="col-md-3">
                    <div style="font-weight: 700; color: #e2e8f0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem;">Info Akademi</div>
                    <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.75rem;">
                        <li style="display:flex;align-items:flex-start;gap:8px;">
                            <i class="bi bi-geo-alt-fill" style="color: #10b981; margin-top: 2px; flex-shrink:0;"></i>
                            <span style="font-size: 0.8rem; color: #64748b; line-height: 1.5;">Superseed Academy, FKC 2024</span>
                        </li>
                        <li style="display:flex;align-items:center;gap:8px;">
                            <i class="bi bi-shield-fill-check" style="color: #10b981; flex-shrink:0;"></i>
                            <span style="font-size: 0.8rem; color: #64748b;">Data Siswa Terlindungi</span>
                        </li>
                        <li style="display:flex;align-items:center;gap:8px;">
                            <i class="bi bi-lock-fill" style="color: #10b981; flex-shrink:0;"></i>
                            <span style="font-size: 0.8rem; color: #64748b;">Akses Terenkripsi & Aman</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Baris Copyright -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-3">
                <span style="font-size: 0.75rem; color: #475569;">
                    &copy; {{ date('Y') }} <strong style="color:#64748b;">Super Seed Academy</strong>. Hak Cipta Dilindungi.
                </span>
                <span style="font-size: 0.72rem; color: #334155;">
                    Portal Akademik &mdash; <span style="color: #10b981; font-weight: 600;">Laravel 12</span>
                </span>
            </div>

        </div>
    </footer>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SUCCESS TOAST NOTIFICATION -->
    @if(session('success'))
    <div id="successToast" style="
        position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 99999;
        background: #ffffff; border-radius: 16px; padding: 1rem 1.25rem;
        box-shadow: 0 20px 50px rgba(0,0,0,0.15); border-left: 4px solid #10b981;
        display: flex; align-items: center; gap: 0.75rem; min-width: 300px;
        animation: toastSlideIn 0.4s cubic-bezier(0.34,1.56,0.64,1);
        font-family: 'Inter', sans-serif;
    ">
        <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#d1fae5,#6ee7b7);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-check-lg" style="color:#065f46;font-size:1.1rem;font-weight:800;"></i>
        </div>
        <div style="flex:1;">
            <div style="font-weight:700;color:#0a192f;font-size:0.85rem;">Berhasil!</div>
            <div style="color:#64748b;font-size:0.82rem;margin-top:1px;">{{ session('success') }}</div>
        </div>
        <button onclick="document.getElementById('successToast').style.display='none'" style="background:none;border:none;color:#94a3b8;cursor:pointer;padding:0;font-size:1rem;line-height:1;">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <style>
    @keyframes toastSlideIn {
        from { opacity:0; transform: translateX(60px) scale(0.95); }
        to   { opacity:1; transform: translateX(0) scale(1); }
    }
    </style>
    <script>
    setTimeout(function() {
        var t = document.getElementById('successToast');
        if (t) { t.style.opacity='0'; t.style.transition='opacity 0.4s'; setTimeout(function(){ t.style.display='none'; }, 400); }
    }, 4000);
    </script>
    @endif

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