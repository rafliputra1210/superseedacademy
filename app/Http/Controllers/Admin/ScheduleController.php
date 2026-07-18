<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\Schedule;
use App\Models\Coach;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with('coach')->latest()->paginate(10);
        $coaches   = Coach::orderBy('nama', 'asc')->get();

        // Ambil daftar kelompok unik dari data atlet (sinkron dengan DB atlet)
        $kelompokUmurList    = Athlete::whereNotNull('kelompok_umur')
                                      ->distinct()->pluck('kelompok_umur')->sort()->values();
        $kelompokLatihanList = Athlete::whereNotNull('kelompok_latihan')
                                      ->distinct()->pluck('kelompok_latihan')->sort()->values();

        return view('admin.schedules.index', compact(
            'schedules', 'coaches', 'kelompokUmurList', 'kelompokLatihanList'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelompok_usia'     => 'required|string|max:100',
            'kelompok_latihan'  => 'nullable|string|max:100',
            'hari'              => 'required|string|max:100',
            'waktu'             => 'required|string|max:100',
            'lokasi'            => 'required|string|max:255',
            'coach_id'          => 'nullable|exists:coaches,id',
        ]);

        Schedule::create($validated);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal latihan berhasil ditambahkan!');
    }

    public function destroy(Schedule $schedule)
    {
        Schedule::destroy($schedule->id);
        return redirect()->back()->with('success', 'Jadwal latihan berhasil dihapus!');
    }

    public function edit(Schedule $schedule)
    {
        $coaches = Coach::orderBy('nama', 'asc')->get();

        // Ambil daftar kelompok unik dari data atlet (sinkron dengan DB atlet)
        $kelompokUmurList    = Athlete::whereNotNull('kelompok_umur')
                                      ->distinct()->pluck('kelompok_umur')->sort()->values();
        $kelompokLatihanList = Athlete::whereNotNull('kelompok_latihan')
                                      ->distinct()->pluck('kelompok_latihan')->sort()->values();

        return view('admin.schedules.edit', compact(
            'schedule', 'coaches', 'kelompokUmurList', 'kelompokLatihanList'
        ));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'kelompok_usia'     => 'required|string|max:100',
            'kelompok_latihan'  => 'nullable|string|max:100',
            'hari'              => 'required|string|max:100',
            'waktu'             => 'required|string|max:100',
            'lokasi'            => 'required|string|max:255',
            'coach_id'          => 'nullable|exists:coaches,id',
        ]);

        $schedule->update($validated);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal latihan berhasil diperbarui!');
    }
}