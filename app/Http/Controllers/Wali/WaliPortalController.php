<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\Attendance;
use App\Models\Report;
use App\Models\Finance;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class WaliPortalController extends Controller
{
    // 1. Dashboard & Biodata Anak
    public function dashboard(Request $request)
    {
        // Ambil daftar anak yang terhubung dengan akun wali ini
        $myAthletes = Auth::user()->athletes;

        // Jika punya lebih dari 1 anak, izinkan filter berdasarkan ID anak yang dipilih
        $selectedId = $request->get('child_id', $myAthletes->first()->id ?? null);
        $athlete = $myAthletes->where('id', $selectedId)->first() ?? $myAthletes->first();

        // Ambil pengumuman terbaru dari akademi
        $announcements = Announcement::query()->where('is_active', true)->latest('created_at')->take(5)->get();

        return view('wali.dashboard', compact('myAthletes', 'athlete', 'announcements'));
    }

    // 2. Laporan Raport & Rekapitulasi Absensi Anak
    public function absensi(Request $request)
    {
        $myAthletes = Auth::user()->athletes;
        $selectedId = $request->get('child_id', $myAthletes->first()->id ?? null);
        $athlete = $myAthletes->where('id', $selectedId)->first() ?? $myAthletes->first();

        $attendances = [];
        $rekapAbsen = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];

        if ($athlete) {
            // Tarik riwayat absensi anak (diurutkan dari yang terbaru)
            $attendances = Attendance::query()->where('athlete_id', $athlete->id)->latest()->paginate(15);
            
            // Hitung rekap statistik absensi
            $rekapAbsen['hadir'] = Attendance::query()->where('athlete_id', $athlete->id)->where('status', 'hadir')->count();
            $rekapAbsen['izin'] = Attendance::query()->where('athlete_id', $athlete->id)->where('status', 'izin')->count();
            $rekapAbsen['sakit'] = Attendance::query()->where('athlete_id', $athlete->id)->where('status', 'sakit')->count();
            $rekapAbsen['alpa'] = Attendance::query()->where('athlete_id', $athlete->id)->where('status', 'alpa')->count();
        }

        return view('wali.absensi', compact('myAthletes', 'athlete', 'attendances', 'rekapAbsen'));
    }

    // 3. Halaman Khusus Raport & Evaluasi Skill Anak
    public function raport(Request $request)
    {
        $myAthletes = Auth::user()->athletes;
        $selectedId = $request->get('child_id', $myAthletes->first()->id ?? null);
        $athlete = $myAthletes->where('id', $selectedId)->first() ?? $myAthletes->first();

        $reports = [];

        if ($athlete) {
            // Tarik data raport evaluasi
            $reports = Report::query()->where('athlete_id', $athlete->id)->latest()->get();
        }

        return view('wali.raport', compact('myAthletes', 'athlete', 'reports'));
    }

    // 3. Transparansi Laporan Keuangan (Uang Kas) & Riwayat Pembayaran Anak
    public function keuangan(Request $request)
    {
        $myAthletes = Auth::user()->athletes;
        $selectedId = $request->get('child_id', $myAthletes->first()->id ?? null);
        $athlete = $myAthletes->where('id', $selectedId)->first() ?? $myAthletes->first();

        $riwayatPembayaran = collect();
        $totalTagihan      = 0;
        $totalTerbayar     = 0;
        $totalBelumBayar   = 0;

        if ($athlete) {
            // Seluruh transaksi yang terhubung dengan siswa ini (pemasukan saja = tagihan siswa)
            $riwayatPembayaran = Finance::where('athlete_id', $athlete->id)
                                        ->where('jenis', 'pemasukan')
                                        ->orderBy('tanggal', 'desc')
                                        ->orderBy('id', 'desc')
                                        ->get();

            // Total Tagihan = seluruh nominal yang ditagihkan ke siswa ini
            $totalTagihan    = $riwayatPembayaran->sum('nominal');

            // Total Terbayar = yang status_bayar = 'lunas'
            $totalTerbayar   = $riwayatPembayaran->where('status_bayar', 'lunas')->sum('nominal');

            // Belum Terbayar = yang status_bayar = 'belum_lunas'
            $totalBelumBayar = $riwayatPembayaran->where('status_bayar', 'belum_lunas')->sum('nominal');
        }

        $finances        = Finance::orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(15);
        $totalPemasukan  = Finance::where('jenis', 'pemasukan')->where('status_bayar', 'lunas')->sum('nominal');
        $totalPengeluaran= Finance::where('jenis', 'pengeluaran')->sum('nominal');
        $saldoSekarang   = $totalPemasukan - $totalPengeluaran;

        return view('wali.keuangan', compact(
            'myAthletes', 'athlete', 'riwayatPembayaran', 'finances',
            'totalPemasukan', 'totalPengeluaran', 'saldoSekarang',
            'totalTagihan', 'totalTerbayar', 'totalBelumBayar'
        ));
    }

    // 4. Daftar Pengumuman Lengkap
    public function pengumuman()
    {
        $announcements = Announcement::query()->where('is_active', true)->latest('created_at')->paginate(10);
        return view('wali.pengumuman', compact('announcements'));
    }

    // 5. Profil & Manajemen Keamanan Akun Mandiri
    public function profile()
    {
        $user = Auth::user();
        $myAthletes = $user->athletes;
        return view('wali.profile', compact('user', 'myAthletes'));
    }

    // 6. Proses Ubah Password Mandiri oleh Wali Murid
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required'         => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini yang Anda masukkan salah.',
            'password.required'                 => 'Password baru wajib diisi.',
            'password.min'                      => 'Password baru minimal harus 8 karakter.',
            'password.confirmed'                => 'Konfirmasi password baru tidak cocok.',
            'password.different'                => 'Password baru tidak boleh sama dengan password saat ini.',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        Log::info('Wali murid berhasil memperbarui password mandiri', [
            'user_id'  => $user->id,
            'username' => $user->username,
            'ip'       => $request->ip(),
        ]);

        return redirect()->route('wali.profile')->with('success', 'Password akun Anda berhasil diperbarui! Gunakan password baru ini untuk login berikutnya.');
    }
}