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

    // ... function update() dan destroy() ...
}