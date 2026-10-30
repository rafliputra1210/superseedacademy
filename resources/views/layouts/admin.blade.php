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
    <title>@yield('title', (Auth::check() && Auth::user()->role === 'asesor') ? 'Portal Asesor' : 'Admin Panel') - Super Seed Academy</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tailwind CSS & App Assets via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Tom Select (Searchable Select) -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

    
    <style>
        /* CSS Variables for Brand Colors */
        :root {
            --brand-blue: #0066FF;
            --brand-navy: #0A192F;
            --brand-light: #F0F5FF;
            --brand-gray: #64748B;
            --bg-color: #F8FAFC; /* slate-50 equivalent */
        }
        
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--bg-color); 
            color: #334155; 
        }

        /* Sidebar Styling (Navy Blue) */
        #sidebar { 
            min-width: 260px; max-width: 260px; height: 100vh;
            background: var(--brand-navy); /* Navy sidebar */
            color: #cbd5e1; 
            transition: all 0.3s; 
            border-right: none;
            position: sticky;
            top: 0;
            align-self: flex-start;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }
        #sidebar .sidebar-header { 
            padding: 20px; background: var(--brand-navy); 
            border-bottom: 1px solid rgba(255,255,255,0.1); 
            flex-shrink: 0;
        }
        #sidebar .sidebar-nav-body {
            flex: 1 1 auto;
            overflow-y: auto;
            overflow-x: hidden;
        }
        #sidebar ul.components { padding: 15px 0; }
        #sidebar ul li a { 
            padding: 12px 24px; font-size: 0.95em; font-weight: 500; display: block; 
            color: #94a3b8; text-decoration: none; 
            border-left: 4px solid transparent; transition: 0.2s; 
        }
        #sidebar ul li a:hover, #sidebar ul li a.active { 
            color: #ffffff; 
            background: rgba(255,255,255,0.05); 
            border-left-color: var(--brand-blue); 
        }
        #sidebar ul li a i { margin-right: 12px; font-size: 1.2em; color: #64748b; transition: 0.2s; }
        #sidebar ul li a:hover i, #sidebar ul li a.active i { color: var(--brand-blue); }

        .sidebar-section-title {
            font-size: 0.75rem; font-weight: 700; color: #64748b; 
            text-transform: uppercase; letter-spacing: 0.05em;
        }

        #sidebar .sidebar-footer {
            padding: 16px 20px;
            background: rgba(15, 23, 42, 0.4);
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            flex-shrink: 0;
        }
        #sidebar .sidebar-footer .btn-logout {
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.25);
            transition: all 0.2s ease-in-out;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px;
        }
        #sidebar .sidebar-footer .btn-logout:hover {
            background: #ef4444;
            color: #ffffff;
            border-color: #ef4444;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35);
        }

        /* Main Content & Navbar */
        .main-content { width: 100%; overflow-x: hidden; }
        .navbar-custom { 
            background: #ffffff; 
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.5rem;
        }
        
        /* Card Custom (Minimalist) */
        .card-custom { 
            background: #ffffff; border: 1px solid #e2e8f0; 
            border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); 
        }

        /* Utility classes */
        .text-brand-blue { color: var(--brand-blue) !important; }
        .text-brand-navy { color: var(--brand-navy) !important; }
        .text-brand-gray { color: var(--brand-gray) !important; }
        .bg-brand-light { background-color: var(--brand-light) !important; }
        .bg-brand-navy { background-color: var(--brand-navy) !important; color: #ffffff !important; }
        .bg-brand-blue { background-color: var(--brand-blue) !important; color: #ffffff !important; }

        /* Responsive Sidebar */
        @media (max-width: 991.98px) {
            #sidebar {
                margin-left: -260px;
                position: fixed;
                z-index: 1050;
                height: 100vh;
                height: 100dvh; /* dynamic viewport height — fix iOS safari */
                top: 0;
                left: 0;
                overscroll-behavior: contain;
            }
            #sidebar.active { margin-left: 0; }
            .sidebar-overlay {
                display: none; position: fixed; width: 100vw; height: 100vh;
                background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(2px);
                z-index: 1040; top: 0; left: 0;
            }
            .sidebar-overlay.active { display: block; }
        }

        /* Scrollbar tipis & elegan untuk sidebar */
        #sidebar .sidebar-nav-body::-webkit-scrollbar { width: 4px; }
        #sidebar .sidebar-nav-body::-webkit-scrollbar-track { background: transparent; }
        #sidebar .sidebar-nav-body::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.15);
            border-radius: 4px;
        }
        #sidebar .sidebar-nav-body::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.25);
        }

        /* Custom Pagination Styling & Brand Integration */
        .pagination {
            margin-bottom: 0;
            gap: 5px;
        }
        .pagination .page-item .page-link {
            border-radius: 8px !important;
            color: #475569;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 6px 13px;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
        }
        .pagination .page-item .page-link:hover {
            background-color: #f1f5f9;
            border-color: #cbd5e1;
            color: var(--brand-navy);
            transform: translateY(-1px);
        }
        .pagination .page-item.active .page-link {
            background-color: var(--brand-blue) !important;
            border-color: var(--brand-blue) !important;
            color: #ffffff !important;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(0, 102, 255, 0.3);
        }
        .pagination .page-item.disabled .page-link {
            color: #94a3b8;
            background-color: #f8fafc;
            border-color: #e2e8f0;
            box-shadow: none;
            cursor: not-allowed;
            transform: none;
        }
        /* Anti-Explosion Rule for any SVG icons */
        svg.w-5,
        svg.h-5,
        .pagination svg,
        .pagination-wrapper svg,
        nav[role="navigation"] svg {
            width: 16px !important;
            height: 16px !important;
            max-width: 16px !important;
            max-height: 16px !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }
    </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebar-overlay"></div>

