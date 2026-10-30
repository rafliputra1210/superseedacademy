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

        // 1. Ambil semua nilai dari para asesor untuk anak ini di bulan ini beserta observasi
        $scores = AssessorScore::with('observation')
                               ->where('athlete_id', $athlete->id)
                               ->where('periode_bulan', $periode)
                               ->get();

        // Validasi: Pastikan minimal ada penilaian masuk
        if ($scores->count() < 1) {
            return back()->with('error', 'Belum ada asesor yang memberikan nilai!');
        }

        // Kumpulkan observasi kualitatif dari semua asesor
        $observasiGabungan = [
            'kekuatan_utama'   => [],
            'area_peningkatan' => [],
            'catatan_perilaku' => [],
            'fokus_latihan'    => [],
        ];

        foreach ($scores as $s) {
            if ($s->observation) {
                if (!empty($s->observation->kekuatan_utama))   $observasiGabungan['kekuatan_utama'][]   = trim($s->observation->kekuatan_utama);
                if (!empty($s->observation->area_ditingkatkan)) $observasiGabungan['area_peningkatan'][] = trim($s->observation->area_ditingkatkan);
                if (!empty($s->observation->catatan_perilaku))  $observasiGabungan['catatan_perilaku'][] = trim($s->observation->catatan_perilaku);
                if (!empty($s->observation->fokus_latihan))     $observasiGabungan['fokus_latihan'][]    = trim($s->observation->fokus_latihan);
            }
        }

        // 2. Hitung Rata-rata Otomatis per Kategori Aktif sesuai Posisi Atlet
        $posisiBermain = $athlete->posisi_bermain ?? '';
        $isKiper = stripos($posisiBermain, 'Kiper') !== false || stripos($posisiBermain, 'GK') !== false;
        $targetPosisi = $isKiper ? 'Kiper' : 'Player';

        $categories = AssessmentCategory::where('is_active', true)
                                        ->where('target_posisi', $targetPosisi)
                                        ->orderBy('urutan', 'asc')
                                        ->get();
        $scoresSummary = [];
        $legacy = [
            'nilai_teknik_final' => 0,
            'nilai_taktik_final' => 0,
            'nilai_fisik_final'  => 0,
            'nilai_mental_final' => 0,
        ];

        $grandTotalSkor = 0;
        $totalPertanyaanSemuaKategori = 0;


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
                        
                        // TAMBAHAN UNTUK REKAP: Menjumlahkan setiap nilai mentah
                        $grandTotalSkor += (float)$subItem['skor'];
                        $totalPertanyaanSemuaKategori++;
                    }
                    if (!empty($subItem['catatan'])) {
                        $subPointsMap[$aspek]['notes'][] = trim($subItem['catatan']);
                    }
                }
            }

            $avg = count($catScores) > 0 ? round(array_sum($catScores) / count($catScores), 2) : 0;

            // ... (Kode penyusunan $aggregatedSubPoints dari aslinya tetap sama) ...
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
                'kategori_id'   => $cat->id,
                'target_posisi' => $cat->target_posisi,
                'nama'          => $cat->nama,
                'nilai'         => $avg,
                'sub_points'    => $aggregatedSubPoints,
            ];

            $slug = Str::slug($cat->nama);
            if ($slug === 'teknik') $legacy['nilai_teknik_final'] = $avg;
            if ($slug === 'taktik') $legacy['nilai_taktik_final'] = $avg;
            if ($slug === 'fisik')  $legacy['nilai_fisik_final']  = $avg;
            if ($slug === 'mental') $legacy['nilai_mental_final'] = $avg;
        }

        // Gabungkan semua catatan asesor menjadi satu paragraf
        $catatanGabungan = $scores->pluck('catatan_asesor')->filter()->implode(' | ');
        
        // Gabungkan observasi kualitatif
        $observasiFinal = [
            'kekuatan_utama' => implode(' | ', array_unique($observasiGabungan['kekuatan_utama'])),
            'area_peningkatan' => implode(' | ', array_unique($observasiGabungan['area_peningkatan'])),
            'catatan_perilaku' => implode(' | ', array_unique($observasiGabungan['catatan_perilaku'])),
            'fokus_latihan' => implode(' | ', array_unique($observasiGabungan['fokus_latihan'])),
        ];

        // KALKULASI FINAL UNTUK CETAKAN (Skala 100 dan Skala 5)
        if ($totalPertanyaanSemuaKategori > 0) {
            $grandRataRata = round($grandTotalSkor / $totalPertanyaanSemuaKategori, 2);
            $skorSkala100  = round(($grandTotalSkor / ($totalPertanyaanSemuaKategori * 5)) * 100);
        } else {
            $catVals = array_filter(array_column($scoresSummary, 'nilai'));
            $grandRataRata = count($catVals) > 0 ? round(array_sum($catVals) / count($catVals), 2) : 0;
            $skorSkala100  = round(($grandRataRata / 5) * 100);
        }

        // 3. Simpan ke Tabel Raport Final Utama
        Raport::updateOrCreate(
            [
                'athlete_id' => $athlete->id,
                'periode'    => $periode,
            ],
            array_merge($legacy, [
                'scores_summary'       => $scoresSummary,
                'catatan_pelatih'      => $catatanGabungan,
                'rekap_skor_100'       => $skorSkala100, 
                'rekap_rata_rata'      => $grandRataRata,
                'observasi_kualitatif' => $observasiFinal,
                'status_publish'       => 'Published',
            ])
        );

        return back()->with('success', "Raport Final berhasil di-generate dari " . $scores->count() . " asesor!");
    }
    public function print($id)
    {
        // Ambil data raport tunggal berdasarkan ID, beserta data atletnya
        $raport = Raport::with('athlete')->findOrFail($id);
        
        // Pastikan kalkulasi rekap dan observasi terisi (auto-fix jika sebelumnya 0 / belum sync)
        $this->ensureRaportRekapData($raport);

        // Eager load assessment categories untuk mencegah query berulang di blade
        $activeCategories = AssessmentCategory::all()->keyBy('id');

        // Buka halaman khusus cetak PDF
        return view('admin.reports.print', compact('raport', 'activeCategories'));
    }

    /**
     * Halaman Rekapitulasi Penilaian Asesor & Raport Bawah
     */
    public function rekapAsesor(Request $request)
    {
        Carbon::setLocale('id');

        $activePeriod = AssessmentPeriod::getActivePeriod();
        $periodeAktifName = AssessmentPeriod::getActivePeriodName();
        $periodeSekarang = $request->get('periode', $periodeAktifName);

        $athletes = Athlete::orderBy('kelompok_umur', 'asc')->orderBy('nama', 'asc')->get();
        $assessors = User::where('role', 'asesor')->get();
        $allScores = AssessorScore::where('periode_bulan', $periodeSekarang)->get();
        
        // Ambil semua raport final pada periode ini
        $raports = Raport::with('athlete')
            ->where('periode', $periodeSekarang)
            ->get();

        // Pastikan data rekap terhitung & observasi terisi
        foreach ($raports as $rap) {
            $this->ensureRaportRekapData($rap);
        }

        return view('admin.reports.rekap', compact(
            'athletes', 'assessors', 'allScores', 'periodeSekarang', 'raports'
        ));
    }
    public function edit($id)
    {
        $raport = \App\Models\Raport::with('athlete')->findOrFail($id);
        
        // Decode observasi kualitatif jika tersimpan sebagai JSON
        $observasi = is_string($raport->observasi_kualitatif) 
            ? json_decode($raport->observasi_kualitatif, true) 
            : ($raport->observasi_kualitatif ?? []);

        return view('admin.reports.edit', compact('raport', 'observasi'));
    }

    // Menyimpan perubahan raport
    public function update(Request $request, $id)
    {
        $raport = \App\Models\Raport::findOrFail($id);

        // Validasi input
        $request->validate([
            'catatan_pelatih' => 'nullable|string',
            'kekuatan_utama' => 'nullable|string',
            'area_peningkatan' => 'nullable|string',
            'catatan_perilaku' => 'nullable|string',
            'fokus_latihan' => 'nullable|string',
        ]);

        // Kemas kembali observasi kualitatif menjadi format JSON/Array
        $observasi = [
            'kekuatan_utama' => $request->kekuatan_utama,
            'area_peningkatan' => $request->area_peningkatan,
            'catatan_perilaku' => $request->catatan_perilaku,
            'fokus_latihan' => $request->fokus_latihan,
        ];

        // Update database
        $raport->update([
            'catatan_pelatih' => $request->catatan_pelatih,
            'observasi_kualitatif' => json_encode($observasi),
            // Jika ada field nilai angka yang mau diedit, tambahkan di sini
        ]);

        return redirect()->route('admin.reports.index')->with('success', 'Data Raport berhasil diperbarui!');
    }
    /**
     * Memastikan data rekap_skor_100, rekap_rata_rata, dan observasi_kualitatif terisi
     */
    private function ensureRaportRekapData(Raport $raport)
    {
        $updated = false;

        $athlete = $raport->athlete;
        if ($athlete) {
            $posisi = $athlete->posisi_bermain ?? '';
            $isKiper = stripos($posisi, 'Kiper') !== false || stripos($posisi, 'GK') !== false;
            $allCategories = AssessmentCategory::all()->keyBy('id');

            // 0. Bersihkan kategori yang tidak sesuai posisi pemain (contoh: kiper pada outfield player)
            if (!empty($raport->scores_summary) && is_array($raport->scores_summary)) {
                $cleanSummary = [];
                $hasWrongCategory = false;

                foreach ($raport->scores_summary as $kId => $cat) {
                    $catModel = $allCategories[$cat['kategori_id'] ?? $kId] ?? null;
                    $cTarget = $cat['target_posisi'] ?? ($catModel->target_posisi ?? null);

                    $namaLower = strtolower($cat['nama'] ?? '');
                    $isCatKiper = str_contains($namaLower, 'kiper') || str_contains($namaLower, 'goalkeeper');

                    if ($cTarget) {
                        if ($isKiper && $cTarget !== 'Kiper') { $hasWrongCategory = true; continue; }
                        if (!$isKiper && $cTarget === 'Kiper') { $hasWrongCategory = true; continue; }
                    } else {
                        if ($isKiper && !$isCatKiper) { $hasWrongCategory = true; continue; }
                        if (!$isKiper && $isCatKiper) { $hasWrongCategory = true; continue; }
                    }

                    $cleanSummary[$kId] = $cat;
                }

                if ($hasWrongCategory) {
                    $raport->scores_summary = $cleanSummary;
                    $updated = true;
                    $raport->rekap_skor_100 = 0;
                    $raport->rekap_rata_rata = 0;
                }
            }
        }

        // 1. Cek Rekap Nilai: jika masih 0 atau null, kalkulasikan dari scores_summary
        if (empty($raport->rekap_skor_100) || (float)$raport->rekap_skor_100 <= 0 || empty($raport->rekap_rata_rata) || (float)$raport->rekap_rata_rata <= 0) {
            $totalSkor = 0;
            $totalCount = 0;

            if (!empty($raport->scores_summary) && is_array($raport->scores_summary)) {
                foreach ($raport->scores_summary as $cat) {
                    if (!empty($cat['sub_points']) && is_array($cat['sub_points'])) {
                        foreach ($cat['sub_points'] as $sp) {
                            if (isset($sp['skor']) && is_numeric($sp['skor']) && (float)$sp['skor'] > 0) {
                                $totalSkor += (float)$sp['skor'];
                                $totalCount++;
                            }
                        }
                    } elseif (!empty($cat['nilai']) && (float)$cat['nilai'] > 0) {
                        $totalSkor += (float)$cat['nilai'];
                        $totalCount++;
                    }
                }
            }

            if ($totalCount > 0) {
                $raport->rekap_rata_rata = round($totalSkor / $totalCount, 2);
                $raport->rekap_skor_100  = round(($raport->rekap_rata_rata / 5) * 100);
                $updated = true;
            }
        }

        // 2. Cek Observasi Kualitatif
        $observasi = $raport->observasi_kualitatif;
        if (is_string($observasi)) {
            $observasi = json_decode($observasi, true);
        }
        $isEmptyObservasi = empty($observasi) || (
            empty($observasi['kekuatan_utama']) && 
            empty($observasi['area_peningkatan']) && 
            empty($observasi['catatan_perilaku']) && 
            empty($observasi['fokus_latihan'])
        );

        if ($isEmptyObservasi) {
            // Ambil dari AssessorScore jika ada
            $scores = AssessorScore::with('observation')
                        ->where('athlete_id', $raport->athlete_id)
                        ->where('periode_bulan', $raport->periode)
                        ->get();

            $obsGabung = [
                'kekuatan_utama'   => [],
                'area_peningkatan' => [],
                'catatan_perilaku' => [],
                'fokus_latihan'    => [],
            ];
            $catatanAsesor = [];

            foreach ($scores as $s) {
                if ($s->observation) {
                    if (!empty($s->observation->kekuatan_utama))    $obsGabung['kekuatan_utama'][]   = trim($s->observation->kekuatan_utama);
                    if (!empty($s->observation->area_ditingkatkan)) $obsGabung['area_peningkatan'][] = trim($s->observation->area_ditingkatkan);
                    if (!empty($s->observation->catatan_perilaku))  $obsGabung['catatan_perilaku'][] = trim($s->observation->catatan_perilaku);
                    if (!empty($s->observation->fokus_latihan))     $obsGabung['fokus_latihan'][]    = trim($s->observation->fokus_latihan);
                }
                if (!empty($s->catatan_asesor)) {
                    $catatanAsesor[] = trim($s->catatan_asesor);
                }
            }

            $obsFinal = [
                'kekuatan_utama'   => implode(' | ', array_unique($obsGabung['kekuatan_utama'])),
                'area_peningkatan' => implode(' | ', array_unique($obsGabung['area_peningkatan'])),
                'catatan_perilaku' => implode(' | ', array_unique($obsGabung['catatan_perilaku'])),
                'fokus_latihan'    => implode(' | ', array_unique($obsGabung['fokus_latihan'])),
            ];

            if (!empty($obsFinal['kekuatan_utama']) || !empty($obsFinal['area_peningkatan']) || !empty($obsFinal['catatan_perilaku']) || !empty($obsFinal['fokus_latihan'])) {
                $raport->observasi_kualitatif = $obsFinal;
                $updated = true;
            }

            if (empty($raport->catatan_pelatih) && count($catatanAsesor) > 0) {
                $raport->catatan_pelatih = implode(' | ', array_unique($catatanAsesor));
                $updated = true;
            }
        }

        if ($updated) {
            $raport->save();
        }
    }
}