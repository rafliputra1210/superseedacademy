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
        $validated = $request->validate([
            'athlete_id'        => 'required|exists:athletes,id',
            'kategori_kelas'    => 'required|in:01,02,03,04',
            'tanggal_bergabung' => 'required|date',
            'posisi'            => 'nullable|string',
            'foto_profil'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $data = $request->except('foto_profil');

        if ($request->hasFile('foto_profil')) {
            $file = $request->file('foto_profil');
            $ext = strtolower($file->guessExtension() ?: 'jpg');
            $fileName = time() . '_' . \Illuminate\Support\Str::random(20) . '.' . $ext;

            $destDir = public_path('uploads/biography_photos');
            if (!file_exists($destDir)) {
                mkdir($destDir, 0755, true);
            }
            $file->move($destDir, $fileName);

            // Copy juga ke storage disk public untuk redundansi
            $storageDir = storage_path('app/public/biography_photos');
            if (!file_exists($storageDir)) {
                @mkdir($storageDir, 0755, true);
            }
            @copy($destDir . '/' . $fileName, $storageDir . '/' . $fileName);

            $data['foto_profil'] = 'uploads/biography_photos/' . $fileName;
        } else {
            // Jika tidak upload, gunakan foto dari master data atlet jika ada
            $athlete = Athlete::find($request->athlete_id);
            if ($athlete && !empty($athlete->foto)) {
                $data['foto_profil'] = $athlete->foto;
            }
        }

        AthleteBiography::create($data);

        return redirect()->route('admin.biography.index')->with('success', 'Biografi berhasil ditambahkan.');
    }

    public function edit(AthleteBiography $biography)
    {
        return view('admin.biography.edit', compact('biography'));
    }

    public function update(Request $request, AthleteBiography $biography)
    {
        $request->validate([
            'kategori_kelas'    => 'required|in:01,02,03,04',
            'tanggal_bergabung' => 'required|date',
            'status_aktif'      => 'required|boolean',
            'foto_profil'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $data = $request->except('foto_profil');

        // Proses jika ada upload foto profil baru
        if ($request->hasFile('foto_profil')) {
            $file = $request->file('foto_profil');
            $ext = strtolower($file->guessExtension() ?: 'jpg');
            $fileName = time() . '_' . \Illuminate\Support\Str::random(20) . '.' . $ext;

            // Hapus foto lama jika ada
            if ($biography->foto_profil) {
                $oldBase = basename($biography->foto_profil);
                $oldFiles = [
                    public_path('uploads/biography_photos/' . $oldBase),
                    public_path($biography->foto_profil),
                    storage_path('app/public/biography_photos/' . $oldBase),
                    storage_path('app/public/' . $biography->foto_profil),
                ];
                foreach ($oldFiles as $f) {
                    if (file_exists($f) && is_file($f)) {
                        @unlink($f);
                    }
                }
            }

            // Simpan ke public/uploads/biography_photos
            $destDir = public_path('uploads/biography_photos');
            if (!file_exists($destDir)) {
                mkdir($destDir, 0755, true);
            }
            $file->move($destDir, $fileName);

            // Copy juga ke storage disk public
            $storageDir = storage_path('app/public/biography_photos');
            if (!file_exists($storageDir)) {
                @mkdir($storageDir, 0755, true);
            }
            @copy($destDir . '/' . $fileName, $storageDir . '/' . $fileName);

            $data['foto_profil'] = 'uploads/biography_photos/' . $fileName;
        }

        $biography->update($data);

        return redirect()->route('admin.biography.index')->with('success', 'Data Biografi berhasil diperbarui.');
    }

    // Fungsi untuk menghapus data biografi
    public function destroy(AthleteBiography $biography)
    {
        // Hapus file foto dari server jika ada
        if ($biography->foto_profil) {
            $oldBase = basename($biography->foto_profil);
            $oldFiles = [
                public_path('uploads/biography_photos/' . $oldBase),
                public_path($biography->foto_profil),
                storage_path('app/public/biography_photos/' . $oldBase),
                storage_path('app/public/' . $biography->foto_profil),
            ];
            foreach ($oldFiles as $f) {
                if (file_exists($f) && is_file($f)) {
                    @unlink($f);
                }
            }
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
        $biography = \App\Models\AthleteBiography::with('athlete')->findOrFail($id);

        $validated = $request->validate([
            'nama_turnamen' => 'required|string|max:255',
            'pencapaian'    => 'required|string|max:255',
            'tingkat'       => 'required|string|max:100',
            'kategori'      => 'nullable|string|max:100',
            'tanggal'       => 'required|date',
            'penghargaan'   => 'nullable|string|max:255',
            'keterangan'    => 'nullable|string',
        ]);

        // Pastikan kolom kategori selalu memiliki nilai (fallback KU atlet atau tanda '-')
        if (empty($validated['kategori'])) {
            $validated['kategori'] = !empty($biography->athlete->kelompok_umur)
                ? 'KU-' . $biography->athlete->kelompok_umur
                : '-';
        }

        $biography->achievements()->create($validated);
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