<div class="d-flex">
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('images/logo.png') }}"
                     alt="Superseed Logo"
                     style="height: 40px; width: 40px; object-fit: contain; border-radius: 8px; background: rgba(255,255,255,0.05); padding: 2px;">
                <div>
                    <div style="font-weight: 800; color: #fff; font-size: 0.95rem; letter-spacing: 0.5px; line-height: 1.1;">SUPERSEED</div>
                    <div style="font-size: 0.65rem; color: #94a3b8; letter-spacing: 1px; text-transform: uppercase;">Academy Portal</div>
                </div>
            </div>
            @if(Auth::check() && Auth::user()->role === 'asesor')
                <span class="badge px-2 py-1" style="font-size: 0.65rem; background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.35); border-radius: 6px; letter-spacing: 0.5px;">ASESOR</span>
            @else
                <span class="badge px-2 py-1" style="font-size: 0.65rem; background: rgba(255,255,255,0.1); color: #94a3b8; border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; letter-spacing: 0.5px;">ADMIN</span>
            @endif
        </div>
        
        <div class="sidebar-nav-body">
            <ul class="list-unstyled components">
            @if(Auth::check() && Auth::user()->role === 'asesor')
                <li class="px-4 mt-2 mb-2 sidebar-section-title">Menu Penilaian</li>
                <li>
                    <a href="{{ route('asesor.penilaian.index') }}" class="{{ request()->routeIs('asesor.penilaian.*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-check-fill"></i> Penilaian Atlet
                    </a>
                </li>
            @else
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-fill"></i> Dashboard
                    </a>
                </li>
                
                <li class="px-4 mt-4 mb-2 sidebar-section-title">Manajemen Data</li>
                <li>
                    <a href="{{ route('admin.athletes.index') }}" class="{{ request()->routeIs('admin.athletes.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> Data Atlet (Murid)
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.biography.index') }}" class="nav-link {{ request()->routeIs('admin.biography.*') ? 'bg-blue-900 text-white rounded-md' : '' }}">
                        <i class="bi bi-person-badge"></i>
                        <span>Biografi Akademisi</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.coaches.index') }}" class="{{ request()->routeIs('admin.coaches.*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge-fill"></i> Data Coach
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.schedules.index') }}" class="{{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-event-fill"></i> Jadwal Latihan
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.attendances.index') }}" class="{{ request()->routeIs('admin.attendances.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check-fill"></i> Absensi Murid
                    </a>
                </li>
                
                <li class="px-4 mt-4 mb-2 sidebar-section-title">Akademik & Keuangan</li>
                <li>
                    <a href="{{ route('admin.finances.index') }}" class="{{ request()->routeIs('admin.finances.*') ? 'active' : '' }}">
                        <i class="bi bi-wallet2"></i> Uang Kas & Keuangan
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <i class="bi bi-award-fill"></i> Raport Murid
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.asesor_manager.index') }}" class="{{ request()->routeIs('admin.asesor_manager.*') ? 'active' : '' }}">
                        <i class="bi bi-person-check-fill"></i> Manajemen Asesor
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.assessment_categories.index') }}" class="{{ request()->routeIs('admin.assessment_categories.*') ? 'active' : '' }}">
                        <i class="bi bi-sliders2"></i> Kategori Penilaian
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.achievements.index') }}" class="{{ request()->routeIs('admin.achievements.*') ? 'active' : '' }}">
                        <i class="bi bi-trophy-fill"></i> Prestasi Klub
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.announcements.index') }}" class="{{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                        <i class="bi bi-megaphone-fill"></i> Pengumuman
                    </a>
                </li>
                
                <li class="px-4 mt-4 mb-2 sidebar-section-title">Pengaturan Front-End</li>
                <li>
                    <a href="{{ route('admin.banners.index') }}" class="{{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                        <i class="bi bi-images"></i> Banner Beranda
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.news.index') }}" class="{{ request()->routeIs('admin.news.*') ? 'active' : '' }}">
                        <i class="bi bi-newspaper"></i> Berita & Artikel
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.galleries.index') }}" class="{{ request()->routeIs('admin.galleries.*') ? 'active' : '' }}">
                        <i class="bi bi-camera-fill"></i> Galeri Foto
                    </a>
                </li>
            @endif
            </ul>
        </div>

        <!-- Tombol Keluar di bagian paling bawah Sidebar -->
        <div class="sidebar-footer">
            <div class="d-flex align-items-center mb-2 px-1">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-2 flex-shrink-0" style="width: 32px; height: 32px; background: rgba(255,255,255,0.1); color: #60a5fa;">
                    <i class="bi bi-person-fill fs-6"></i>
                </div>
                <div class="text-truncate">
                    <div class="text-white fw-semibold small text-truncate" style="font-size: 0.825rem;">{{ Auth::user()->name ?? 'Administrator' }}</div>
                    <div style="font-size: 0.68rem; color: #94a3b8;">{{ (Auth::check() && Auth::user()->role === 'asesor') ? 'Asesor Penilai' : 'Sesi Login Aktif' }}</div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="w-100">
                @csrf
                <button type="submit" class="btn btn-logout w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-box-arrow-right fs-6"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </nav>

    <!-- Main Content Area -->
    <div class="main-content d-flex flex-column min-vh-100">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand navbar-custom sticky-top">
            <div class="container-fluid p-0">
                <button type="button" id="sidebarCollapse" class="btn btn-sm btn-light border me-3 d-lg-none shadow-sm">
                    <i class="bi bi-list fs-5 text-secondary"></i>
                </button>
                
                <span class="navbar-brand mb-0 h1 fs-5 fw-bold text-brand-navy">@yield('title', (Auth::check() && Auth::user()->role === 'asesor') ? 'Portal Penilaian Asesor' : 'Dashboard')</span>
                
                <div class="d-flex align-items-center ms-auto">
                    <div class="d-flex align-items-center">
                        <div class="bg-brand-light text-brand-blue rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <span class="text-secondary small fw-medium d-none d-sm-inline">{{ Auth::user()->name ?? 'Administrator' }}</span>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Container -->
        <main class="p-4 flex-grow-1">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center mb-3" role="alert" style="background-color: #ecfdf5; color: #065f46; border-radius: 12px;">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i> 
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center mb-3" role="alert" style="background-color: #fef2f2; color: #991b1b; border-radius: 12px;">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i> 
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="border-top mt-auto" style="background: #fff; padding: 1.25rem 1.5rem;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="logo" style="height:28px;width:28px;object-fit:contain;opacity:.7;">
                    <span style="font-size:0.78rem;color:#94a3b8;font-weight:600;">
                        &copy; {{ date('Y') }} <strong style="color:#64748b;">Superseed Academy</strong>
                    </span>
                </div>
                <span style="font-size:0.72rem;color:#cbd5e1;">Sistem Manajemen Akademi &mdash; Laravel 12</span>
            </div>
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    // Inisialisasi Searchable Select secara global
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.searchable-select').forEach(function(el) {
            new TomSelect(el, {
                create: false,
                placeholder: "-- Cari / Pilih Data --",
            });
        });
    });
