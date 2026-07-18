<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WaliManagerController extends Controller
{
    // Menampilkan daftar seluruh siswa & status akun walinya
    public function index()
    {
        $athletes = Athlete::with('user')->orderBy('nama')->paginate(15);
        return view('admin.wali.index', compact('athletes'));
    }

    // Fitur 1-Klik Generate Akun Otomatis
    public function generate(Athlete $athlete)
    {
        // Cegah duplikasi jika sudah punya akun
        if ($athlete->user_id) {
            return redirect()->back()->with('error', 'Siswa ini sudah terhubung dengan akun wali!');
        }

        // 1. Buat username otomatis dari nama depan siswa + 2 angka acak
        // Contoh: Raditya Pratama -> ortu_raditya45
        $namaDepan = Str::slug(Str::words($athlete->nama, 1, ''));
        $username = 'ortu_' . $namaDepan . rand(10, 99);

        // Pastikan username benar-benar unik di database
        while (User::query()->where('username', $username)->exists()) {
            $username = 'ortu_' . $namaDepan . rand(100, 999);
        }

        $passwordDefault = 'superseed123';
        if ($athlete->tanggal_lahir) {
            $passwordDefault = \Carbon\Carbon::parse($athlete->tanggal_lahir)->format('dmY');
        }

        // 2. Buat akun di tabel users
        $newUser = User::create([
            'name' => 'Wali dari ' . $athlete->nama,
            'username' => $username,
            'password' => Hash::make($passwordDefault),
            'role' => 'wali_murid',
        ]);

        // 3. Hubungkan akun tersebut ke data siswa
        $athlete->update(['user_id' => $newUser->id]);

        // Kirim data kredensial ke session untuk ditampilkan di popup sukses
        return redirect()->back()->with('success_generate', [
            'nama_siswa' => $athlete->nama,
            'username' => $username,
            'password' => $passwordDefault,
            'nomor_wa' => $athlete->nomor_wa_ortu
        ]);
    }

    // Fitur 1-Klik Reset Password ke Default
    public function resetPassword(User $user)
    {
        $password = 'superseed123';
        $athlete = $user->athletes()->first();
        if ($athlete && $athlete->tanggal_lahir) {
            $password = \Carbon\Carbon::parse($athlete->tanggal_lahir)->format('dmY');
        }

        $user->update([
            'password' => Hash::make($password)
        ]);

        return redirect()->back()->with('success', "Password untuk akun (@{$user->username}) berhasil direset kembali menjadi: {$password}");
    }
}