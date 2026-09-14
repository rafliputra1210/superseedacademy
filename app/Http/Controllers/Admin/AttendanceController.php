<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', $request->input('dari_tanggal'));
        $endDate   = $request->input('end_date', $request->input('sampai_tanggal'));
        $tanggal   = $request->input('tanggal');

        $query = Attendance::with('athlete')->latest();

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->whereDate('tanggal', '>=', $startDate);
        } elseif ($endDate) {
            $query->whereDate('tanggal', '<=', $endDate);
        } elseif ($tanggal) {
            $query->whereDate('tanggal', $tanggal);
        }

        if ($request->filled('status')) {
            if ($request->status === 'alpa' || $request->status === 'alpha') {
                $query->whereIn('status', ['alpa', 'alpha']);
            } else {
                $query->where('status', $request->status);
            }
        }

        $perPage = $request->input('per_page', 10);
        if ($perPage === 'all') {
            $perPage = 10000;
        } else {
            $perPage = (int) $perPage;
            if ($perPage <= 0) {
                $perPage = 10;
            }
        }

        $attendances = $query->paginate($perPage)->withQueryString();
        $athletes = Athlete::orderBy('nama', 'asc')->get();

        $rekapQuery = Attendance::query();
        if ($startDate && $endDate) {
            $rekapQuery->whereBetween('tanggal', [$startDate, $endDate]);
        } elseif ($startDate) {
            $rekapQuery->whereDate('tanggal', '>=', $startDate);
        } elseif ($endDate) {
            $rekapQuery->whereDate('tanggal', '<=', $endDate);
        } elseif ($tanggal) {
            $rekapQuery->whereDate('tanggal', $tanggal);
        }

        $rekap = [
            'hadir' => (clone $rekapQuery)->where('status', 'hadir')->count(),
            'izin'  => (clone $rekapQuery)->where('status', 'izin')->count(),
            'sakit' => (clone $rekapQuery)->where('status', 'sakit')->count(),
            'alpa'  => (clone $rekapQuery)->whereIn('status', ['alpa', 'alpha'])->count(),
            'total' => (clone $rekapQuery)->count(),
        ];

        return view('admin.attendances.index', compact('attendances', 'athletes', 'rekap'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,alpa',
            'foto_bukti' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('foto_bukti')) {
            $file = $request->file('foto_bukti');
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower($file->guessExtension() ?: 'jpg');
            if (!in_array($ext, $allowedExtensions)) {
                $ext = 'jpg';
            }
            $fileName = time() . '_' . Str::random(20) . '.' . $ext;
            if (!file_exists(public_path('uploads/foto_bukti'))) {
                mkdir(public_path('uploads/foto_bukti'), 0755, true);
            }
            $file->move(public_path('uploads/foto_bukti'), $fileName);
            $validated['foto_bukti'] = 'uploads/foto_bukti/' . $fileName;
        }

        // Generate a unique barcode code (e.g. ATT + date + random string)
        $validated['kode_barcode'] = 'ATT' . date('YmdHis') . strtoupper(Str::random(4));

        Attendance::create($validated);

        return redirect()->route('admin.attendances.index')->with('success', 'Absensi berhasil dicatat!');
    }

    public function show(Attendance $attendance)
    {
        return view('admin.attendances.barcode', compact('attendance'));
    }

    public function edit(Attendance $attendance)
    {
        $athletes = Athlete::orderBy('nama', 'asc')->get();
        return view('admin.attendances.edit', compact('attendance', 'athletes'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,alpa',
            'foto_bukti' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('foto_bukti')) {
            if ($attendance->foto_bukti && file_exists(public_path($attendance->foto_bukti))) {
                @unlink(public_path($attendance->foto_bukti));
            }

            $file = $request->file('foto_bukti');
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower($file->guessExtension() ?: 'jpg');
            if (!in_array($ext, $allowedExtensions)) {
                $ext = 'jpg';
            }
            $fileName = time() . '_' . Str::random(20) . '.' . $ext;
            if (!file_exists(public_path('uploads/foto_bukti'))) {
                mkdir(public_path('uploads/foto_bukti'), 0755, true);
            }
            $file->move(public_path('uploads/foto_bukti'), $fileName);
            $validated['foto_bukti'] = 'uploads/foto_bukti/' . $fileName;
        }

        $attendance->update($validated);

        return redirect()->route('admin.attendances.index')->with('success', 'Data absensi berhasil diperbarui!');
    }

    public function destroy(Attendance $attendance)
    {
        if ($attendance->foto_bukti && file_exists(public_path($attendance->foto_bukti))) {
            unlink(public_path($attendance->foto_bukti));
        }

        Attendance::destroy($attendance->id);

        return redirect()->route('admin.attendances.index')->with('success', 'Absensi berhasil dihapus!');
    }
    public function scan()
    {
        // Mengambil riwayat absensi hari ini untuk ditampilkan di live log di bawah kamera
        $todayAttendances = Attendance::with('athlete')
            ->whereDate('tanggal', Carbon::today())
            ->latest()
            ->get();

        return view('admin.attendances.scan', compact('todayAttendances'));
    }

    // 2. Proses AJAX dari Kamera Scanner
    public function storeScan(Request $request)
    {
        $request->validate([
            'kode_barcode' => 'required|string',
        ]);

        // 1. Cari data atlet berdasarkan kode barcode
        $athlete = Athlete::query()->where('kode_barcode', $request->kode_barcode)->first();

        if (!$athlete) {
            return response()->json([
                'status' => 'error',
                'message' => '❌ Barcode "' . $request->kode_barcode . '" tidak dikenali / tidak terdaftar!'
            ], 404);
        }

        // 2. Cek apakah atlet tersebut sudah absen hari ini (cegah dobel absen)
        $sudahAbsen = Attendance::query()->where('athlete_id', $athlete->id)
            ->whereDate('tanggal', Carbon::today())
            ->exists();

        if ($sudahAbsen) {
            return response()->json([
                'status' => 'warning',
                'message' => '⚠️ ' . $athlete->nama . ' sudah melakukan absensi hari ini!',
                'athlete' => $athlete
            ], 200);
        }

        // 3. Simpan data absensi dengan status Hadir
        $attendance = Attendance::create([
            'athlete_id'   => $athlete->id,
            'tanggal'      => Carbon::today()->format('Y-m-d'),
            'waktu_absen'  => Carbon::now()->format('H:i:s'),
            'status'       => 'hadir',
            'kode_barcode' => $athlete->kode_barcode
        ]);

        return response()->json([
            'status' => 'success',
            'message' => '✅ Berhasil! ' . $athlete->nama . ' hadir latihan.',
            'athlete' => $athlete,
            'time'    => Carbon::now()->format('H:i:s')
        ], 200);
    }
}