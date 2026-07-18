<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Athlete;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with('athlete')->latest()->paginate(10);
        return view('admin.reports.index', compact('reports'));
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
}