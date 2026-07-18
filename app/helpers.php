<?php

if (!function_exists('storage_img_url')) {
    /**
     * Helper untuk menghasilkan URL gambar yang benar di hosting shared.
     * Mendukung dua format path yang disimpan di database:
     *
     * 1. Path LAMA (dari ->store('folder', 'public')): "athletes/foto.jpg"
     *    → Diarahkan ke "uploads/athletes/foto.jpg" (foto sudah dipindahkan)
     *
     * 2. Path BARU (disimpan langsung ke public/uploads/): "uploads/athletes/foto.jpg"
     *    → Langsung gunakan asset()
     *
     * @param string|null $path
     * @return string
     */
    function storage_img_url(?string $path): string
    {
        if (empty($path)) {
            return '';
        }

        // Path baru sudah diawali 'uploads/' — langsung pakai asset()
        if (str_starts_with($path, 'uploads/')) {
            return asset($path);
        }

        // Path lama (misal: "athletes/foto.jpg", "coaches/foto.jpg", "news/foto.jpg", dll.)
        // Foto-foto ini sudah dipindahkan ke public/uploads/
        // Jadi tambahkan prefix 'uploads/' untuk membentuk path yang benar
        return asset('uploads/' . $path);
    }
}
