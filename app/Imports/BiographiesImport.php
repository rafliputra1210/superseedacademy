<?php

namespace App\Imports;

use App\Models\Athlete;
use App\Models\AthleteBiography;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class BiographiesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. Cari data atlet berdasarkan Nomor Induk (NIS) dari file Excel
        // Pastikan header di Excel bernama "nis_atlet"
        $athlete = Athlete::where('nomor_induk', $row['nis_atlet'])->first();

        // Jika NIS tidak ditemukan di database, lewati baris ini
        if (!$athlete) {
            return null; 
        }

        // 2. Cek apakah atlet tersebut sudah punya biografi
        $sudahAda = AthleteBiography::where('athlete_id', $athlete->id)->exists();
        
        // Jika sudah punya, lewati agar tidak dobel data
        if ($sudahAda) {
            return null; 
        }

        // 3. Masukkan data ke database
        return new AthleteBiography([
            'athlete_id'        => $athlete->id,
            // Format kategori agar pasti 2 digit (misal Excel tulis "1", jadikan "01")
            'kategori_kelas'    => str_pad($row['kategori_kelas'], 2, '0', STR_PAD_LEFT),
            // Jika tanggal di Excel berupa text YYYY-MM-DD
            'tanggal_bergabung' => $row['tanggal_bergabung'],
            'posisi'            => $row['posisi'] ?? null,
            'status_aktif'      => 1, // Default otomatis aktif
        ]);
    }
}