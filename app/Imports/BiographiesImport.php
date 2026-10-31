<?php

namespace App\Imports;

use App\Models\Athlete;
use App\Models\AthleteBiography;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class BiographiesImport implements ToModel, WithHeadingRow
{
    private int $importedCount = 0;
    private int $skippedCount = 0;
    private array $skippedReasons = [];

    public function model(array $row)
    {
        // 1. SMART EXTRACTION NIS ATLET (Dukung variasi nama header)
        $nis = trim((string)(
            $row['nis_atlet'] ?? 
            $row['nis'] ?? 
            $row['nomor_induk'] ?? 
            $row['no_induk'] ?? 
            $row['nis_siswa'] ?? 
            $row['kode_barcode'] ?? 
            $row['barcode'] ?? ''
        ));

        // Dukung juga deteksi nama atlet jika kolom nama disertakan di Excel
        $nama = trim((string)(
            $row['nama_atlet'] ?? 
            $row['nama_siswa'] ?? 
            $row['nama'] ?? ''
        ));

        // Abaikan baris kosong di Excel
        if ($nis === '' && $nama === '') {
            return null;
        }

        // 2. CARI DATA ATLET DI DATABASE
        $athlete = null;
        if ($nis !== '') {
            $athlete = Athlete::where('kode_barcode', $nis)->first();

            // Jika tidak ditemukan dan NIS berupa angka, coba tanpa leading zero
            if (!$athlete && is_numeric($nis)) {
                $unpadded = ltrim($nis, '0');
                if ($unpadded !== '') {
                    $athlete = Athlete::where('kode_barcode', $unpadded)->first();
                }
            }
        }

        // Fallback jika belum ketemu berdasarkan NIS atau NIS kosong, cari berdasarkan nama atlet!
        if (!$athlete && $nama !== '') {
            $athlete = Athlete::where('nama', $nama)
                ->orWhere('nama', 'like', "%{$nama}%")
                ->first();
        }

        // Jika data atlet tidak ditemukan sama sekali, lewati
        if (!$athlete) {
            $this->skippedCount++;
            $identitas = $nama ? "{$nama} (NIS: {$nis})" : "NIS: {$nis}";
            $this->skippedReasons[] = "Data atlet {$identitas} tidak terdaftar di database";
            return null;
        }

        // 3. CEK APAKAH ATLET SUDAH MEMILIKI BIOGRAFI
        $sudahAda = AthleteBiography::where('athlete_id', $athlete->id)->exists();
        if ($sudahAda) {
            $this->skippedCount++;
            $this->skippedReasons[] = "Atlet {$athlete->nama} (NIS: {$athlete->nomor_induk}) sudah memiliki dokumen biografi";
            return null;
        }

        // 4. SMART PARSING TANGGAL BERGABUNG (Cegah error SQL format date 45230 / d-m-Y)
        $tglRaw = $row['tanggal_bergabung'] ?? 
                  $row['tgl_bergabung'] ?? 
                  $row['tanggal_gabung'] ?? 
                  $row['tgl_gabung'] ?? 
                  $row['tgl_masuk'] ?? 
                  $row['tanggal_masuk'] ?? null;
        
        $tanggalBergabung = $this->parseDate($tglRaw);

        // 5. SMART PARSING KATEGORI KELAS (01, 02, 03, 04 atau nama U-10, U-13, dsb.)
        $katRaw = $row['kategori_kelas'] ?? 
                  $row['kategori'] ?? 
                  $row['kelas'] ?? 
                  $row['kode_kelas'] ?? null;

        $kategoriKelas = $this->parseKategoriKelas($katRaw, $athlete);

        // 6. EXTRACTION DATA TAMBAHAN (Posisi, Sekolah, Atribut Fisik)
        $posisi = trim((string)($row['posisi'] ?? $row['posisi_bermain'] ?? ''));
        if ($posisi === '' && !empty($athlete->posisi_bermain)) {
            $posisi = $athlete->posisi_bermain;
        }

        $asalSekolah = trim((string)($row['asal_sekolah'] ?? $row['sekolah'] ?? $row['sekolah_asal'] ?? ''));

        // Atribut opsional lainnya
        $nomorPunggung = $row['nomor_punggung'] ?? $row['no_punggung'] ?? $athlete->nomor_punggung ?? null;
        $nomorPunggung = is_numeric($nomorPunggung) ? (int)$nomorPunggung : null;

        $kakiDominan = trim((string)($row['kaki_dominan'] ?? $row['kaki'] ?? ''));
        if ($kakiDominan !== '') {
            $kakiLower = strtolower($kakiDominan);
            if (str_contains($kakiLower, 'kanan')) $kakiDominan = 'Kanan';
            elseif (str_contains($kakiLower, 'kiri')) $kakiDominan = 'Kiri';
            elseif (str_contains($kakiLower, 'dua') || str_contains($kakiLower, 'kedua')) $kakiDominan = 'Keduanya';
            else $kakiDominan = ucfirst($kakiDominan);
        } else {
            $kakiDominan = null;
        }

        $tinggiCm = $row['tinggi_cm'] ?? $row['tinggi'] ?? null;
        $tinggiCm = is_numeric($tinggiCm) ? (int)$tinggiCm : null;

        $beratKg = $row['berat_kg'] ?? $row['berat'] ?? null;
        $beratKg = is_numeric($beratKg) ? (float)$beratKg : null;

        // Foto default dari data master atlet jika ada
        $fotoProfil = !empty($athlete->foto) ? $athlete->foto : null;

        $this->importedCount++;

        return new AthleteBiography([
            'athlete_id'        => $athlete->id,
            'kategori_kelas'    => $kategoriKelas,
            'tanggal_bergabung' => $tanggalBergabung,
            'posisi'            => $posisi ?: null,
            'asal_sekolah'      => $asalSekolah ?: null,
            'nomor_punggung'    => $nomorPunggung,
            'kaki_dominan'      => $kakiDominan,
            'tinggi_cm'         => $tinggiCm,
            'berat_kg'          => $beratKg,
            'foto_profil'       => $fotoProfil,
            'status_aktif'      => 1,
        ]);
    }

    /**
     * Parse tanggal dari berbagai format Excel (Serial Number, Y-m-d, d/m/Y, dll)
     */
    private function parseDate($value): string
    {
        if (empty($value)) {
            return date('Y-m-d');
        }

        // 1. Jika serial number Excel (contoh: 45230 atau 45230.0)
        if (is_numeric($value) && (int)$value >= 10000 && (int)$value <= 80000) {
            try {
                $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $dt->format('Y-m-d');
            } catch (\Throwable $e) {}
        }

        // 2. Jika DateTime object
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        $str = trim((string)$value);

        // 3. Coba format umum dengan Carbon::createFromFormat
        $formats = ['Y-m-d', 'd-m-Y', 'd/m/Y', 'Y/m/d', 'm/d/Y', 'm-d-Y'];
        foreach ($formats as $fmt) {
            try {
                $parsed = Carbon::createFromFormat($fmt, $str);
                if ($parsed && $parsed->year >= 1970 && $parsed->year <= 2100) {
                    return $parsed->format('Y-m-d');
                }
            } catch (\Throwable $e) {}
        }

        // 4. Fallback Carbon::parse
        try {
            $cleanStr = str_replace(['/', '_', '.'], '-', $str);
            $parsed = Carbon::parse($cleanStr);
            if ($parsed && $parsed->year >= 1970 && $parsed->year <= 2100) {
                return $parsed->format('Y-m-d');
            }
        } catch (\Throwable $e) {}

        return date('Y-m-d');
    }

    /**
     * Parse kode kategori kelas (01, 02, 03, 04) dari berbagai input (angka atau nama kelas)
     */
    private function parseKategoriKelas($value, $athlete = null): string
    {
        if (!empty($value)) {
            $val = strtoupper(trim((string)$value));

            // Jika berupa digit angka
            if ($val === '1' || $val === '01') return '01';
            if ($val === '2' || $val === '02') return '02';
            if ($val === '3' || $val === '03') return '03';
            if ($val === '4' || $val === '04') return '04';

            // Jika teks nama kelompok
            if (str_contains($val, 'BORN') || str_contains($val, 'NEW')) return '01';
            if (str_contains($val, '10')) return '02';
            if (str_contains($val, '13')) return '03';
            if (str_contains($val, '16')) return '04';
        }

        // Fallback berdasarkan kelompok umur atlet
        if ($athlete && !empty($athlete->kelompok_umur)) {
            $ku = strtoupper(trim((string)$athlete->kelompok_umur));
            if (str_contains($ku, 'BORN') || str_contains($ku, 'NEW')) return '01';
            if (str_contains($ku, '10')) return '02';
            if (str_contains($ku, '13')) return '03';
            if (str_contains($ku, '16')) return '04';
        }

        return '02'; // Default Reguler Class U-10
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function getSkippedReasons(): array
    {
        return $this->skippedReasons;
    }
}
