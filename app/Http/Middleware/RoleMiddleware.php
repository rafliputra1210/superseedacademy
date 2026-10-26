<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Periksa apakah user terautentikasi dan memiliki role yang diizinkan.
     * Fail-closed: tolak akses jika role tidak dikenali.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('warning', 'Silakan login untuk mengakses halaman ini.');
        }

        $user     = Auth::user();
        $userRole = $user->role;

        // Cek apakah role user ada dalam daftar role yang diizinkan
        if (!in_array($userRole, $roles)) {
            // Log akses tidak sah
            Log::warning('Unauthorized role access attempt', [
                'user_id'        => $user->id,
                'username'       => $user->username,
                'user_role'      => $userRole,
                'required_roles' => $roles,
                'path'           => $request->path(),
                'ip'             => $request->ip(),
                'user_agent'     => $request->userAgent(),
            ]);

            // Kembalikan ke halaman masing-masing berdasarkan role
            return match($userRole) {
                'admin'      => redirect()->route('admin.dashboard')
                                    ->with('error', 'Anda tidak memiliki akses ke area tersebut.'),
                'wali_murid' => redirect()->route('wali.dashboard')
                                    ->with('error', 'Anda tidak memiliki akses ke area tersebut.'),
                'asesor'     => redirect()->route('asesor.penilaian.index')
                                    ->with('error', 'Anda tidak memiliki akses ke area tersebut.'),
                // Fail-closed: role tidak dikenal
                default      => abort(403, 'Akses ditolak. Peran Anda tidak memiliki izin untuk mengakses halaman ini.'),
            };
        }

        return $next($request);
    }
}