</script>
<script>
/* ============================================================
   SIDEBAR TOGGLE & SWEETALERT2 GLOBAL CONFIRM / TOAST
   ============================================================ */
document.addEventListener("DOMContentLoaded", function () {
    const sidebar  = document.getElementById('sidebar');
    const collapseBtn = document.getElementById('sidebarCollapse');
    const overlay  = document.getElementById('sidebar-overlay');

    if (collapseBtn) {
        collapseBtn.addEventListener('click', function () {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });
    }
    if (overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }

    // --- Toast Notification Helper ---
    window.AppToast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
    });

    @if(session('success'))
        AppToast.fire({
            icon: 'success',
            title: @json(session('success'))
        });
    @endif
    @if(session('error'))
        AppToast.fire({
            icon: 'error',
            title: @json(session('error'))
        });
    @endif

    // --- SweetAlert Confirm Helper ---
    function fireSweetConfirm(options, onConfirmed) {
        Swal.fire({
            title: options.title || 'Konfirmasi Tindakan',
            html: options.html || options.text || 'Apakah Anda yakin ingin melanjutkan?',
            icon: options.icon || 'question',
            showCancelButton: true,
            confirmButtonText: options.btnText || 'Ya, Lanjutkan',
            cancelButtonText: options.cancelText || 'Batal',
            reverseButtons: true,
            focusCancel: true,
            customClass: {
                popup: 'rounded-4 p-4 shadow-lg border-0',
                title: 'fs-5 fw-bold text-dark mb-2',
                htmlContainer: 'text-secondary small line-relaxed',
                confirmButton: `btn ${options.btnClass || 'btn-primary'} px-4 py-2 rounded-3 fw-semibold shadow-sm`,
                cancelButton: 'btn btn-light border px-4 py-2 rounded-3 fw-medium text-secondary me-2'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                onConfirmed();
            }
        });
    }

    // 1. Intercept form dengan class .form-confirm-action
    document.querySelectorAll('form.form-confirm-action').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            fireSweetConfirm({
                title: form.getAttribute('data-title'),
                html: form.getAttribute('data-html') || form.getAttribute('data-text'),
                icon: form.getAttribute('data-icon') || 'question',
                btnText: form.getAttribute('data-btn-text') || 'Ya, Lanjutkan',
                btnClass: form.getAttribute('data-btn-class') || 'btn-primary'
            }, () => form.submit());
        });
    });

    // 2. Intercept form dengan attribute data-confirm
    document.querySelectorAll('form[data-confirm]:not(.form-confirm-action)').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            fireSweetConfirm({
                title: 'Konfirmasi Tindakan',
                html: form.getAttribute('data-confirm'),
                icon: 'warning',
                btnText: 'Ya, Lanjutkan',
                btnClass: 'btn-danger'
            }, () => form.submit());
        });
    });

    // 3. Intercept tombol lama dengan onclick="return confirm(...)"
    document.querySelectorAll('button[onclick*="confirm("], a[onclick*="confirm("], input[type="submit"][onclick*="confirm("]').forEach(function(el) {
        const onclickAttr = el.getAttribute('onclick') || '';
        const match = onclickAttr.match(/confirm\(['"](.*?)['"]\)/);
        if (match) {
            const msg = match[1];
            el.removeAttribute('onclick');

            el.addEventListener('click', function(e) {
                e.preventDefault();
                const form = el.closest('form');
                if (!form && el.tagName.toLowerCase() !== 'a') return;

                const lower = msg.toLowerCase();
                let title = 'Konfirmasi Tindakan';
                let icon = 'question';
                let btnText = 'Ya, Lanjutkan';
                let btnClass = 'btn-primary';

                if (lower.includes('perbarui') || lower.includes('update')) {
                    title = 'Perbarui Raport?';
                    icon = 'question';
                    btnText = '<i class="bi bi-arrow-repeat me-1"></i> Ya, Perbarui';
                    btnClass = 'btn-primary';
                } else if (lower.includes('terbitkan') || lower.includes('generate')) {
                    title = 'Generate & Terbitkan?';
                    icon = 'info';
                    btnText = '<i class="bi bi-send-check me-1"></i> Ya, Terbitkan';
                    btnClass = 'btn-success';
                } else if (lower.includes('arsip')) {
                    title = 'Arsipkan Raport?';
                    icon = 'warning';
                    btnText = '<i class="bi bi-archive me-1"></i> Ya, Arsipkan';
                    btnClass = 'btn-warning text-dark';
                } else if (lower.includes('aktifkan')) {
                    title = 'Aktifkan Raport?';
                    icon = 'question';
                    btnText = '<i class="bi bi-box-arrow-up me-1"></i> Ya, Aktifkan';
                    btnClass = 'btn-success';
                } else if (lower.includes('hapus') || lower.includes('delete')) {
                    title = 'Konfirmasi Hapus';
                    icon = 'warning';
                    btnText = '<i class="bi bi-trash3-fill me-1"></i> Ya, Hapus';
                    btnClass = 'btn-danger';
                }

                fireSweetConfirm({
                    title: title,
                    html: msg,
                    icon: icon,
                    btnText: btnText,
                    btnClass: btnClass
                }, () => {
                    if (form) {
                        form.submit();
                    } else if (el.href) {
                        window.location.href = el.href;
                    }
                });
            });
        }
    });

    // 4. Intercept form yang masih pakai onsubmit="return confirm(...)"
    document.querySelectorAll('form[onsubmit*="confirm("]').forEach(function(form) {
        const attr = form.getAttribute('onsubmit') || '';
        const match = attr.match(/confirm\(['"](.*?)['"]\)/);
        if (match) {
            const msg = match[1];
            form.removeAttribute('onsubmit');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                fireSweetConfirm({
                    title: 'Konfirmasi Tindakan',
                    html: msg,
                    icon: 'warning',
                    btnText: 'Ya, Lanjutkan',
                    btnClass: 'btn-danger'
                }, () => form.submit());
            });
        }
    });
});
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