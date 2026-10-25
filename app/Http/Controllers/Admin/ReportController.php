<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Athlete;
use App\Models\User;
use App\Models\AssessorScore;
use App\Models\Raport;
use App\Models\AssessmentCategory;
use App\Models\AssessmentPeriod;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReportController extends Controller
{

    public function index(Request $request)
    {
        Carbon::setLocale('id');

        $activePeriod = AssessmentPeriod::getActivePeriod();
        $periodeAktifName = AssessmentPeriod::getActivePeriodName();

        // Filter periode, default ke periode aktif instansi
        $periodeSekarang = $request->get('periode', $periodeAktifName);

        // Ambil semua master periode yang terdaftar
        $periods = AssessmentPeriod::orderBy('id', 'desc')->get();

        // Ambil riwayat nama periode yang pernah ada di tabel Raport atau AssessorScore
        $historyPeriods = Raport::select('periode')->distinct()->pluck('periode')
            ->merge(AssessorScore::select('periode_bulan')->distinct()->pluck('periode_bulan'))
            ->filter()
            ->unique()
            ->values();
        
        // Ambil semua atlet
        $athletes = Athlete::orderBy('kelompok_umur', 'asc')->orderBy('nama', 'asc')->get();
        
        // Ambil semua akun yang bertugas sebagai asesor
        $assessors = User::where('role', 'asesor')->get();

        // Ambil kategori penilaian aktif
        $categories = AssessmentCategory::where('is_active', true)->orderBy('urutan', 'asc')->get();

        // Ambil semua nilai mentah dari asesor di periode ini
        $allScores = AssessorScore::where('periode_bulan', $periodeSekarang)->get();

        // Ambil raport final yang sudah digenerate di periode ini
        $raportFinal = Raport::where('periode', $periodeSekarang)->get();

        return view('admin.reports.index', compact(
            'athletes', 'assessors', 'categories', 'allScores', 
            'periodeSekarang', 'raportFinal', 'periods', 'activePeriod', 
            'periodeAktifName', 'historyPeriods'
        ));
    }

    /**
     * Ubah periode aktif instansi
     */
    public function setActivePeriod(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:assessment_periods,id',
        ]);

        AssessmentPeriod::setActiveById($request->period_id);
        $period = AssessmentPeriod::find($request->period_id);

        return redirect()->route('admin.reports.index', ['periode' => $period->nama])
                         ->with('success', "Periode aktif instansi berhasil diubah menjadi: {$period->nama}!");
    }

    /**
     * Tambah periode baru (semester, bulanan, atau custom)
     */
    public function storePeriod(Request $request)
    {
        $request->validate([
            'nama'         => 'required|string|max:100|unique:assessment_periods,nama',
            'tipe'         => 'required|in:semester,bulanan,kustom',
            'tahun_ajaran' => 'nullable|string|max:20',
            'is_active'    => 'nullable',
            'keterangan'   => 'nullable|string|max:255',
        ], [
            'nama.unique' => 'Nama periode tersebut sudah terdaftar.',
        ]);

        $isActive = $request->boolean('is_active');
        if ($isActive) {
            AssessmentPeriod::query()->update(['is_active' => false]);
        }

        $period = AssessmentPeriod::create([
            'nama'         => trim($request->nama),
            'tipe'         => $request->tipe,
            'tahun_ajaran' => $request->tahun_ajaran,
            'is_active'    => $isActive,
            'keterangan'   => $request->keterangan,
        ]);

        return redirect()->route('admin.reports.index', ['periode' => $period->nama])
                         ->with('success', "Periode baru '{$period->nama}' berhasil ditambahkan" . ($isActive ? ' dan dijadikan periode aktif instansi!' : '!'));
    }

    /**
     * Hapus periode
     */
    public function destroyPeriod(AssessmentPeriod $period)
    {
        if ($period->is_active) {
            return back()->with('error', 'Tidak dapat menghapus periode yang sedang aktif! Tetapkan periode lain sebagai aktif terlebih dahulu.');
        }

        $name = $period->nama;
        $period->delete();

        return back()->with('success', "Periode '{$name}' berhasil dihapus.");
    }
    public function create()
    {
        $athletes = Athlete::orderBy('nama', 'asc')->get();
        return view('admin.reports.create', compact('athletes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'athlete_id'       => 'required|exists:athletes,id',
            'nomor_asesmen'    => 'nullable|string|max:50',
            'tanggal_asesmen'  => 'required|date',
            'asesor_nama'      => 'required|string|max:100',
            
            // Array Input Penilaian
            'teknis_skor.*'    => 'required|integer|min:1|max:5',
            'teknis_catatan.*' => 'nullable|string',
            'taktik_skor.*'    => 'required|integer|min:1|max:5',
            'taktik_catatan.*' => 'nullable|string',
            'fisik_skor.*'     => 'required|integer|min:1|max:5',
            'fisik_catatan.*'  => 'nullable|string',

            // Observasi Lapangan
            'kekuatan_utama'   => 'nullable|string',
            'area_peningkatan' => 'nullable|string',
            'catatan_perilaku' => 'nullable|string',
        ]);

        // Menyusun ulang data array untuk disimpan sebagai JSON
        $teknis = [];
        foreach ($request->teknis_aspek as $i => $aspek) {
            $teknis[] = ['aspek' => $aspek, 'skor' => $request->teknis_skor[$i], 'catatan' => $request->teknis_catatan[$i]];
        }

        $taktikal = [];
        foreach ($request->taktik_aspek as $i => $aspek) {
            $taktikal[] = ['aspek' => $aspek, 'skor' => $request->taktik_skor[$i], 'catatan' => $request->taktik_catatan[$i]];
        }

        $fisik = [];
        foreach ($request->fisik_aspek as $i => $aspek) {
            $fisik[] = ['aspek' => $aspek, 'skor' => $request->fisik_skor[$i], 'catatan' => $request->fisik_catatan[$i]];
        }

        Report::create([
            'athlete_id'         => $request->athlete_id,
            'nomor_asesmen'      => $request->nomor_asesmen,
            'tanggal_asesmen'    => $request->tanggal_asesmen,
            'asesor_nama'        => $request->asesor_nama,
            'aspek_teknis'       => json_encode($teknis),
            'aspek_taktikal'     => json_encode($taktikal),
            'aspek_fisik_mental' => json_encode($fisik),
            'kekuatan_utama'     => $request->kekuatan_utama,
            'area_peningkatan'   => $request->area_peningkatan,
            'catatan_perilaku'   => $request->catatan_perilaku,
        ]);

        return redirect()->route('admin.reports.index')->with('success', 'Form Asesmen Pemain berhasil disimpan!');
    }

    public function toggleArchive(Raport $raport)
    {
        $raport->update([
            'is_archived' => !$raport->is_archived
        ]);

        $status = $raport->is_archived ? 'diarsipkan' : 'dikembalikan ke status aktif';
        $athleteName = $raport->athlete ? $raport->athlete->nama : 'atlet';
        return redirect()->back()->with('success', "Raport {$athleteName} ({$raport->periode}) berhasil {$status}!");
    }

    public function destroy(Report $report)
    {
        Report::destroy($report->id);
        return redirect()->back()->with('success', 'Asesmen berhasil dihapus!');
    }
    public function generateFinalRaport(Request $request, Athlete $athlete)
    {
        $periode = $request->periode_bulan; // "Oktober 2026"

        // 1. Ambil semua nilai dari para asesor untuk anak ini di bulan ini
        $scores = AssessorScore::where('athlete_id', $athlete->id)
                               ->where('periode_bulan', $periode)
                               ->get();

        // Validasi: Pastikan minimal ada penilaian masuk
        if ($scores->count() < 1) {
            return back()->with('error', 'Belum ada asesor yang memberikan nilai!');
        }

        // 2. Hitung Rata-rata Otomatis per Kategori Aktif
        $categories = AssessmentCategory::where('is_active', true)->orderBy('urutan', 'asc')->get();
        $scoresSummary = [];
        $legacy = [
            'nilai_teknik_final' => 0,
            'nilai_taktik_final' => 0,
            'nilai_fisik_final'  => 0,
            'nilai_mental_final' => 0,
        ];

        foreach ($categories as $cat) {
            $catScores = [];
            $subPointsMap = [];

            foreach ($scores as $s) {
                $val = $s->getCategoryScore($cat->id, $cat->nama);
                if ($val !== null && is_numeric($val)) {
                    $catScores[] = (float) $val;
                }

                $subs = $s->getCategorySubPoints($cat->id);
                foreach ($subs as $subItem) {
                    $aspek = $subItem['aspek'] ?? '';
                    if (!$aspek) continue;

                    if (!isset($subPointsMap[$aspek])) {
                        $subPointsMap[$aspek] = [
                            'scores' => [],
                            'notes'  => [],
                        ];
                    }
                    if (isset($subItem['skor']) && is_numeric($subItem['skor'])) {
                        $subPointsMap[$aspek]['scores'][] = (float)$subItem['skor'];
                    }
                    if (!empty($subItem['catatan'])) {
                        $subPointsMap[$aspek]['notes'][] = trim($subItem['catatan']);
                    }
                }
            }

            $avg = count($catScores) > 0 ? round(array_sum($catScores) / count($catScores), 2) : 0;

            // Susun rekap sub-poin
            $aggregatedSubPoints = [];
            $definedSubs = $cat->sub_points_list;

            if (!empty($definedSubs)) {
                foreach ($definedSubs as $aspekName) {
                    $itemData = $subPointsMap[$aspekName] ?? null;
                    $itemScore = ($itemData && count($itemData['scores']) > 0)
                                 ? round(array_sum($itemData['scores']) / count($itemData['scores']), 1)
                                 : $avg;
                    $itemNotes = ($itemData && count($itemData['notes']) > 0)
                                 ? implode('; ', array_unique($itemData['notes']))
                                 : '';

                    $aggregatedSubPoints[] = [
                        'aspek'   => $aspekName,
                        'skor'    => $itemScore,
                        'catatan' => $itemNotes,
                    ];
                }
            } elseif (!empty($subPointsMap)) {
                foreach ($subPointsMap as $aspekName => $itemData) {
                    $itemScore = count($itemData['scores']) > 0
                                 ? round(array_sum($itemData['scores']) / count($itemData['scores']), 1)
                                 : $avg;
                    $itemNotes = count($itemData['notes']) > 0
                                 ? implode('; ', array_unique($itemData['notes']))
                                 : '';

                    $aggregatedSubPoints[] = [
                        'aspek'   => $aspekName,
                        'skor'    => $itemScore,
                        'catatan' => $itemNotes,
                    ];
                }
            }

            $scoresSummary[$cat->id] = [
                'kategori_id' => $cat->id,
                'nama'        => $cat->nama,
                'nilai'       => $avg,
                'sub_points'  => $aggregatedSubPoints,
            ];

            $slug = Str::slug($cat->nama);
            if ($slug === 'teknik') $legacy['nilai_teknik_final'] = $avg;
            if ($slug === 'taktik') $legacy['nilai_taktik_final'] = $avg;
            if ($slug === 'fisik')  $legacy['nilai_fisik_final']  = $avg;
            if ($slug === 'mental') $legacy['nilai_mental_final'] = $avg;
        }

        // Gabungkan semua catatan asesor menjadi satu paragraf
        $catatanGabungan = $scores->pluck('catatan_asesor')->filter()->implode(' | ');

        // 3. Simpan ke Tabel Raport Final Utama
        Raport::updateOrCreate(
            [
                'athlete_id' => $athlete->id,
                'periode'    => $periode,
            ],
            array_merge($legacy, [
                'scores_summary' => $scoresSummary,
                'catatan_pelatih' => $catatanGabungan,
                'status_publish' => 'Published' // Langsung bisa dilihat wali murid
            ])
        );

        return back()->with('success', "Raport Final berhasil di-generate dari " . $scores->count() . " asesor!");
    }
}