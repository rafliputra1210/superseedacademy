<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AthleteController;
use App\Http\Controllers\Admin\CoachController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Wali\WaliPortalController;
use App\Http\Controllers\Admin\WaliManagerController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\AchievementController;
use App\Http\Controllers\Asesor\AsesorController;
use App\Http\Controllers\Admin\AsesorManagerController;
use App\Http\Controllers\Admin\AssessmentCategoryController;


// =============================================================================
// RUTE MODUL LANDING PAGE (Publik)
// =============================================================================
Route::controller(LandingPageController::class)->group(function () {
    Route::get('/', 'index')->name('landing.home');
    Route::get('/profil-coach', 'coaches')->name('landing.coaches');
    Route::get('/jadwal-latihan', 'schedule')->name('landing.schedule');
    Route::get('/prestasi', 'achievements')->name('landing.achievements');
    Route::get('/galeri', 'gallery')->name('landing.gallery');
    Route::get('/pendaftaran', 'registration')->name('landing.registration');
    Route::get('/berita', 'news')->name('landing.news');
    Route::get('/berita/{slug}', 'newsDetail')->name('landing.news.detail');
    // Rate limit komentar publik: 3x per menit per IP
    Route::post('/berita/{slug}/comment', 'storeComment')
        ->middleware('throttle:3,1')
        ->name('landing.news.comment');
});

// =============================================================================
// RUTE AUTENTIKASI
// =============================================================================
// Rate limit login: max 5 percobaan per menit per IP (+ manual RateLimiter di AuthController)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// =============================================================================
// RUTE PANEL ADMIN (Backend) — dilindungi auth + role + throttle
// =============================================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', function () {
        $stats = \Illuminate\Support\Facades\Cache::remember('admin_dashboard_stats', 120, function () {
            $pemasukan = \App\Models\Finance::where('jenis', 'pemasukan')->sum('nominal');
            $pengeluaran = \App\Models\Finance::where('jenis', 'pengeluaran')->sum('nominal');
            return [
                'totalAthlete'      => \App\Models\Athlete::count(),
                'totalCoach'        => \App\Models\Coach::count(),
                'totalSaldo'        => (float)($pemasukan - $pengeluaran),
                'totalAnnouncement' => \App\Models\Announcement::where('is_active', true)->count(),
            ];
        });

        return view('admin.dashboard', $stats);
    })->name('dashboard');

    // --- EKSPOR EXCEL (read-only, throttle sedang) ---
    Route::get('/athletes/export/excel', [AthleteController::class, 'exportExcel'])
        ->middleware('throttle:10,1')
        ->name('athletes.export');
    Route::get('/coaches/export/excel', [CoachController::class, 'exportExcel'])
        ->middleware('throttle:10,1')
        ->name('coaches.export');

    // --- IMPORT (write, throttle ketat) ---
    Route::post('/coaches/import/excel', [CoachController::class, 'importExcel'])
        ->middleware('throttle:5,1')
        ->name('coaches.import');
    Route::post('/athletes/import/excel', [AthleteController::class, 'importExcel'])
        ->middleware('throttle:5,1')
        ->name('athletes.import');

    // --- CETAK KARTU / QR ---
    Route::get('/athletes/export/qr', [AthleteController::class, 'exportQr'])
        ->middleware('throttle:20,1')
        ->name('athletes.export-qr');
    Route::get('/athletes/{athlete}/print-qr', [AthleteController::class, 'printQrCard'])
        ->middleware('throttle:30,1')
        ->name('athletes.print-qr-card');
    Route::get('/athletes/print-all-cards', [AthleteController::class, 'printAllCards'])
        ->middleware('throttle:10,1')
        ->name('athletes.print-all');
    Route::get('/athletes/{athlete}/print-card', [AthleteController::class, 'printCard'])
        ->middleware('throttle:30,1')
        ->name('athletes.print-card');

    // --- RESOURCE CRUD ---
    Route::resource('/athletes', AthleteController::class);
    Route::resource('/coaches', CoachController::class);

    // --- SCAN ABSENSI (rate limit ketat: anti-abuse scanner) ---
    Route::get('/attendances/scan', [AttendanceController::class, 'scan'])->name('attendances.scan');
    Route::post('/attendances/scan-store', [AttendanceController::class, 'storeScan'])
        ->middleware('throttle:60,1')
        ->name('attendances.scan-store');
    Route::resource('/attendances', AttendanceController::class);

    // --- KEUANGAN ---
    Route::post('/finances/generate-bulk', [FinanceController::class, 'generateBulkTagihan'])
        ->middleware('throttle:10,1')
        ->name('finances.generate-bulk');
    Route::delete('/finances/bulk-delete', [FinanceController::class, 'bulkDelete'])
        ->middleware('throttle:10,1')
        ->name('finances.bulkDelete');
    Route::get('/finances/export/excel', [FinanceController::class, 'exportExcel'])
        ->middleware('throttle:10,1')
        ->name('finances.export');
    Route::get('/finances/print', [FinanceController::class, 'print'])
        ->middleware('throttle:10,1')
        ->name('finances.print');
    Route::resource('/finances', FinanceController::class);

    // --- PENGUMUMAN ---
    Route::resource('/announcements', AnnouncementController::class);

    // --- RAPORT & PERIODE ---
    Route::post('/reports/{raport}/toggle-archive', [ReportController::class, 'toggleArchive'])
        ->middleware('throttle:30,1')
        ->name('reports.toggle-archive');
    Route::post('/reports/periods/set-active', [ReportController::class, 'setActivePeriod'])
        ->middleware('throttle:10,1')
        ->name('reports.periods.set-active');
    Route::post('/reports/periods/store', [ReportController::class, 'storePeriod'])
        ->middleware('throttle:10,1')
        ->name('reports.periods.store');
    Route::delete('/reports/periods/{period}', [ReportController::class, 'destroyPeriod'])
        ->middleware('throttle:10,1')
        ->name('reports.periods.destroy');
    Route::get('/reports/{id}/print', [ReportController::class, 'print'])->name('reports.print');
    Route::resource('/reports', ReportController::class);
    Route::get('/reports/rekap-asesor', [ReportController::class, 'rekapAsesor'])->name('reports.rekap');
    Route::post('/reports/generate-final/{athlete}', [ReportController::class, 'generateFinalRaport'])
        ->middleware('throttle:20,1')
        ->name('reports.generate-final');
    Route::get('/admin/reports/{id}/edit', [\App\Http\Controllers\Admin\ReportController::class, 'edit'])->name('admin.reports.edit');
    Route::put('/admin/reports/{id}', [\App\Http\Controllers\Admin\ReportController::class, 'update'])->name('admin.reports.update');

    // --- JADWAL ---
    Route::resource('/schedules', ScheduleController::class);

    // --- MANAJEMEN AKUN WALI (write ops throttled) ---
    Route::get('/manajemen-akun-wali', [WaliManagerController::class, 'index'])->name('wali.index');
    Route::post('/manajemen-akun-wali/generate/{athlete}', [WaliManagerController::class, 'generate'])
        ->middleware('throttle:20,1')
        ->name('wali.generate');
    Route::post('/manajemen-akun-wali/reset/{user}', [WaliManagerController::class, 'resetPassword'])
        ->middleware('throttle:10,1')
        ->name('wali.reset');

    // --- BANNER ---
    Route::resource('/banners', BannerController::class)->except(['create', 'show', 'edit', 'update']);
    Route::post('/banners/{id}/toggle', [BannerController::class, 'toggleActive'])
        ->middleware('throttle:30,1')
        ->name('banners.toggle');

    // --- KONTEN (BERITA, GALERI, PRESTASI) ---
    Route::resource('/news', NewsController::class);
    Route::resource('/galleries', GalleryController::class);
    Route::resource('/achievements', AchievementController::class);

    // --- MANAJEMEN AKUN ASESOR ---
    Route::get('/manajemen-asesor', [AsesorManagerController::class, 'index'])->name('asesor_manager.index');
    Route::post('/manajemen-asesor', [AsesorManagerController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('asesor_manager.store');
    Route::delete('/manajemen-asesor/{user}', [AsesorManagerController::class, 'destroy'])
        ->middleware('throttle:10,1')
        ->name('asesor_manager.destroy');

    // --- KATEGORI PENILAIAN RAPORT ---
    Route::resource('/assessment-categories', AssessmentCategoryController::class)
        ->names('assessment_categories')
        ->parameters(['assessment-categories' => 'category'])
        ->except(['create', 'show', 'edit']);
    Route::post('/assessment-categories/{category}/toggle', [AssessmentCategoryController::class, 'toggleActive'])
        ->middleware('throttle:30,1')
        ->name('assessment_categories.toggle');
    
    // --- BIOGRAFI ATLET ---
    Route::post('biography/import', [\App\Http\Controllers\Admin\AthleteBiographyController::class, 'import'])->name('biography.import');
    Route::post('biography/{id}/achievement', [\App\Http\Controllers\Admin\AthleteBiographyController::class, 'storeAchievement'])->name('biography.achievement.store');
    Route::post('biography/{id}/statistic', [\App\Http\Controllers\Admin\AthleteBiographyController::class, 'storeStatistic'])->name('biography.statistic.store');
    Route::get('biography/{id}/print', [\App\Http\Controllers\Admin\AthleteBiographyController::class, 'print'])->name('biography.print');
    Route::resource('biography', \App\Http\Controllers\Admin\AthleteBiographyController::class);
});

