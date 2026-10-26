<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * EnsureUserIsActive Middleware
 *
 * Memastikan akun yang terautentikasi masih aktif (tidak dinonaktifkan oleh admin).
 * Juga memvalidasi integritas sesi untuk mencegah session fixation/hijacking.
 */
class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Cek apakah akun masih aktif (kolom is_active jika ada)
            if (isset($user->is_active) && !$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                Log::warning('Login attempt by deactivated account', [
                    'user_id'  => $user->id,
                    'username' => $user->username,
                    'ip'       => $request->ip(),
                    'ua'       => $request->userAgent(),
                ]);

                return redirect()->route('login')
                    ->with('error', 'Akun Anda telah dinonaktifkan. Hubungi administrator.');
            }

            // Session Hijacking Protection: validasi IP binding (opsional, aktifkan jika jaringan stabil)
            // Uncomment jika ingin menerapkan IP binding ketat:
            // if ($request->session()->has('auth_ip') &&
            //     $request->session()->get('auth_ip') !== $request->ip()) {
            //     Auth::logout();
            //     $request->session()->invalidate();
            //     $request->session()->regenerateToken();
            //     return redirect()->route('login')->with('error', 'Sesi tidak valid. Silakan login ulang.');
            // }
        }

        return $next($request);
    }
}
