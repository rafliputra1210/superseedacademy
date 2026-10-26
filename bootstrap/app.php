<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Terapkan Security Headers ke SEMUA response web
        $middleware->web(append: [
            SecurityHeaders::class,
            EnsureUserIsActive::class,
        ]);

        // Alias middleware
        $middleware->alias([
            'role'    => RoleMiddleware::class,
            'active'  => EnsureUserIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Redirect ke login dengan pesan saat CSRF token mismatch
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            return redirect()->route('login')
                ->with('warning', 'Sesi keamanan Anda telah kedaluwarsa. Silakan login kembali.');
        });

        // Handle 403 Forbidden dengan halaman yang informatif
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses ditolak.'], 403);
            }
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melakukan tindakan ini.');
        });
    })->create();