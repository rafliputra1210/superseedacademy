<?php

if (!function_exists('storage_img_url')) {
    /**
     * Helper untuk menghasilkan URL gambar yang benar di hosting shared & VPS.
     * Mendukung format path storage, uploads, dan fallback otomatis.
     *
     * @param string|null $path
     * @return string
     */
    function storage_img_url(?string $path): string
    {
        if (empty($path)) {
            return '';
        }

        // Full URL
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Jika path sudah diawali 'uploads/'
        if (str_starts_with($path, 'uploads/')) {
            return asset($path);
        }

        // Bersihkan jika diawali 'storage/'
        $cleanPath = ltrim(preg_replace('#^storage/#', '', $path), '/');

        // 1. Cek di public/uploads/
        if (file_exists(public_path('uploads/' . $cleanPath))) {
            return asset('uploads/' . $cleanPath);
        }

        // 2. Cek di public/storage/ (jika symlink aktif)
        if (file_exists(public_path('storage/' . $cleanPath))) {
            return asset('storage/' . $cleanPath);
        }

        // 3. Cek di storage/app/public/ lalu auto-mirror ke public/uploads/
        $storageFile = storage_path('app/public/' . $cleanPath);
        if (file_exists($storageFile) && is_file($storageFile)) {
            $dest = public_path('uploads/' . $cleanPath);
            $dir = dirname($dest);
            if (!file_exists($dir)) {
                @mkdir($dir, 0755, true);
            }
            @copy($storageFile, $dest);
            if (file_exists($dest)) {
                return asset('uploads/' . $cleanPath);
            }
            return asset('storage/' . $cleanPath);
        }

        // Fallback default ke uploads/
        return asset('uploads/' . $cleanPath);
    }
}

if (!function_exists('file_to_base64')) {
    /**
     * Mengubah path file lokal ke format Base64 Data URI untuk cetak dokumen / print view.
     * Menjamin foto 100% muncul di print/PDF tanpa ketergantungan HTTP request atau symlink.
     *
     * @param string|null $path
     * @return string|null
     */
    function file_to_base64(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        // Bersihkan prefix
        $clean = ltrim(preg_replace('#^storage/|^uploads/#', '', $path), '/');
        $fileName = basename($path);

        $candidates = [
            public_path($path),
            public_path('uploads/' . $path),
            public_path('uploads/' . $clean),
            public_path('storage/' . $clean),
            storage_path('app/public/' . $clean),
            storage_path('app/public/' . $path),
            public_path('uploads/biography_photos/' . $fileName),
            storage_path('app/public/biography_photos/' . $fileName),
            public_path('uploads/athletes/' . $fileName),
            storage_path('app/public/athletes/' . $fileName),
        ];

        foreach ($candidates as $cand) {
            if (!empty($cand) && file_exists($cand) && is_file($cand)) {
                $ext = strtolower(pathinfo($cand, PATHINFO_EXTENSION));
                $mime = match ($ext) {
                    'png' => 'image/png',
                    'webp' => 'image/webp',
                    'gif' => 'image/gif',
                    'svg' => 'image/svg+xml',
                    default => 'image/jpeg',
                };
                $data = @file_get_contents($cand);
                if ($data !== false) {
                    return 'data:' . $mime . ';base64,' . base64_encode($data);
                }
            }
        }

        return null;
    }
}
