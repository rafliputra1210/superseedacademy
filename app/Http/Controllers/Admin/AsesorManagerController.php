<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AsesorManagerController extends Controller
{
    /**
     * Menampilkan daftar akun asesor.
     */
    public function index()
    {
        $asesors = User::where('role', 'asesor')->latest()->get();
        return view('admin.asesor_manager.index', compact('asesors'));
    }

    /**
     * Menyimpan akun asesor baru.
     * Validasi diperkuat: password min 8 char + confirmed, username hanya alphanumeric.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'username'              => ['required', 'string', 'max:50', 'unique:users,username', 'regex:/^[a-zA-Z0-9_]+$/'],
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ], [
            'name.required'                  => 'Nama lengkap asesor wajib diisi.',
            'username.required'              => 'Username wajib diisi.',
            'username.regex'                 => 'Username hanya boleh mengandung huruf, angka, dan underscore (_).',
            'username.unique'                => 'Username ini sudah digunakan, silakan pilih username lain.',
            'password.required'              => 'Password wajib diisi.',
            'password.min'                   => 'Password minimal 8 karakter.',
            'password.confirmed'             => 'Konfirmasi password tidak cocok dengan password yang dimasukkan.',
            'password_confirmation.required' => 'Konfirmasi password wajib diisi.',
        ]);

        try {
            $newUser = User::create([
                'name'     => $request->name,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role'     => 'asesor', // Role selalu di-hardcode, tidak dari input user
            ]);

            Log::info('New asesor account created by admin', [
                'admin_id'            => Auth::id(),
                'new_asesor_id'       => $newUser->id,
                'new_asesor_username' => $newUser->username,
                'ip'                  => $request->ip(),
            ]);

            return back()->with('success', 'Akun Asesor berhasil ditambahkan!');
        } catch (\Throwable $e) {
            Log::error('Failed to create asesor account', [
                'error'    => $e->getMessage(),
                'admin_id' => Auth::id(),
            ]);

            $message = 'Gagal menyimpan akun asesor.';
            if (str_contains(strtolower($e->getMessage()), 'truncated') || str_contains(strtolower($e->getMessage()), 'enum')) {
                $message .= ' Database server belum mendukung role "asesor". Silakan jalankan `php artisan migrate` di terminal hosting Anda atau jalankan query ALTER TABLE di database.';
            } else {
                $message .= ' Error: ' . $e->getMessage();
            }

            return back()->withInput()->with('error', $message);
        }
    }

    /**
     * Menghapus akun asesor.
     * Proteksi: hanya akun role 'asesor' yang boleh dihapus melalui endpoint ini.
     */
    public function destroy(User $user)
    {
        // Proteksi Broken Access Control: cegah penghapusan akun selain asesor
        if ($user->role !== 'asesor') {
            Log::warning('Unauthorized asesor delete attempt', [
                'admin_id'        => Auth::id(),
                'target_user_id'  => $user->id,
                'target_role'     => $user->role,
                'ip'              => request()->ip(),
            ]);
            return back()->with('error', 'Akses ditolak: hanya akun asesor yang dapat dihapus melalui fitur ini!');
        }

        Log::info('Asesor account deleted by admin', [
            'admin_id'          => Auth::id(),
            'deleted_user_id'   => $user->id,
            'deleted_username'  => $user->username,
            'ip'                => request()->ip(),
        ]);

        $user->delete();

        return back()->with('success', 'Akun Asesor berhasil dihapus!');
    }
}