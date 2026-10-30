<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\AthleteBiography;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 
use Maatwebsite\Excel\Facades\Excel; 
use App\Imports\BiographiesImport;

class AthleteBiographyController extends Controller
{
    public function index(Request $request)
    {
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
            $path = $request->file('foto_profil')->store('biography_photos', 'public');
            $data['foto_profil'] = $path;

            // Mirror otomatis ke public/uploads/biography_photos agar langsung terbaca di shared hosting tanpa storage:link
            try {
                $storageFile = storage_path('app/public/' . $path);
                $destDir = public_path('uploads/biography_photos');
                if (!file_exists($destDir)) {
                    @mkdir($destDir, 0755, true);
                }
                @copy($storageFile, $destDir . '/' . basename($path));
            } catch (\Throwable $e) {}
        } else {
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

        if ($request->hasFile('foto_profil')) {
            // Hapus file fisik foto lama agar hosting tidak penuh
            if ($biography->foto_profil) {
                Storage::disk('public')->delete($biography->foto_profil);
                $oldUpload = public_path('uploads/' . ltrim($biography->foto_profil, '/'));
                if (file_exists($oldUpload)) {
                    @unlink($oldUpload);
                }
            }

            // Simpan foto baru
            $path = $request->file('foto_profil')->store('biography_photos', 'public');
            $data['foto_profil'] = $path;

            // Mirror otomatis ke public/uploads/biography_photos
            try {
                $storageFile = storage_path('app/public/' . $path);
                $destDir = public_path('uploads/biography_photos');
                if (!file_exists($destDir)) {
                    @mkdir($destDir, 0755, true);
                }
                @copy($storageFile, $destDir . '/' . basename($path));
            } catch (\Throwable $e) {}
        }

        $biography->update($data);

        return redirect()->route('admin.biography.index')->with('success', 'Data Biografi berhasil diperbarui.');
    }

    public function destroy(AthleteBiography $biography)
    {
        // Hapus file fisik dari storage sebelum data dihapus dari database
        if ($biography->foto_profil) {
            Storage::disk('public')->delete($biography->foto_profil);
            $uploadFile = public_path('uploads/' . ltrim($biography->foto_profil, '/'));
            if (file_exists($uploadFile)) {
                @unlink($uploadFile);
            }
        }
        
        $biography->delete();

        return redirect()->route('admin.biography.index')->with('success', 'Data Biografi berhasil dihapus secara permanen.');
    }

    public function show($id)
    {
        $biography = AthleteBiography::with(['athlete', 'achievements', 'statistics', 'galleries'])->findOrFail($id);
        return view('admin.biography.show', compact('biography'));
    }

    public function storeAchievement(Request $request, $id)
    {
        $biography = AthleteBiography::with('athlete')->findOrFail($id);

        $validated = $request->validate([
            'nama_turnamen' => 'required|string|max:255',
            'pencapaian'    => 'required|string|max:255',
            'tingkat'       => 'required|string|max:100',
            'kategori'      => 'nullable|string|max:100',
            'tanggal'       => 'required|date',
            'penghargaan'   => 'nullable|string|max:255',
            'keterangan'    => 'nullable|string',
        ]);

        if (empty($validated['kategori'])) {
            $validated['kategori'] = !empty($biography->athlete->kelompok_umur)
                ? 'KU-' . $biography->athlete->kelompok_umur
                : '-';
        }

        $biography->achievements()->create($validated);
        return back()->with('success', 'Prestasi berhasil ditambahkan!');
    }

    public function storeStatistic(Request $request, $id)
    {
        $biography = AthleteBiography::findOrFail($id);

        $validated = $request->validate([
            'nama_pertandingan' => 'required|string|max:255',
            'nama_lawan'        => 'required|string|max:255',
            'tanggal'           => 'required|date',
            'menit_bermain'     => 'required|integer|min:0|max:200',
            'gol'               => 'required|integer|min:0|max:50',
            'assist'            => 'required|integer|min:0|max:50',
            'saves'             => 'nullable|integer|min:0|max:100',
            'clean_sheet'       => 'nullable|boolean',
            'nilai_evaluasi'    => 'nullable|integer|min:1|max:100',
            'catatan_coach'     => 'nullable|string|max:1000',
        ], [
            'nama_pertandingan.required' => 'Nama pertandingan wajib diisi.',
            'nama_lawan.required'        => 'Nama tim lawan wajib diisi.',
            'tanggal.required'           => 'Tanggal pertandingan wajib diisi.',
            'menit_bermain.required'     => 'Menit bermain wajib diisi.',
            'gol.required'               => 'Jumlah gol wajib diisi.',
            'assist.required'            => 'Jumlah assist wajib diisi.',
        ]);

        $biography->statistics()->create($validated);
        return back()->with('success', 'Statistik pertandingan berhasil ditambahkan!');
    }

    public function print($id)
    {
        $biography = AthleteBiography::with(['athlete', 'achievements', 'statistics', 'galleries'])->findOrFail($id);
        return view('admin.biography.print', compact('biography'));
    }
    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            // Jalankan proses import
            Excel::import(new BiographiesImport, $request->file('file_excel'));
            
            return redirect()->route('admin.biography.index')->with('success', 'Data Biografi berhasil di-import dari Excel!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal meng-import data: ' . $e->getMessage());
        }
    }
}