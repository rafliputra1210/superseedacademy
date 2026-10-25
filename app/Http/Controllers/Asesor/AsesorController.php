<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\AssessorScore;
use App\Models\AssessmentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AsesorController extends Controller
{
    /**
     * 1. Halaman Daftar Atlet untuk Asesor
     * Menampilkan daftar atlet dan status apakah asesor ini sudah menilai mereka di bulan ini.
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $periodeSekarang = $request->get('periode', Carbon::now()->translatedFormat('F Y'));
        
        $athletes = Athlete::orderBy('kelompok_umur', 'asc')->orderBy('nama', 'asc')->get();

        // Cari ID atlet mana saja yang SUDAH dinilai oleh asesor ini pada periode tersebut
        $penilaianSelesai = AssessorScore::where('assessor_id', auth()->id())
                                         ->where('periode_bulan', $periodeSekarang)
                                         ->pluck('athlete_id')
                                         ->toArray();

        return view('asesor.penilaian.index', compact('athletes', 'periodeSekarang', 'penilaianSelesai'));
    }

    /**
     * 2. Halaman Form Penilaian (Create/Edit)
     * Membuka form input nilai untuk 1 atlet spesifik berdasarkan kategori penilaian aktif.
     */
    public function create(Athlete $athlete)
    {
        Carbon::setLocale('id');
        $periodeSekarang = Carbon::now()->translatedFormat('F Y');

        // Ambil semua kategori penilaian yang sedang aktif
        $categories = AssessmentCategory::where('is_active', true)
                                        ->orderBy('urutan', 'asc')
                                        ->orderBy('id', 'asc')
                                        ->get();

        // Cek jika asesor sudah pernah menilai atlet ini di bulan yang sama
        $nilaiSebelumnya = AssessorScore::where('athlete_id', $athlete->id)
                                        ->where('assessor_id', auth()->id())
                                        ->where('periode_bulan', $periodeSekarang)
                                        ->first();

        return view('asesor.penilaian.create', compact('athlete', 'periodeSekarang', 'categories', 'nilaiSebelumnya'));
    }

    /**
     * 3. Proses Simpan Nilai ke Database
     */
    public function store(Request $request, Athlete $athlete)
    {
        // Validasi input
        $request->validate([
            'periode_bulan'  => 'required|string',
            'scores'         => 'required|array',
            'scores.*'       => 'required|numeric|min:0|max:100',
            'catatan_asesor' => 'nullable|string|max:1000',
        ], [
            'scores.*.required' => 'Setiap aspek nilai wajib diisi.',
            'scores.*.min'      => 'Nilai minimal adalah 0.',
            'scores.*.max'      => 'Nilai maksimal adalah 100.',
        ]);

        $scoresData = $request->scores;

        // Ambil kategori untuk sinkronisasi kolom legacy (teknik, taktik, fisik, mental)
        $categories = AssessmentCategory::whereIn('id', array_keys($scoresData))->get();
        $legacy = [
            'nilai_teknik' => 0,
            'nilai_taktik' => 0,
            'nilai_fisik'  => 0,
            'nilai_mental' => 0,
        ];

        foreach ($categories as $cat) {
            $slug = Str::slug($cat->nama);
            $val = $scoresData[$cat->id] ?? 0;
            if ($slug === 'teknik') $legacy['nilai_teknik'] = $val;
            if ($slug === 'taktik') $legacy['nilai_taktik'] = $val;
            if ($slug === 'fisik')  $legacy['nilai_fisik']  = $val;
            if ($slug === 'mental') $legacy['nilai_mental'] = $val;
        }

        AssessorScore::updateOrCreate(
            [
                'athlete_id'    => $athlete->id,
                'assessor_id'   => auth()->id(),
                'periode_bulan' => $request->periode_bulan,
            ],
            array_merge($legacy, [
                'scores'         => $scoresData,
                'catatan_asesor' => $request->catatan_asesor,
            ])
        );

        return redirect()->route('asesor.penilaian.index')
                         ->with('success', "Penilaian untuk {$athlete->nama} berhasil disimpan!");
    }
}