<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\AssessorScore;
use App\Models\AssessmentCategory;
use App\Models\AssessmentPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AsesorController extends Controller
{
    /**
     * 1. Halaman Daftar Atlet untuk Asesor
     * Menampilkan daftar atlet dan status apakah asesor ini sudah menilai mereka di periode yang dipilih.
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        $activePeriodName = AssessmentPeriod::getActivePeriodName();
        $periodeSekarang = $request->get('periode', $activePeriodName);

        $periods = AssessmentPeriod::orderBy('id', 'desc')->get();
        $athletes = Athlete::orderBy('kelompok_umur', 'asc')->orderBy('nama', 'asc')->get();

        // Cari ID atlet mana saja yang SUDAH dinilai oleh asesor ini pada periode tersebut
        $penilaianSelesai = AssessorScore::where('assessor_id', auth()->id())
                                         ->where('periode_bulan', $periodeSekarang)
                                         ->pluck('athlete_id')
                                         ->toArray();

        return view('asesor.penilaian.index', compact('athletes', 'periodeSekarang', 'penilaianSelesai', 'periods', 'activePeriodName'));
    }

    /**
     * 2. Halaman Form Penilaian (Create/Edit)
     * Membuka form input nilai untuk 1 atlet spesifik berdasarkan kategori penilaian aktif dan periode yang dipilih.
     */
    public function create(Request $request, Athlete $athlete)
    {
        Carbon::setLocale('id');
        
        $activePeriodName = AssessmentPeriod::getActivePeriodName();
        $periodeSekarang = $request->get('periode', $activePeriodName);

        // Ambil semua kategori penilaian yang sedang aktif
        $categories = AssessmentCategory::where('is_active', true)
                                        ->orderBy('urutan', 'asc')
                                        ->orderBy('id', 'asc')
                                        ->get();

        // Cek jika asesor sudah pernah menilai atlet ini di periode yang sama
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
            'periode_bulan'       => 'required|string',
            'sub_scores'          => 'nullable|array',
            'sub_scores.*.*.skor' => 'required|numeric|min:1|max:5',
            'scores'              => 'nullable|array',
            'catatan_asesor'      => 'nullable|string|max:1000',
        ], [
            'sub_scores.*.*.skor.required' => 'Setiap aspek nilai wajib dipilih (skala 1 - 5).',
            'sub_scores.*.*.skor.min'      => 'Skor minimal adalah 1.',
            'sub_scores.*.*.skor.max'      => 'Skor maksimal adalah 5.',
        ]);

        $activeCategories = AssessmentCategory::where('is_active', true)->get();
        $scoresData = [];

        $legacy = [
            'nilai_teknik' => 0,
            'nilai_taktik' => 0,
            'nilai_fisik'  => 0,
            'nilai_mental' => 0,
        ];

        foreach ($activeCategories as $cat) {
            $catId = $cat->id;

            // Jika ada input sub_scores untuk kategori ini
            if ($request->has("sub_scores.{$catId}") && is_array($request->input("sub_scores.{$catId}"))) {
                $subInputs = $request->input("sub_scores.{$catId}");
                $subScoresArr = [];
                $totalScore = 0;
                $count = 0;

                foreach ($subInputs as $item) {
                    $skor = isset($item['skor']) ? (float)$item['skor'] : 3;
                    $aspek = $item['aspek'] ?? '';
                    $catatan = !empty($item['catatan']) ? trim($item['catatan']) : null;

                    $subScoresArr[] = [
                        'aspek'   => $aspek,
                        'skor'    => $skor,
                        'catatan' => $catatan,
                    ];
                    $totalScore += $skor;
                    $count++;
                }

                $categoryScore = $count > 0 ? round($totalScore / $count, 2) : 0;

                $scoresData[$catId] = [
                    'score'      => $categoryScore,
                    'sub_points' => $subScoresArr,
                ];
            } elseif ($request->has("scores.{$catId}")) {
                // Fallback direct score
                $directVal = (float)$request->input("scores.{$catId}", 0);
                $scoresData[$catId] = [
                    'score'      => $directVal,
                    'sub_points' => [],
                ];
                $categoryScore = $directVal;
            } else {
                $categoryScore = 0;
            }

            // Sync legacy columns
            $slug = Str::slug($cat->nama);
            if ($slug === 'teknik') $legacy['nilai_teknik'] = $categoryScore;
            if ($slug === 'taktik') $legacy['nilai_taktik'] = $categoryScore;
            if ($slug === 'fisik')  $legacy['nilai_fisik']  = $categoryScore;
            if ($slug === 'mental') $legacy['nilai_mental'] = $categoryScore;
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

        return redirect()->route('asesor.penilaian.index', ['periode' => $request->periode_bulan])
                         ->with('success', "Penilaian untuk {$athlete->nama} ({$request->periode_bulan}) berhasil disimpan!");
    }
}