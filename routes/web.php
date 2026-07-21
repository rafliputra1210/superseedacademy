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



// Rute Modul Landing Page (Publik)
Route::controller(LandingPageController::class)->group(function () {
    Route::get('/', 'index')->name('landing.home');
    Route::get('/profil-coach', 'coaches')->name('landing.coaches');
    Route::get('/jadwal-latihan', 'schedule')->name('landing.schedule');
    Route::get('/prestasi', 'achievements')->name('landing.achievements');
    Route::get('/galeri', 'gallery')->name('landing.gallery');
    Route::get('/pendaftaran', 'registration')->name('landing.registration');
});
// --- RUTE AUTENTIKASI ---
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
// Rute autentikasi lainnya...
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- RUTE MODUL 3: PANEL ADMIN (Backend) ---
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // --- RUTE EKSPOR EXCEL (Wajib ditaruh di atas resource) ---
    Route::get('/athletes/export/excel', [AthleteController::class, 'exportExcel'])->name('athletes.export');
    Route::get('/coaches/export/excel', [CoachController::class, 'exportExcel'])->name('coaches.export');

    // Route CRUD Data Atlet (Murid)
    // --- RUTE EKSPOR, IMPOR & CETAK KARTU ---
    Route::post('/athletes/import/excel', [AthleteController::class, 'importExcel'])->name('athletes.import');
    
    // Rute Baru Cetak ID Card Barcode:
    Route::get('/athletes/print-all-cards', [AthleteController::class, 'printAllCards'])->name('athletes.print-all');
    Route::get('/athletes/{athlete}/print-card', [AthleteController::class, 'printCard'])->name('athletes.print-card');

    // --- RUTE RESOURCE CRUD ---
    Route::resource('/athletes', AthleteController::class);
    
    // Route CRUD Coach & Absensi
    Route::resource('/coaches', CoachController::class);
    
    // --- RUTE KAMERA SCAN ABSENSI ---
    Route::get('/attendances/scan', [AttendanceController::class, 'scan'])->name('attendances.scan');
    Route::post('/attendances/scan-store', [AttendanceController::class, 'storeScan'])->name('attendances.scan-store');

    Route::resource('/attendances', AttendanceController::class);
    // Rute Keuangan, Pengumuman, dan Raport
    Route::post('/finances/generate-bulk', [FinanceController::class, 'generateBulkTagihan'])->name('finances.generate-bulk');
    Route::delete('/finances/bulk-delete', [FinanceController::class, 'bulkDelete'])->name('finances.bulkDelete');
    Route::get('/finances/export/excel', [FinanceController::class, 'exportExcel'])->name('finances.export');
    Route::get('/finances/print', [FinanceController::class, 'print'])->name('finances.print');
    Route::resource('/finances', FinanceController::class);
    Route::resource('/announcements', AnnouncementController::class);
    Route::patch('/reports/{report}/toggle-archive', [ReportController::class, 'toggleArchive'])->name('reports.toggle-archive');
    Route::resource('/reports', ReportController::class);

    // Rute CRUD Jadwal Latihan
    Route::resource('/schedules', ScheduleController::class);

    // Rute Manajemen Cepat Akun Wali Murid
    Route::get('/manajemen-akun-wali', [WaliManagerController::class, 'index'])->name('wali.index');
    Route::post('/manajemen-akun-wali/generate/{athlete}', [WaliManagerController::class, 'generate'])->name('wali.generate');
    Route::post('/manajemen-akun-wali/reset/{user}', [WaliManagerController::class, 'resetPassword'])->name('wali.reset');

    // Rute Pengaturan Banner
    Route::resource('/banners', BannerController::class)->except(['create', 'show', 'edit', 'update']);
    Route::post('/banners/{id}/toggle', [BannerController::class, 'toggleActive'])->name('banners.toggle');
});

// --- RUTE MODUL 4: PORTAL WALI MURID (Frontend User) ---
Route::middleware(['auth', 'role:wali_murid'])->prefix('portal-wali')->name('wali.')->group(function () {
    Route::get('/dashboard', [WaliPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/absensi', [WaliPortalController::class, 'absensi'])->name('absensi');
    Route::get('/raport', [WaliPortalController::class, 'raport'])->name('raport');
    Route::get('/keuangan', [WaliPortalController::class, 'keuangan'])->name('keuangan');
    Route::get('/pengumuman', [WaliPortalController::class, 'pengumuman'])->name('pengumuman');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // --- RUTE EKSPOR & IMPOR EXCEL ---
    Route::get('/athletes/export/excel', [AthleteController::class, 'exportExcel'])->name('athletes.export');
    Route::post('/athletes/import/excel', [AthleteController::class, 'importExcel'])->name('athletes.import'); // <-- Rute Upload Siswa
    
    Route::get('/coaches/export/excel', [CoachController::class, 'exportExcel'])->name('coaches.export');
    Route::post('/coaches/import/excel', [CoachController::class, 'importExcel'])->name('coaches.import'); // <-- Rute Upload Coach

    // --- RUTE RESOURCE CRUD ---
    Route::resource('/athletes', AthleteController::class);
    Route::resource('/coaches', CoachController::class);
});
Route::controller(LandingPageController::class)->group(function () {
    Route::get('/', 'index')->name('landing.home');
    Route::get('/berita', 'news')->name('landing.news'); // <-- Daftar Berita Publik
    Route::get('/berita/{slug}', 'newsDetail')->name('landing.news.detail'); // <-- Detail Baca Berita
    Route::post('/berita/{slug}/comment', 'storeComment')->name('landing.news.comment'); // <-- Tambah route komentar
    // ... rute publik lainnya (profil-coach, jadwal, dll) ...
});

// --- 2. RUTE ADMIN (Tambahkan di dalam grup middleware Admin) ---
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // ... rute admin sebelumnya ...
    
    // Rute CRUD Berita & Artikel
    Route::resource('/news', NewsController::class);
    
    // Rute CRUD Galeri
    Route::resource('/galleries', GalleryController::class);
    
    // Rute CRUD Prestasi
    Route::resource('/achievements', AchievementController::class);
});