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
    <title>Admin Panel - Super Seed Academy</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
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
        .bg-brand-light { background-color: var(--brand-light) !important; }

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
            <span class="badge px-2 py-1" style="font-size: 0.65rem; background: rgba(255,255,255,0.1); color: #94a3b8; border: 1px solid rgba(255,255,255,0.15); border-radius: 6px;">ADMIN</span>
        </div>
        
        <div class="sidebar-nav-body">
            <ul class="list-unstyled components">
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
                    <div style="font-size: 0.68rem; color: #94a3b8;">Sesi Login Aktif</div>
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
                
                <span class="navbar-brand mb-0 h1 fs-5 fw-bold text-brand-navy">@yield('title', 'Dashboard')</span>
                
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
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center" role="alert" style="background-color: #ecfdf5; color: #065f46;">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i> 
                    <div>{{ session('success') }}</div>
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

<!-- ============================================================ -->
<!-- CUSTOM CONFIRM MODAL (Global — menggantikan browser confirm) -->
<!-- ============================================================ -->
<div id="customConfirmModal" style="
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 1rem;
">
    <!-- Backdrop blur -->
    <div id="confirmBackdrop" style="
        position: absolute;
        inset: 0;
        background: rgba(10, 25, 47, 0.65);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        animation: fadeInBackdrop 0.2s ease;
    "></div>

    <!-- Modal Card -->
    <div id="confirmCard" style="
        position: relative;
        background: #ffffff;
        border-radius: 20px;
        padding: 2rem;
        max-width: 420px;
        width: 100%;
        box-shadow: 0 25px 60px rgba(0,0,0,0.25), 0 0 0 1px rgba(255,255,255,0.05);
        animation: slideUpIn 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
        text-align: center;
    ">
        <!-- Icon -->
        <div style="
            width: 72px; height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, #fff3cd, #ffe083);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.25rem;
            box-shadow: 0 8px 24px rgba(251, 191, 36, 0.35);
        ">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
                <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"
                    stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        <!-- Title -->
        <h5 style="font-weight: 700; color: #0a192f; margin-bottom: 0.5rem; font-size: 1.15rem;">Konfirmasi Hapus</h5>

        <!-- Message -->
        <p id="confirmMessage" style="color: #64748b; font-size: 0.92rem; line-height: 1.6; margin-bottom: 1.75rem;">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>

        <!-- Buttons -->
        <div style="display: flex; gap: 0.75rem; justify-content: center;">
            <button id="confirmCancel" style="
                flex: 1;
                padding: 0.65rem 1.25rem;
                border-radius: 12px;
                border: 2px solid #e2e8f0;
                background: #f8fafc;
                color: #475569;
                font-weight: 600;
                font-size: 0.9rem;
                cursor: pointer;
                transition: all 0.2s;
                font-family: 'Inter', sans-serif;
            " onmouseover="this.style.borderColor='#cbd5e1';this.style.background='#f1f5f9'" onmouseout="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc'">
                <i class="bi bi-x-lg me-1"></i> Batal
            </button>
            <button id="confirmOk" style="
                flex: 1;
                padding: 0.65rem 1.25rem;
                border-radius: 12px;
                border: none;
                background: linear-gradient(135deg, #ef4444, #dc2626);
                color: #ffffff;
                font-weight: 700;
                font-size: 0.9rem;
                cursor: pointer;
                transition: all 0.2s;
                font-family: 'Inter', sans-serif;
                box-shadow: 0 4px 14px rgba(220, 38, 38, 0.35);
            " onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 20px rgba(220,38,38,0.45)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 14px rgba(220,38,38,0.35)'">
                <i class="bi bi-trash3-fill me-1"></i> Ya, Hapus!
            </button>
        </div>
    </div>
</div>

<style>
@keyframes fadeInBackdrop { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUpIn {
    from { opacity: 0; transform: translateY(30px) scale(0.95); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes slideDownOut {
    from { opacity: 1; transform: translateY(0) scale(1); }
    to   { opacity: 0; transform: translateY(20px) scale(0.95); }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
   SIDEBAR TOGGLE
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

/* ============================================================
   CUSTOM CONFIRM MODAL — intercept semua form dengan data-confirm
   atau onsubmit berisi "confirm("  
   ============================================================ */
    const modal     = document.getElementById('customConfirmModal');
    const backdrop  = document.getElementById('confirmBackdrop');
    const card      = document.getElementById('confirmCard');
    const msgEl     = document.getElementById('confirmMessage');
    const okBtn     = document.getElementById('confirmOk');
    const cancelBtn = document.getElementById('confirmCancel');
    let targetForm  = null;

    function showConfirm(msg, form) {
        targetForm = form;
        msgEl.textContent = msg || 'Apakah Anda yakin ingin menghapus data ini?';
        modal.style.display = 'flex';
        card.style.animation = 'slideUpIn 0.28s cubic-bezier(0.34,1.56,0.64,1)';
    }

    function closeConfirm() {
        card.style.animation = 'slideDownOut 0.2s ease forwards';
        setTimeout(() => { modal.style.display = 'none'; targetForm = null; }, 200);
    }

    okBtn.addEventListener('click', function () {
        if (targetForm) {
            modal.style.display = 'none';
            targetForm.submit();
        }
    });

    cancelBtn.addEventListener('click', closeConfirm);
    backdrop.addEventListener('click', closeConfirm);

    /* Escape key tutup modal */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') closeConfirm();
    });

    /* Intercept semua form yang punya attribute data-confirm */
    document.querySelectorAll('form[data-confirm]').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            showConfirm(form.getAttribute('data-confirm'), form);
        });
    });

    /* Intercept form yang masih pakai onsubmit=confirm() lama */
    document.querySelectorAll('form[onsubmit]').forEach(function(form) {
        const attr = form.getAttribute('onsubmit') || '';
        const match = attr.match(/confirm\(['"](.*?)['"]\)/);
        if (match) {
            const msg = match[1];
            form.removeAttribute('onsubmit');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                showConfirm(msg, form);
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