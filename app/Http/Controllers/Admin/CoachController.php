<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Exports\CoachesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CoachesImport;

class CoachController extends Controller
{
    public function index()
    {
        $coaches = Coach::latest('created_at')->paginate(10);
        return view('admin.coaches.index', compact('coaches'));
    }

    public function create()
    {
        return view('admin.coaches.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_wa' => 'nullable|string|max:20',
            'status_lisensi' => 'required|in:berlisensi,tidak_berlisensi',
            'detail_lisensi' => 'nullable|string|max:100',
            'referensi' => 'nullable|string',
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5242880', // max 5 GB
        ]);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fileName = time() . '_' . Str::slug($validated['nama'], '_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/coaches'), $fileName);
            $validated['foto'] = 'uploads/coaches/' . $fileName;
        }

        Coach::create($validated);

        return redirect()->route('admin.coaches.index')->with('success', 'Data Coach berhasil ditambahkan!');
    }

    public function edit(Coach $coach)
    {
        return view('admin.coaches.edit', compact('coach'));
    }

    public function update(Request $request, Coach $coach)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_wa' => 'nullable|string|max:20',
            'status_lisensi' => 'required|in:berlisensi,tidak_berlisensi',
            'detail_lisensi' => 'nullable|string|max:100',
            'referensi' => 'nullable|string',
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5242880', // max 5 GB
        ]);

        if ($request->hasFile('foto')) {
            if ($coach->foto && file_exists(public_path($coach->foto))) {
                unlink(public_path($coach->foto));
            }
            $file = $request->file('foto');
            $fileName = time() . '_' . Str::slug($validated['nama'], '_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/coaches'), $fileName);
            $validated['foto'] = 'uploads/coaches/' . $fileName;
        } else {
            unset($validated['foto']);
        }

        $coach->update($validated);

        return redirect()->route('admin.coaches.index')->with('success', 'Data Coach berhasil diperbarui!');
    }

    public function destroy(Coach $coach)
    {
        if ($coach->foto && file_exists(public_path($coach->foto))) {
            unlink(public_path($coach->foto));
        }
        Coach::destroy($coach->id);

        return redirect()->route('admin.coaches.index')->with('success', 'Data Coach berhasil dihapus!');
    }
    public function exportExcel()
    {
        $namaFile = 'Data_Coach_Superseed_Academy_' . date('Y-m-d_H-i') . '.xlsx';
        return Excel::download(new CoachesExport, $namaFile);
    }
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        Excel::import(new CoachesImport, $request->file('file_excel'));

        return redirect()->back()->with('success', 'Data Coach & Pelatih berhasil diimpor dari file Excel!');
    }
}