<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman form login.
     * Redirect otomatis jika sudah terautentikasi.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    /**
     * Memproses autentikasi login dengan rate limiting berlapis.
     */
    public function login(Request $request)
    {
        // Validasi input: username hanya boleh alphanumeric + underscore, max 100 karakter
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z0-9_]+$/'],
            'password' => ['required', 'string', 'max:255'],
        ], [
            'username.regex' => 'Username hanya boleh mengandung huruf, angka, dan underscore.',
        ]);

        // Rate limiter key: gabungan username + IP (double throttle)
        $throttleKey = Str::lower($request->input('username')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            Log::warning('Login rate limit exceeded', [
                'username'   => $request->input('username'),
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
                'retry_in'   => $seconds,
            ]);

            throw ValidationException::withMessages([
                'username' => __('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ])->status(429);
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);

            // Regenerate session ID untuk mencegah session fixation
            $request->session()->regenerate();

            // Log login berhasil (tanpa password)
            Log::info('User logged in successfully', [
                'user_id'    => Auth::id(),
                'username'   => Auth::user()->username,
                'role'       => Auth::user()->role,
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return $this->redirectBasedOnRole(Auth::user()->role);
        }

        // Login gagal: tambah counter rate limiter
        RateLimiter::hit($throttleKey, 60);

        Log::warning('Failed login attempt', [
            'username'   => $request->input('username'),
            'ip'         => $request->ip(),
            'user_agent' => $request->userAgent(),
            'attempts'   => RateLimiter::attempts($throttleKey),
        ]);

        // Pesan error umum (tidak membocorkan informasi: "username salah" vs "password salah")
        throw ValidationException::withMessages([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ]);
    }

    /**
     * Redirect berdasarkan role user yang telah login.
     */
    private function redirectBasedOnRole(string $role)
    {
        return match($role) {
            'admin'      => redirect()->route('admin.dashboard'),
            'wali_murid' => redirect()->route('wali.dashboard'),
            'asesor'     => redirect()->route('asesor.penilaian.index'),
            default      => redirect()->route('login')->with('error', 'Role tidak dikenali. Hubungi administrator.'),
        };
    }

    /**
     * Proses Logout: invalidasi sesi dan token CSRF.
     */
    public function logout(Request $request)
    {
        $userId   = Auth::id();
        $username = Auth::user()?->username;
        $role     = Auth::user()?->role;

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('User logged out', [
            'user_id'  => $userId,
            'username' => $username,
            'role'     => $role,
            'ip'       => $request->ip(),
        ]);

        return redirect()->route('login');
    }
}