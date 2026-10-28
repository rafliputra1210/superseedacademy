<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SecurityHeaders Middleware
 *
 * Menambahkan header keamanan HTTP komprehensif ke setiap response.
 * Menggantikan / melengkapi PreventBackHistory dengan header yang lebih lengkap.
 */
class SecurityHeaders
{
    /**
     * Header keamanan yang diterapkan ke setiap response.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // --- Anti-clickjacking ---
        $response->headers->set('X-Frame-Options', 'DENY');

        // --- Cegah MIME sniffing ---
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // --- XSS Filter (legacy browser) ---
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // --- Referrer Policy ---
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // --- Permissions Policy (batasi fitur browser berbahaya) ---
        $response->headers->set(
            'Permissions-Policy',
            'camera=(self), microphone=(), geolocation=(), payment=(), usb=(), magnetometer=(), gyroscope=(), accelerometer=()'
        );

        // --- Cross-Domain Policy ---
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        // --- Content-Security-Policy ---
        // Sesuaikan dengan CDN yang benar-benar digunakan di proyek ini
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.jsdelivr.net fonts.googleapis.com",
            "style-src 'self' 'unsafe-inline' cdn.jsdelivr.net fonts.googleapis.com fonts.gstatic.com",
            "font-src 'self' fonts.gstatic.com cdn.jsdelivr.net data:",
            "img-src 'self' data: blob: *",
            "connect-src 'self' cdn.jsdelivr.net fonts.googleapis.com fonts.gstatic.com",
            "media-src 'self' blob:",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        // --- Cache Control untuk halaman autentikasi ---
        if ($request->is('admin/*') || $request->is('portal-wali/*') || $request->is('asesor/*')) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        // --- HSTS hanya jika HTTPS aktif ---
        if ($request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // --- Hapus header fingerprinting server ---
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
        if (function_exists('header_remove')) {
            @header_remove('X-Powered-By');
            @header_remove('Server');
        }

        return $response;
    }
}
