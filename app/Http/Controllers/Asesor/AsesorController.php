<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\AssessorScore;
use App\Models\AssessmentCategory;
use App\Models\AssessmentPeriod;
use App\Models\AssessmentObservation;
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

        // 1. DETEKSI POSISI BERMAIN (KIPER / PLAYER)
        // (Catatan: Jika kolom posisi_bermain ada di tabel relasi biografi, 
        // ubah menjadi: $athlete->biography->posisi_bermain ?? '')
        $posisiBermain = $athlete->posisi_bermain ?? ''; 
        $isKiper = stripos($posisiBermain, 'Kiper') !== false || stripos($posisiBermain, 'GK') !== false;
        
        $targetPosisi = $isKiper ? 'Kiper' : 'Player';

        // 2. AMBIL KATEGORI PENILAIAN (Difilter berdasarkan Is_Active dan Target Posisi)
        $categories = AssessmentCategory::where('is_active', true)
                                        ->where('target_posisi', $targetPosisi) // <-- Filter Posisi Ditambahkan
                                        ->orderBy('urutan', 'asc')
                                        ->orderBy('id', 'asc')
                                        ->get();

        // Cek jika asesor sudah pernah menilai atlet ini di periode yang sama
        $nilaiSebelumnya = AssessorScore::with('observation')
                                        ->where('athlete_id', $athlete->id)
                                        ->where('assessor_id', auth()->id())
                                        ->where('periode_bulan', $periodeSekarang)
                                        ->first();

        // 3. KIRIM $targetPosisi KE VIEW UNTUK MENGHIDUPKAN INDIKATOR MERAH
        return view('asesor.penilaian.create', compact(
            'athlete', 
            'periodeSekarang', 
            'categories', 
            'nilaiSebelumnya', 
            'targetPosisi' // <-- Variabel baru ditambahkan ke compact
        ));
    }

    /**
     * 3. Proses Simpan Nilai ke Database
     */
    public function store(Request $request, Athlete $athlete)
    {
        // Validasi input yang diperketat
        $request->validate([
            // Periode harus ada di tabel assessment_periods
            'periode_bulan'             => 'required|string|max:100',
            // sub_scores harus berupa array bersarang dengan nilai numerik 1-5
            'sub_scores'                => 'nullable|array|max:20',
            'sub_scores.*'              => 'nullable|array|max:20',
            'sub_scores.*.*.skor'       => 'required|numeric|min:1|max:5',
            'sub_scores.*.*.aspek'      => 'nullable|string|max:200',
            'sub_scores.*.*.catatan'    => 'nullable|string|max:500',
            // scores fallback juga harus 1-5
            'scores'                    => 'nullable|array|max:20',
            'scores.*'                  => 'nullable|numeric|min:1|max:5',
            // Catatan asesor: max 2000 karakter
            'catatan_asesor'            => 'nullable|string|max:2000',
        ], [
            'sub_scores.*.*.skor.required' => 'Setiap aspek nilai wajib dipilih (skala 1 - 5).',
            'sub_scores.*.*.skor.min'      => 'Skor minimal adalah 1.',
            'sub_scores.*.*.skor.max'      => 'Skor maksimal adalah 5.',
            'catatan_asesor.max'           => 'Catatan maksimal 2000 karakter.',
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
                    // Clamp skor antara 1-5 dan pastikan tipe float
                    $rawSkor = isset($item['skor']) ? (float)$item['skor'] : 3.0;
                    $skor    = max(1.0, min(5.0, $rawSkor));

                    // Sanitasi aspek: strip tag HTML, batasi panjang
                    $aspek   = strip_tags(trim($item['aspek'] ?? ''));
                    $aspek   = mb_substr($aspek, 0, 200);

                    // Sanitasi catatan: strip tag HTML, batasi panjang
                    $catatan = !empty($item['catatan'])
                        ? mb_substr(strip_tags(trim($item['catatan'])), 0, 500)
                        : null;

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

        // Sanitasi catatan asesor dari tag HTML berbahaya
        $catatanBersih = $request->catatan_asesor
            ? mb_substr(strip_tags(trim($request->catatan_asesor)), 0, 2000)
            : null;

        $assessorScore = AssessorScore::updateOrCreate(
            [
                'athlete_id'    => $athlete->id,
                'assessor_id'   => auth()->id(),
                'periode_bulan' => $request->periode_bulan,
            ],
            array_merge($legacy, [
                'scores'         => $scoresData,
                'catatan_asesor' => $catatanBersih,
            ])
        );

        // Simpan / Perbarui Observasi Lapangan (Kualitatif)
        if ($request->has('observasi')) {
            AssessmentObservation::updateOrCreate(
                ['assessor_score_id' => $assessorScore->id],
                [
                    'kekuatan_utama'    => $request->input('observasi.kekuatan_utama') ? strip_tags(trim($request->input('observasi.kekuatan_utama'))) : null,
                    'area_ditingkatkan' => $request->input('observasi.area_ditingkatkan') ? strip_tags(trim($request->input('observasi.area_ditingkatkan'))) : null,
                    'catatan_perilaku'  => $request->input('observasi.catatan_perilaku') ? strip_tags(trim($request->input('observasi.catatan_perilaku'))) : null,
                    'fokus_latihan'     => $request->input('observasi.fokus_latihan') ? strip_tags(trim($request->input('observasi.fokus_latihan'))) : null,
                ]
            );
        }

        return redirect()->route('asesor.penilaian.index', ['periode' => $request->periode_bulan])
                         ->with('success', "Penilaian untuk {$athlete->nama} ({$request->periode_bulan}) berhasil disimpan!");
    }
}