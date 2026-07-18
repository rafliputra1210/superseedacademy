<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Jika belum login, tendang ke halaman login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Cek apakah role user saat ini ada dalam daftar role yang diizinkan
        $userRole = Auth::user()->role;
        if (!in_array($userRole, $roles)) {
            // Jika salah kamar, kembalikan ke dashboard masing-masing
            if ($userRole === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($userRole === 'wali_murid') {
                return redirect()->route('wali.dashboard');
            }
        }

        return $next($request);
    }
}