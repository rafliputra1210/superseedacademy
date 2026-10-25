<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Athlete;
use App\Models\User;
use App\Models\AssessorScore;
use App\Models\Raport;
use App\Models\AssessmentCategory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReportController extends Controller
{

    public function index(Request $request)
    {
        Carbon::setLocale('id');
        // Filter bulan, default bulan ini
        $periodeSekarang = $request->get('periode', Carbon::now()->translatedFormat('F Y'));
        
        // Ambil semua atlet
        $athletes = Athlete::orderBy('kelompok_umur', 'asc')->orderBy('nama', 'asc')->get();
        
        // Ambil semua akun yang bertugas sebagai asesor
        $assessors = User::where('role', 'asesor')->get();

        // Ambil kategori penilaian aktif
        $categories = AssessmentCategory::where('is_active', true)->orderBy('urutan', 'asc')->get();

        // Ambil semua nilai mentah dari asesor di bulan ini
        $allScores = AssessorScore::where('periode_bulan', $periodeSekarang)->get();

        // Ambil raport final yang sudah digenerate bulan ini
        $raportFinal = Raport::where('periode', $periodeSekarang)->get();

        return view('admin.reports.index', compact('athletes', 'assessors', 'categories', 'allScores', 'periodeSekarang', 'raportFinal'));
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

    public function toggleArchive(Report $report)
    {
        $report->update([
            'is_archived' => !$report->is_archived
        ]);

        $status = $report->is_archived ? 'diarsipkan' : 'dikembalikan dari arsip';
        return redirect()->back()->with('success', "Raport berhasil {$status}!");
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
            foreach ($scores as $s) {
                $val = $s->getCategoryScore($cat->id, $cat->nama);
                if ($val !== null && is_numeric($val)) {
                    $catScores[] = (float) $val;
                }
            }
            $avg = count($catScores) > 0 ? round(array_sum($catScores) / count($catScores), 2) : 0;
            $scoresSummary[$cat->id] = [
                'kategori_id' => $cat->id,
                'nama'        => $cat->nama,
                'nilai'       => $avg,
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