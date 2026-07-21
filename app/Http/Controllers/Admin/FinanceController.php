<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Finance;
use App\Models\Athlete;
use Illuminate\Http\Request;
use App\Exports\FinancesExport;
use Maatwebsite\Excel\Facades\Excel;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Finance::query()->with('athlete');
        
        if ($request->has('bulan') && $request->bulan != '') {
            $query->where('bulan_tagihan', $request->bulan);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kategori', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('athlete', function($q2) use ($search) {
                      $q2->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('jenis') && $request->jenis != '') {
            $query->where('jenis', $request->jenis);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status_bayar', $request->status);
        }

        $perPage = $request->input('per_page', 15);
        $finances = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate($perPage)->appends($request->all());
        
        // Ambil daftar bulan untuk dropdown filter
        $listBulan = Finance::query()->whereNotNull('bulan_tagihan', 'and')->select('bulan_tagihan')->distinct()->pluck('bulan_tagihan');

        $totalPemasukanLunas = Finance::query()->where('jenis', 'pemasukan')->where('status_bayar', 'lunas')->sum('nominal');
        $totalPemasukanBelumLunas = Finance::query()->where('jenis', 'pemasukan')->where('status_bayar', 'belum_lunas')->sum('nominal');
        $totalPengeluaran = Finance::query()->where('jenis', 'pengeluaran')->sum('nominal');
        $saldoSekarang = $totalPemasukanLunas - $totalPengeluaran;

        return view('admin.finances.index', compact('finances', 'totalPemasukanLunas', 'totalPemasukanBelumLunas', 'totalPengeluaran', 'saldoSekarang', 'listBulan'));
    }

    public function create(Request $request)
    {
        // Ambil daftar atlet untuk dropdown pilihan siswa yang membayar kas
        $athletes = Athlete::query()->orderBy('nama', 'asc')->get();
        $selectedAthleteId = $request->get('athlete_id');
        return view('admin.finances.create', compact('athletes', 'selectedAthleteId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal'               => 'required|date',
            'jenis'                 => 'required|in:pemasukan,pengeluaran',
            'kategori'              => 'required|string|max:100',
            'nominal'               => 'required|numeric|min:0',
            'status_bayar'          => 'required|in:lunas,belum_lunas',
            'tanggal_jatuh_tempo'   => 'nullable|date',
            'keterangan'            => 'nullable|string',
            'athlete_id'            => 'nullable|exists:athletes,id',
            'bulan_tagihan'         => 'nullable|string|max:50',
        ]);

        // Jika kategori Iuran Kas Siswa, buat keterangan otomatis jika kosong
        if ($validated['athlete_id'] && empty($validated['keterangan'])) {
            $siswa = Athlete::query()->find($validated['athlete_id']);
            $validated['keterangan'] = "Pembayaran " . $validated['kategori'] . " Bulan " . ($validated['bulan_tagihan'] ?? '-') . " a.n. " . ($siswa->nama ?? 'Siswa');
        }

        $validated['saldo_akhir'] = 0; // Akan dihitung ulang

        Finance::create($validated);
        $this->recalculateSaldo();

        return redirect()->route('admin.finances.index')->with('success', 'Transaksi berhasil dicatat & Saldo Kas diperbarui!');
    }

    public function edit(Finance $finance)
    {
        $athletes = Athlete::query()->orderBy('nama', 'asc')->get();
        return view('admin.finances.edit', compact('finance', 'athletes'));
    }

    public function update(Request $request, Finance $finance)
    {
        $validated = $request->validate([
            'tanggal'               => 'required|date',
            'jenis'                 => 'required|in:pemasukan,pengeluaran',
            'kategori'              => 'required|string|max:100',
            'nominal'               => 'required|numeric|min:0',
            'status_bayar'          => 'required|in:lunas,belum_lunas',
            'tanggal_jatuh_tempo'   => 'nullable|date',
            'keterangan'            => 'nullable|string',
            'athlete_id'            => 'nullable|exists:athletes,id',
            'bulan_tagihan'         => 'nullable|string|max:50',
        ]);

        if ($validated['athlete_id'] && empty($validated['keterangan'])) {
            $siswa = Athlete::query()->find($validated['athlete_id']);
            $validated['keterangan'] = "Pembayaran " . $validated['kategori'] . " Bulan " . ($validated['bulan_tagihan'] ?? '-') . " a.n. " . ($siswa->nama ?? 'Siswa');
        }

        $finance->update($validated);
        $this->recalculateSaldo();

        return redirect()->route('admin.finances.index')->with('success', 'Transaksi berhasil diperbarui & Saldo Kas diperbarui!');
    }

    public function destroy(Finance $finance)
    {
        Finance::destroy($finance->id);
        $this->recalculateSaldo();
        return redirect()->back()->with('success', 'Riwayat transaksi berhasil dihapus!');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:finances,id',
        ]);

        Finance::destroy($validated['ids']);
        $this->recalculateSaldo();

        return redirect()->back()->with('success', count($validated['ids']) . ' riwayat transaksi berhasil dihapus!');
    }

    public function generateBulkTagihan(Request $request)
    {
        $validated = $request->validate([
            'nominal' => 'required|numeric|min:1',
            'bulan_tagihan' => 'required|string|max:50',
            'tanggal_jatuh_tempo' => 'nullable|date',
        ]);

        $athletes = Athlete::all();
        $count = 0;

        foreach ($athletes as $athlete) {
            // Cek apakah tagihan untuk bulan ini sudah ada agar tidak ganda
            $exists = Finance::query()->where('athlete_id', $athlete->id)
                ->where('kategori', 'Iuran Uang Kas Bulanan Siswa')
                ->where('bulan_tagihan', $validated['bulan_tagihan'])
                ->exists();

            if (!$exists) {
                Finance::create([
                    'tanggal' => now(),
                    'jenis' => 'pemasukan',
                    'kategori' => 'Iuran Uang Kas Bulanan Siswa',
                    'nominal' => $validated['nominal'],
                    'status_bayar' => 'belum_lunas',
                    'tanggal_jatuh_tempo' => $validated['tanggal_jatuh_tempo'],
                    'keterangan' => "Tagihan Kas Bulan " . $validated['bulan_tagihan'] . " a.n. " . $athlete->nama,
                    'athlete_id' => $athlete->id,
                    'bulan_tagihan' => $validated['bulan_tagihan'],
                    'saldo_akhir' => 0,
                ]);
                $count++;
            }
        }

        if ($count > 0) {
            $this->recalculateSaldo();
            return redirect()->route('admin.finances.index')->with('success', "Berhasil membuat tagihan kas massal untuk {$count} siswa!");
        }

        return redirect()->route('admin.finances.index')->with('info', 'Semua siswa sudah memiliki tagihan untuk bulan tersebut (tidak ada tagihan baru yang ditambahkan).');
    }

    private function recalculateSaldo()
    {
        $finances = Finance::orderBy('tanggal', 'asc')->orderBy('id', 'asc')->get();
        $saldo = 0;

        foreach ($finances as $f) {
            if ($f->status_bayar === 'lunas') {
                if ($f->jenis === 'pemasukan') {
                    $saldo += $f->nominal;
                } else {
                    $saldo -= $f->nominal;
                }
            }
            // Update saldo_akhir without firing events to avoid infinite loops if any
            Finance::query()->where('id', $f->id)->update(['saldo_akhir' => $saldo]);
        }
    }

    public function exportExcel(Request $request)
    {
        $bulan = $request->bulan;
        $namaFile = 'Laporan_Keuangan_Superseed_Academy_' . ($bulan ? str_replace(' ', '_', $bulan) : 'Semua') . '_' . date('Y-m-d_H-i') . '.xlsx';
        return Excel::download(new FinancesExport($bulan), $namaFile);
    }

    public function print(Request $request)
    {
        $query = Finance::query()->with('athlete');
        
        if ($request->has('bulan') && $request->bulan != '') {
            $query->where('bulan_tagihan', $request->bulan);
            $totalPemasukanLunas = Finance::query()->where('jenis', 'pemasukan')->where('status_bayar', 'lunas')->where('bulan_tagihan', $request->bulan)->sum('nominal');
            $totalPemasukanBelumLunas = Finance::query()->where('jenis', 'pemasukan')->where('status_bayar', 'belum_lunas')->where('bulan_tagihan', $request->bulan)->sum('nominal');
            $totalPengeluaran = Finance::query()->where('jenis', 'pengeluaran')->where('bulan_tagihan', $request->bulan)->sum('nominal');
        } else {
            $totalPemasukanLunas = Finance::query()->where('jenis', 'pemasukan')->where('status_bayar', 'lunas')->sum('nominal');
            $totalPemasukanBelumLunas = Finance::query()->where('jenis', 'pemasukan')->where('status_bayar', 'belum_lunas')->sum('nominal');
            $totalPengeluaran = Finance::query()->where('jenis', 'pengeluaran')->sum('nominal');
        }

        $finances = $query->orderBy('tanggal', 'asc')->orderBy('id', 'asc')->get();
        $saldoSekarang = $totalPemasukanLunas - $totalPengeluaran;

        return view('admin.finances.print', compact('finances', 'totalPemasukanLunas', 'totalPemasukanBelumLunas', 'totalPengeluaran', 'saldoSekarang'));
    }
}