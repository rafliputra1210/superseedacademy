<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Finance;
use App\Models\Athlete;
use Illuminate\Http\Request;
use App\Exports\FinancesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $selectedBulanInput = $request->input('bulan');

        // Otomatis deteksi bulan berjalan / bulan terpilih
        $summary = $this->calculateSummary($selectedBulanInput);
        $filterBulanAktif = $summary['filterBulanAktif'];

        $query = Finance::query()->with('athlete');

        if ($filterBulanAktif) {
            $indonesianMonths = [
                'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
                'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
                'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
            ];
            $parts = explode(' ', trim($filterBulanAktif));
            if (count($parts) >= 2 && isset($indonesianMonths[$parts[0]])) {
                $m = sprintf('%02d', $indonesianMonths[$parts[0]]);
                $y = $parts[1];
                $startDate = "{$y}-{$m}-01";
                $endDate = \Carbon\Carbon::parse($startDate)->endOfMonth()->toDateString();

                $query->where(function($q) use ($filterBulanAktif, $startDate, $endDate) {
                    $q->where('bulan_tagihan', $filterBulanAktif)
                      ->orWhereBetween('tanggal', [$startDate, $endDate]);
                });
            } else {
                $query->where('bulan_tagihan', $filterBulanAktif);
            }
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

        if ($request->has('metode') && $request->metode != '') {
            $query->where('metode_pembayaran', $request->metode);
        }

        $perPage = $request->input('per_page', 15);
        $finances = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate($perPage)->appends($request->all());

        // Ambil daftar bulan untuk dropdown filter
        $listBulan = Finance::query()->whereNotNull('bulan_tagihan')->select('bulan_tagihan')->distinct()->pluck('bulan_tagihan');

        $saldoAwal                = $summary['saldoAwal'];
        $totalPemasukanLunas      = $summary['totalPemasukanLunas'];
        $totalPemasukanBelumLunas = $summary['totalPemasukanBelumLunas'];
        $totalPengeluaran         = $summary['totalPengeluaran'];
        $saldoSekarang            = $summary['saldoSekarang'];

        return view('admin.finances.index', compact(
            'finances',
            'saldoAwal',
            'totalPemasukanLunas',
            'totalPemasukanBelumLunas',
            'totalPengeluaran',
            'saldoSekarang',
            'listBulan',
            'filterBulanAktif'
        ));
    }

    public function create(Request $request)
    {
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
            'metode_pembayaran'     => 'nullable|in:cash,transfer',
            'nama_pengirim_transfer'=> 'nullable|string|max:100',
            'tanggal_jatuh_tempo'   => 'nullable|date',
            'keterangan'            => 'nullable|string',
            'athlete_id'            => 'nullable|exists:athletes,id',
            'bulan_tagihan'         => 'nullable|string|max:50',
        ]);

        if ($validated['jenis'] === 'pengeluaran') {
            $validated['athlete_id'] = null;
            $validated['bulan_tagihan'] = $this->formatBulanTagihanFromDate($validated['tanggal']);
        } elseif (empty($validated['bulan_tagihan']) && !empty($validated['tanggal'])) {
            $validated['bulan_tagihan'] = $this->formatBulanTagihanFromDate($validated['tanggal']);
        }

        if ($validated['athlete_id'] && empty($validated['keterangan'])) {
            $siswa = Athlete::query()->find($validated['athlete_id']);
            $validated['keterangan'] = "Pembayaran " . $validated['kategori'] . " Bulan " . ($validated['bulan_tagihan'] ?? '-') . " a.n. " . ($siswa->nama ?? 'Siswa');
        }

        $validated['saldo_akhir'] = 0;

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
            'metode_pembayaran'     => 'nullable|in:cash,transfer',
            'nama_pengirim_transfer'=> 'nullable|string|max:100',
            'tanggal_jatuh_tempo'   => 'nullable|date',
            'keterangan'            => 'nullable|string',
            'athlete_id'            => 'nullable|exists:athletes,id',
            'bulan_tagihan'         => 'nullable|string|max:50',
        ]);

        if ($validated['jenis'] === 'pengeluaran') {
            $validated['athlete_id'] = null;
            $validated['bulan_tagihan'] = $this->formatBulanTagihanFromDate($validated['tanggal']);
        } elseif (empty($validated['bulan_tagihan']) && !empty($validated['tanggal'])) {
            $validated['bulan_tagihan'] = $this->formatBulanTagihanFromDate($validated['tanggal']);
        }

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
            'ids'   => 'required|array',
            'ids.*' => 'exists:finances,id',
        ]);

        Log::warning('Hapus massal riwayat transaksi keuangan dijalankan oleh admin', [
            'admin_id'      => Auth::id(),
            'deleted_count' => count($validated['ids']),
            'ids'           => $validated['ids'],
            'ip'            => $request->ip(),
        ]);

        Finance::destroy($validated['ids']);
        $this->recalculateSaldo();

        return redirect()->back()->with('success', count($validated['ids']) . ' riwayat transaksi berhasil dihapus!');
    }

    public function generateBulkTagihan(Request $request)
    {
        $validated = $request->validate([
            'nominal'             => 'required|numeric|min:1',
            'bulan_tagihan'       => 'required|string|max:50',
            'tanggal_jatuh_tempo' => 'nullable|date',
        ]);

        $athletes = Athlete::all();
        $count    = 0;

        foreach ($athletes as $athlete) {
            // ---------------------------------------------------------------
            // FIX #2: Pengecekan duplikasi berlapis.
            // Skip siswa jika sudah ada record apapun (lunas ATAU belum_lunas)
            // untuk bulan tagihan ini — mencegah tagihan ganda.
            // ---------------------------------------------------------------

            // Cek 1: Apakah sudah ada tagihan belum lunas untuk bulan ini?
            $hasPendingTagihan = Finance::query()
                ->where('athlete_id', $athlete->id)
                ->where('kategori', 'Iuran Uang Kas Bulanan Siswa')
                ->where('bulan_tagihan', $validated['bulan_tagihan'])
                ->where('status_bayar', 'belum_lunas')
                ->exists();

            if ($hasPendingTagihan) {
                continue; // Sudah ada tagihan pending → skip
            }

            // Cek 2: Apakah siswa sudah membayar (status lunas) untuk bulan ini?
            $hasLunasPayment = Finance::query()
                ->where('athlete_id', $athlete->id)
                ->where('bulan_tagihan', $validated['bulan_tagihan'])
                ->where('status_bayar', 'lunas')
                ->exists();

            if ($hasLunasPayment) {
                continue; // Sudah bayar → skip, jangan buat tagihan baru
            }

            // Lolos kedua cek → buat tagihan baru
            Finance::create([
                'tanggal'             => now(),
                'jenis'               => 'pemasukan',
                'kategori'            => 'Iuran Uang Kas Bulanan Siswa',
                'nominal'             => $validated['nominal'],
                'status_bayar'        => 'belum_lunas',
                'metode_pembayaran'   => null,
                'tanggal_jatuh_tempo' => $validated['tanggal_jatuh_tempo'],
                'keterangan'          => "Tagihan Kas Bulan " . $validated['bulan_tagihan'] . " a.n. " . $athlete->nama,
                'athlete_id'          => $athlete->id,
                'bulan_tagihan'       => $validated['bulan_tagihan'],
                'saldo_akhir'         => 0,
            ]);
            $count++;
        }

        if ($count > 0) {
            $this->recalculateSaldo();
            return redirect()->route('admin.finances.index')->with('success', "Berhasil membuat tagihan kas massal untuk {$count} siswa!");
        }

        return redirect()->route('admin.finances.index')->with('info', 'Semua siswa sudah memiliki tagihan atau sudah membayar untuk bulan tersebut.');
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
            Finance::query()->where('id', $f->id)->update(['saldo_akhir' => $saldo]);
        }
    }

    private function calculateSummary($requestBulan)
    {
        // Auto-heal data transaksi pengeluaran agar bulan_tagihan selalu sesuai dengan bulan dari tanggal transaksi
        $healed = false;
        Finance::query()
            ->where('jenis', 'pengeluaran')
            ->get()
            ->each(function($f) use (&$healed) {
                if ($f->tanggal) {
                    $correctBulan = $this->formatBulanTagihanFromDate($f->tanggal);
                    if ($f->bulan_tagihan !== $correctBulan) {
                        $f->update(['bulan_tagihan' => $correctBulan]);
                        $healed = true;
                    }
                }
            });

        // Auto-heal data transaksi lama yang bulan_tagihan-nya masih NULL / kosong
        Finance::query()
            ->where(function($q) {
                $q->whereNull('bulan_tagihan')->orWhere('bulan_tagihan', '');
            })
            ->get()
            ->each(function($f) use (&$healed) {
                if ($f->tanggal) {
                    $f->update(['bulan_tagihan' => $this->formatBulanTagihanFromDate($f->tanggal)]);
                    $healed = true;
                }
            });

        if ($healed) {
            $this->recalculateSaldo();
        }

        // 1. Jika tidak ada filter bulan (request 'bulan' kosong), otomatis gunakan bulan berjalan / bulan terbaru
        if (is_null($requestBulan) || $requestBulan === '') {
            $currentMonthStr = $this->getCurrentMonthString();
            $existsCurrent = Finance::query()->where('bulan_tagihan', $currentMonthStr)->exists();
            if ($existsCurrent) {
                $requestBulan = $currentMonthStr;
            } else {
                $latestBulan = Finance::query()->whereNotNull('bulan_tagihan')->orderBy('id', 'desc')->value('bulan_tagihan');
                if ($latestBulan) {
                    $requestBulan = $latestBulan;
                }
            }
        }

        // 2. Jika user memilih 'semua' secara eksplisit
        if ($requestBulan === 'semua') {
            $totalPemasukanLunas      = Finance::query()->where('jenis', 'pemasukan')->where('status_bayar', 'lunas')->sum('nominal');
            $totalPemasukanBelumLunas = Finance::query()->where('jenis', 'pemasukan')->where('status_bayar', 'belum_lunas')->sum('nominal');
            $totalPengeluaran         = Finance::query()->where('jenis', 'pengeluaran')->sum('nominal');
            $saldoSekarang            = $totalPemasukanLunas - $totalPengeluaran;

            return [
                'filterBulanAktif'         => null,
                'saldoAwal'                => 0,
                'totalPemasukanLunas'      => $totalPemasukanLunas,
                'totalPemasukanBelumLunas' => $totalPemasukanBelumLunas,
                'totalPengeluaran'         => $totalPengeluaran,
                'saldoSekarang'            => $saldoSekarang,
            ];
        }

        $targetSortKey = $this->getMonthSortKey($requestBulan);

        // Ambil SEMUA transaksi untuk menghitung Saldo Awal, Pemasukan, dan Pengeluaran
        $allFinances = Finance::query()->get();

        $saldoAwal = 0;
        $totalPemasukanLunas = 0;
        $totalPemasukanBelumLunas = 0;
        $totalPengeluaran = 0;

        foreach ($allFinances as $f) {
            $fSortKey = $this->getMonthSortKey($f->bulan_tagihan, $f->tanggal);

            if ($targetSortKey > 0 && $fSortKey < $targetSortKey) {
                // Transaksi BULAN-BULAN SEBELUMNYA → Masuk ke Saldo Awal
                if ($f->status_bayar === 'lunas') {
                    if ($f->jenis === 'pemasukan') {
                        $saldoAwal += $f->nominal;
                    } else {
                        $saldoAwal -= $f->nominal;
                    }
                }
            } elseif ($fSortKey === $targetSortKey) {
                // Transaksi BULAN BERJALAN
                if ($f->jenis === 'pemasukan') {
                    if ($f->status_bayar === 'lunas') {
                        $totalPemasukanLunas += $f->nominal;
                    } else {
                        $totalPemasukanBelumLunas += $f->nominal;
                    }
                } else {
                    $totalPengeluaran += $f->nominal;
                }
            }
        }

        $saldoSekarang = $saldoAwal + $totalPemasukanLunas - $totalPengeluaran;

        return [
            'filterBulanAktif'         => $requestBulan,
            'saldoAwal'                => $saldoAwal,
            'totalPemasukanLunas'      => $totalPemasukanLunas,
            'totalPemasukanBelumLunas' => $totalPemasukanBelumLunas,
            'totalPengeluaran'         => $totalPengeluaran,
            'saldoSekarang'            => $saldoSekarang,
        ];
    }

    private function getMonthSortKey($bulanStr, $tanggalFallback = null)
    {
        $indonesianMonths = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
        ];

        if ($bulanStr) {
            $parts = explode(' ', trim($bulanStr));
            if (count($parts) >= 2 && isset($indonesianMonths[$parts[0]])) {
                $m = (int)$indonesianMonths[$parts[0]];
                $y = (int)$parts[1];
                return ($y * 100) + $m;
            }
        }

        if ($tanggalFallback) {
            $dt = \Carbon\Carbon::parse($tanggalFallback);
            return ($dt->year * 100) + $dt->month;
        }

        return 0;
    }

    private function getCurrentMonthString()
    {
        $indonesianMonthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $m = (int)date('n');
        $y = date('Y');
        return ($indonesianMonthNames[$m] ?? date('F')) . ' ' . $y;
    }

    private function formatBulanTagihanFromDate($tanggal)
    {
        $dt = \Carbon\Carbon::parse($tanggal);
        $indonesianMonthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $m = (int)$dt->month;
        $y = $dt->year;
        return ($indonesianMonthNames[$m] ?? $dt->format('F')) . ' ' . $y;
    }

    public function exportExcel(Request $request)
    {
        $bulan    = $request->bulan;
        $namaFile = 'Laporan_Keuangan_Superseed_Academy_' . ($bulan ? str_replace(' ', '_', $bulan) : 'Semua') . '_' . date('Y-m-d_H-i') . '.xlsx';
        return Excel::download(new FinancesExport($bulan), $namaFile);
    }

    public function print(Request $request)
    {
        $query = Finance::query()->with('athlete');

        if ($request->has('bulan') && $request->bulan != '') {
            $query->where('bulan_tagihan', $request->bulan);
        }

        $filterBulanAktif = ($request->has('bulan') && $request->bulan != '') ? $request->bulan : null;
        $summary = $this->calculateSummary($filterBulanAktif);

        $saldoAwal                = $summary['saldoAwal'];
        $totalPemasukanLunas      = $summary['totalPemasukanLunas'];
        $totalPemasukanBelumLunas = $summary['totalPemasukanBelumLunas'];
        $totalPengeluaran         = $summary['totalPengeluaran'];
        $saldoSekarang            = $summary['saldoSekarang'];

        $finances = $query->orderBy('tanggal', 'asc')->orderBy('id', 'asc')->get();

        return view('admin.finances.print', compact(
            'finances',
            'saldoAwal',
            'totalPemasukanLunas',
            'totalPemasukanBelumLunas',
            'totalPengeluaran',
            'saldoSekarang'
        ));
    }
}