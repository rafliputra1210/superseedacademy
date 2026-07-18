<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\Report;
use App\Models\Attendance;
use App\Models\Announcement;
use App\Models\Athlete;
use App\Models\Schedule;
use App\Models\Banner;
use App\Models\Gallery;
use App\Models\Achievement;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    // 1. Beranda (Home) - Menampilkan statistik real & pengumuman aktif
    public function index()
    {
        $totalAthlete = Athlete::count('*');
        $totalCoach = Coach::count('*');
        $announcements = Announcement::where('is_active', '=', true, 'and')->latest('created_at')->take(3)->get();
        $banners = Banner::where('is_active', true)->orderBy('urutan')->take(5)->get();
        
        $recentNews = \App\Models\News::where('is_active', true)->latest('tanggal')->take(3)->get();
        
        return view('landing.home', compact('totalAthlete', 'totalCoach', 'announcements', 'banners', 'recentNews'));
    }

    // 2. Profil Coach - Menampilkan seluruh coach dari database
    public function coaches()
    {
        $coaches = Coach::latest('created_at')->get();
        return view('landing.coaches', compact('coaches'));
    }

    // 3. Jadwal Latihan - Menampilkan jadwal terstruktur
    public function schedule()
{
    // Mengambil jadwal beserta data coach-nya
    $schedules = Schedule::with('coach')->latest('created_at')->get();
    
    return view('landing.schedule', compact('schedules'));
}

    // 4. Prestasi - Menampilkan data prestasi dari model Achievement
    public function achievements()
    {
        $achievements = Achievement::where('is_active', true)->latest('tanggal')->get();

        return view('landing.achievements', compact('achievements'));
    }

    // 5. Galeri - Menampilkan foto-foto galeri
    public function gallery()
    {
        $galleries = Gallery::where('is_active', true)->latest('tanggal')->paginate(12);

        return view('landing.gallery', compact('galleries'));
    }

    // 6. Informasi Pendaftaran
    public function registration()
    {
        return view('landing.registration');
    }

    public function news()
    {
        $news = \App\Models\News::where('is_active', true)->latest('tanggal')->paginate(9);
        return view('landing.news', compact('news'));
    }

    public function newsDetail($slug)
    {
        $news = \App\Models\News::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $recentNews = \App\Models\News::where('is_active', true)->where('id', '!=', $news->id)->latest('tanggal')->take(5)->get();
        return view('landing.news_detail', compact('news', 'recentNews'));
    }

    public function storeComment(Request $request, $slug)
    {
        // Placeholder for comment functionality since there is no comment model yet
        return redirect()->back()->with('success', 'Komentar berhasil dikirim dan menunggu moderasi.');
    }
}