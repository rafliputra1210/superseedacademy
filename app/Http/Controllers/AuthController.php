<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Menampilkan halaman form login
    public function showLoginForm()
    {
        // Jika sudah login, langsung arahkan sesuai role
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    // Memproses data login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $throttleKey = Str::lower($request->input('username')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'username' => 'Terlalu banyak percobaan login yang gagal. Silakan coba lagi dalam ' . $seconds . ' detik.',
            ])->onlyInput('username');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            
            // Redirect sesuai role
            return $this->redirectBasedOnRole(Auth::user()->role);
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ])->onlyInput('username');
    }

    // Fungsi helper untuk pengalihan role
    private function redirectBasedOnRole($role)
    {
        if ($role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }
        return redirect()->intended(route('wali.dashboard'));
    }

    // Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}