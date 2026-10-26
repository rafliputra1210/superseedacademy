<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CORS Configuration — Super Seed Academy
    |--------------------------------------------------------------------------
    |
    | Aplikasi ini adalah server-side rendered web app (Blade + Laravel).
    | Tidak ada API publik atau konsumsi dari domain eksternal, sehingga
    | CORS dikonfigurasi sangat ketat: hanya mengizinkan request
    | dari origin yang sama (same-origin).
    |
    */

    /*
     * Pola path yang diizinkan mendapat response CORS header.
     * 'api/*' untuk endpoint API jika ada.
     * 'sanctum/csrf-cookie' untuk SPA (jika suatu saat ditambahkan).
     */
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    /*
     * HTTP method yang diizinkan dalam cross-origin request.
     * Batasi hanya method yang memang dipakai.
     */
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    /*
     * Origin yang diizinkan melakukan cross-origin request.
     * KOSONG ARRAY = tidak ada origin eksternal yang diizinkan (paling aman).
     * Jika ada mobile app atau SPA terpisah, tambahkan domain spesifik di sini.
     *
     * JANGAN gunakan '*' (wildcard) untuk aplikasi dengan autentikasi.
     */
    'allowed_origins' => [],

    /*
     * Pola regex untuk allowed origins (jika diperlukan).
     * Biarkan kosong karena tidak ada subdomain yang perlu diizinkan.
     */
    'allowed_origins_patterns' => [],

    /*
     * Header yang diizinkan dalam cross-origin request.
     */
    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization', 'Accept', 'X-XSRF-TOKEN'],

    /*
     * Header yang diekspos ke browser dari response server.
     */
    'exposed_headers' => [],

    /*
     * Durasi preflight cache (dalam detik).
     * 0 = tidak cache preflight (lebih aman untuk perubahan konfigurasi cepat).
     */
    'max_age' => 0,

    /*
     * Izinkan pengiriman credentials (cookie, auth header) pada cross-origin request.
     * Harus false jika allowed_origins = [] atau '*'.
     */
    'supports_credentials' => false,
];
