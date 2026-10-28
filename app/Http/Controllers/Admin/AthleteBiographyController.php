<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\AthleteBiography;
use Illuminate\Http\Request;

class AthleteBiographyController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data dengan pencarian dan filter sesuai spesifikasi[cite: 16, 18]
        $query = AthleteBiography::with('athlete');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('athlete', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nomor_induk', 'like', "%{$search}%");
            });
        }

        if ($request->has('kategori')) {
            $query->where('kategori_kelas', $request->kategori);
        }

        $biographies = $query->paginate(10);
        return view('admin.biography.index', compact('biographies'));
    }

    public function create()
    {
        // Ambil atlet yang belum punya biografi[cite: 17, 19]
        $athletes = Athlete::doesntHave('biography')->get();
        return view('admin.biography.create', compact('athletes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'kategori_kelas' => 'required|in:01,02,03,04', // Sesuai kode spesifikasi[cite: 16]
            'tanggal_bergabung' => 'required|date',
            'posisi' => 'nullable|string',
            // ... tambahkan validasi lain[cite: 17, 18]
        ]);

        AthleteBiography::create($request->all());

        return redirect()->route('admin.biography.index')->with('success', 'Biografi berhasil ditambahkan.');
    }

    public function edit(AthleteBiography $biography)
    {
        return view('admin.biography.edit', compact('biography'));
    }

    public function update(Request $request, AthleteBiography $biography)
    {
        $request->validate([
            'kategori_kelas' => 'required|in:01,02,03,04',
            'tanggal_bergabung' => 'required|date',
            'status_aktif' => 'required|boolean',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('foto_profil');

        // Proses jika ada upload foto profil baru
        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama jika ada
            if ($biography->foto_profil && \Illuminate\Support\Facades\Storage::disk('public')->exists($biography->foto_profil)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($biography->foto_profil);
            }
            $data['foto_profil'] = $request->file('foto_profil')->store('biography_photos', 'public');
        }

        $biography->update($data);

        return redirect()->route('admin.biography.index')->with('success', 'Data Biografi berhasil diperbarui.');
    }

    // Fungsi untuk menghapus data biografi
    public function destroy(AthleteBiography $biography)
    {
        // Hapus file foto dari server jika ada
        if ($biography->foto_profil && \Illuminate\Support\Facades\Storage::disk('public')->exists($biography->foto_profil)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($biography->foto_profil);
        }
        
        $biography->delete();

        return redirect()->route('admin.biography.index')->with('success', 'Data Biografi berhasil dihapus secara permanen.');
    }
    public function achievements()
    {
        return $this->hasMany(AthleteAchievement::class);
    }

    // Relasi ke Statistik (One to Many)
    public function statistics()
    {
        return $this->hasMany(AthleteStatistic::class);
    }

    // Relasi ke Galeri (One to Many)
    public function galleries()
    {
        return $this->hasMany(AthleteGallery::class);
    }
    public function show($id)
    {
        // Cari biografi beserta relasi atlet, prestasi, statistik, dan galeri
        $biography = \App\Models\AthleteBiography::with(['athlete', 'achievements', 'statistics', 'galleries'])->findOrFail($id);
        
        return view('admin.biography.show', compact('biography'));
    }
    public function storeAchievement(Request $request, $id)
    {
        $biography = \App\Models\AthleteBiography::findOrFail($id);
        $biography->achievements()->create($request->all());
        return back()->with('success', 'Prestasi berhasil ditambahkan!');
    }

    // Simpan data Statistik
    public function storeStatistic(Request $request, $id)
    {
        $biography = \App\Models\AthleteBiography::findOrFail($id);
        $biography->statistics()->create($request->all());
        return back()->with('success', 'Statistik pertandingan berhasil ditambahkan!');
    }
    public function print($id)
    {
        $biography = \App\Models\AthleteBiography::with(['athlete', 'achievements', 'statistics', 'galleries'])->findOrFail($id);
        return view('admin.biography.print', compact('biography'));
    }
}