<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Exports\AthletesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AthletesImport;

class AthleteController extends Controller
{
    public function index(Request $request)
    {
        // Mulai query dasar dengan mengambil relasi akun wali murid
        $query = Athlete::with('user')->orderBy('nama');

        // Jika Admin mengetik sesuatu di kotak pencarian
        if ($request->filled('cari')) {
            $cari = $request->cari;
            
            $query->where(function($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                  ->orWhere('kode_barcode', 'like', "%{$cari}%")
                  ->orWhere('kelompok_umur', 'like', "%{$cari}%")
                  ->orWhere('kelompok_latihan', 'like', "%{$cari}%")
                  ->orWhere('posisi_bermain', 'like', "%{$cari}%")
                  ->orWhere('nomor_punggung', 'like', "%{$cari}%")
                  ->orWhere('nomor_wa_ortu', 'like', "%{$cari}%")
                  // Mencari berdasarkan Username akun wali murid
                  ->orWhereHas('user', function($u) use ($cari) {
                      $u->where('username', 'like', "%{$cari}%");
                  });
            });
        }

        // Filter Dropdown
        if ($request->filled('kelompok_umur')) {
            $query->where('kelompok_umur', $request->kelompok_umur);
        }
        if ($request->filled('kelompok_latihan')) {
            $query->where('kelompok_latihan', $request->kelompok_latihan);
        }
        if ($request->filled('posisi_bermain')) {
            $query->where('posisi_bermain', $request->posisi_bermain);
        }

        // Gunakan withQueryString() agar pagination tetap mengingat kata kunci pencarian
        $athletes = $query->paginate(15)->withQueryString();

        // Ambil data unik untuk dropdown filter
        $kelompokUmurList = Athlete::select('kelompok_umur')->distinct()->whereNotNull('kelompok_umur')->where('kelompok_umur', '!=', '')->pluck('kelompok_umur');
        $kelompokLatihanList = Athlete::select('kelompok_latihan')->distinct()->whereNotNull('kelompok_latihan')->where('kelompok_latihan', '!=', '')->pluck('kelompok_latihan');
        $posisiBermainList = Athlete::select('posisi_bermain')->distinct()->whereNotNull('posisi_bermain')->where('posisi_bermain', '!=', '')->pluck('posisi_bermain');

        return view('admin.athletes.index', compact('athletes', 'kelompokUmurList', 'kelompokLatihanList', 'posisiBermainList'));
    }

    public function create()
    {
        // Mengambil daftar user dengan role 'wali_murid' untuk dikaitkan ke atlet
        $parents = User::query()->where('role', 'wali_murid')->get();
        return view('admin.athletes.create', compact('parents'));
    }

    public function store(Request $request)
    {
        // ... (validasi input sebelumnya tetap sama) ...

        // GENERATE KODE BARCODE UNIK OTOMATIS (Contoh: SSA-2026-8912)
        $kodeBarcode = 'SSA-' . date('Y') . '-' . rand(1000, 9999);
        while (\App\Models\Athlete::query()->where('kode_barcode', $kodeBarcode)->exists()) {
            $kodeBarcode = 'SSA-' . date('Y') . '-' . rand(1000, 9999);
        }

        // Kode auto-generate Username & Password
        $cleanName = Str::slug($request->nama, '');
        $username = $cleanName;
        $counter = 1;
        while (User::query()->where('username', $username)->exists()) {
            $username = $cleanName . $counter;
            $counter++;
        }

        $password = \Carbon\Carbon::parse($request->tanggal_lahir)->format('dmY');

        $newUser = User::create([
            'name'     => $request->nama_wali,
            'username' => $username,
            'password' => Hash::make($password),
            'role'     => 'wali_murid',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/athletes'), $fileName);
            $fotoPath = 'uploads/athletes/' . $fileName;
        }

        Athlete::create([
            'nama'             => $request->nama,
            'kode_barcode'     => $kodeBarcode,
            'kelompok_umur'    => $request->kelompok_umur,
            'kelompok_latihan' => $request->kelompok_latihan,
            'nomor_punggung'   => $request->nomor_punggung ?? null,
            'tanggal_lahir'    => $request->tanggal_lahir,
            'posisi_bermain'   => $request->posisi_bermain ?? null,
            'nomor_wa_ortu'    => $request->nomor_wa_ortu,
            'nomor_wa'         => $request->nomor_wa ?? null,
            'alamat'           => $request->alamat ?? null,
            'user_id'          => $newUser->id,
            'foto'             => $fotoPath,
        ]);

        return redirect()->route('admin.athletes.index')->with('success', 'Data Siswa & Barcode berhasil dibuat!');
    }

    // METHOD BARU 1: Cetak Kartu Satuan (Per Siswa)
    public function printCard(Athlete $athlete)
    {
        // Jika siswa lawas belum punya barcode, buatkan otomatis sekarang
        if (!$athlete->kode_barcode) {
            $athlete->update(['kode_barcode' => 'SSA-' . date('Y') . '-' . rand(1000, 9999)]);
        }
        
        return view('admin.athletes.print_card', compact('athlete'));
    }

    // METHOD BARU 2: Cetak Semua Kartu Sekaligus (Batch Print)
    public function printAllCards()
    {
        $athletes = Athlete::orderBy('kelompok_umur', 'asc')->orderBy('nama', 'asc')->get();
        
        // Pastikan semua siswa lawas sudah punya barcode
        foreach ($athletes as $item) {
            if (!$item->kode_barcode) {
                $item->update(['kode_barcode' => 'SSA-' . date('Y') . '-' . rand(1000, 9999)]);
            }
        }

        return view('admin.athletes.print_all_cards', compact('athletes'));
    }

    public function edit(Athlete $athlete)
    {
        $parents = User::query()->where('role', 'wali_murid')->get();
        return view('admin.athletes.edit', compact('athlete', 'parents'));
    }

    public function update(Request $request, Athlete $athlete)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_punggung' => 'nullable|string|max:10',
            'tanggal_lahir' => 'nullable|date',
            'posisi_bermain' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'nomor_wa' => 'nullable|string|max:20',
            'nomor_wa_ortu' => 'required|string|max:20',
            'user_id' => 'nullable|exists:users,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5242880', // max 5 GB
        ]);

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($athlete->foto && file_exists(public_path($athlete->foto))) {
                unlink(public_path($athlete->foto));
            }
            $file = $request->file('foto');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/athletes'), $fileName);
            $validated['foto'] = 'uploads/athletes/' . $fileName;
        } else {
            unset($validated['foto']);
        }

        $athlete->update($validated);

        return redirect()->route('admin.athletes.index')->with('success', 'Data Atlet berhasil diperbarui!');
    }

    public function destroy(Athlete $athlete)
    {
        $athlete->delete();
        return redirect()->route('admin.athletes.index')->with('success', 'Data Atlet berhasil dihapus!');
    }
    public function exportExcel()
    {
        $namaFile = 'Data_Atlet_Superseed_Academy_' . date('Y-m-d_H-i') . '.xlsx';
        return Excel::download(new AthletesExport, $namaFile);
    }
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        Excel::import(new AthletesImport, $request->file('file_excel'));

        return redirect()->back()->with('success', 'Ratusan Data Siswa berhasil diimpor & Akun Wali mereka otomatis aktif!');
    }
}