// =============================================================================
// RUTE PORTAL WALI MURID — dilindungi auth + role
// =============================================================================
Route::middleware(['auth', 'role:wali_murid'])->prefix('portal-wali')->name('wali.')->group(function () {
    Route::get('/dashboard', [WaliPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/biografi', [WaliPortalController::class, 'biografi'])->name('biografi');
    Route::get('/biografi/{id}/print', [WaliPortalController::class, 'printBiografi'])->name('biografi.print');
    Route::get('/absensi', [WaliPortalController::class, 'absensi'])->name('absensi');
    Route::get('/raport', [WaliPortalController::class, 'raport'])->name('raport');
    Route::get('/keuangan', [WaliPortalController::class, 'keuangan'])->name('keuangan');
    Route::get('/pengumuman', [WaliPortalController::class, 'pengumuman'])->name('pengumuman');
    Route::get('/profil', [WaliPortalController::class, 'profile'])->name('profile');
    // Rate limit ganti password: 5x per menit (sudah ada) → pertahankan
    Route::post('/profil/ubah-password', [WaliPortalController::class, 'updatePassword'])
        ->middleware('throttle:5,1')
        ->name('profile.update-password');
});

// =============================================================================
// RUTE ASESOR — dilindungi auth + role + throttle pada submit penilaian
// =============================================================================
Route::middleware(['auth', 'role:asesor'])->prefix('asesor')->name('asesor.')->group(function () {
    Route::get('/penilaian', [AsesorController::class, 'index'])->name('penilaian.index');
    Route::get('/penilaian/{athlete}/create', [AsesorController::class, 'create'])->name('penilaian.create');
    // Rate limit submit penilaian: 30x per menit per asesor
    Route::post('/penilaian/{athlete}', [AsesorController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('penilaian.store');
});
