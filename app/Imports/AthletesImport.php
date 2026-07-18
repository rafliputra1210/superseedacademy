<?php

namespace App\Imports;

use App\Models\Athlete;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class AthletesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. SMART DETEKSI NAMA SISWA (Bisa pakai header: nama / nama_siswa / nama_lengkap_siswa)
        $namaSiswa = trim($row['nama_lengkap_siswa'] ?? $row['nama_siswa'] ?? $row['nama'] ?? $row['name'] ?? '');
        
        // Jika baris kosong (tidak ada nama), lewati
        if ($namaSiswa === '') {
            return null;
        }

        // 2. SMART DETEKSI TANGGAL LAHIR & PASSWORD
        $tanggalLahirRaw = $row['tanggal_lahir'] ?? $row['tgl_lahir'] ?? $row['tgl'] ?? null;
        $tanggalLahir = date('Y-m-d'); // Default hari ini jika kosong/rusak
        $password = 'superseed123';    // Default password jika tanggal rusak

        if ($tanggalLahirRaw) {
            try {
                if (is_numeric($tanggalLahirRaw) && (int)$tanggalLahirRaw >= 10000 && (int)$tanggalLahirRaw <= 80000) {
                    $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalLahirRaw);
                    $tanggalLahir = $dt->format('Y-m-d');
                    $password = $dt->format('dmY'); // Format tanggal lahir (misal: 05092014)
                } else {
                    $dateStr = (string)$tanggalLahirRaw;
                    $cleanDateStr = str_replace(['/', '_', ' '], '-', $dateStr);
                    $parsedDate = null;
                    if (is_numeric($cleanDateStr)) {
                        if (strlen($cleanDateStr) === 8) {
                            $yearPart = (int)substr($cleanDateStr, 0, 4);
                            if ($yearPart >= 1900 && $yearPart <= 2100) {
                                try {
                                    $parsedDate = Carbon::createFromFormat('!Ymd', $cleanDateStr);
                                } catch (\Exception $ex) {}
                            }
                            if (!$parsedDate) {
                                try {
                                    $parsedDate = Carbon::createFromFormat('!dmY', $cleanDateStr);
                                } catch (\Exception $ex) {}
                            }
                        } elseif (strlen($cleanDateStr) === 7) {
                            try {
                                $parsedDate = Carbon::createFromFormat('!dmY', '0' . $cleanDateStr);
                            } catch (\Exception $ex) {}
                        }
                    }
                    if (!$parsedDate) {
                        $parsedDate = Carbon::parse($cleanDateStr);
                    }
                    $tanggalLahir = $parsedDate->format('Y-m-d');
                    $password = $parsedDate->format('dmY'); // Format tanggal lahir (misal: 05092014)
                }
            } catch (\Exception $e) {
                // Jika gagal parse tanggal, biarkan pakai default (tanpa error)
            }
        }

        // 3. SMART DETEKSI WA ORTU (Bisa: nomor_wa_orang_tua / nomor_wa_ortu / wa_ortu / no_wa)
        $waOrtu = $row['nomor_wa_orang_tua'] ?? $row['nomor_wa_ortu'] ?? $row['wa_ortu'] ?? $row['no_wa_ortu'] ?? $row['no_wa'] ?? '081234567890';
        
        // 5. SMART DETEKSI WA SISWA
        $waSiswa = $row['nomor_wa_siswa'] ?? $row['wa_siswa'] ?? $row['no_wa_siswa'] ?? null;

        // 6. SMART DETEKSI POSISI & NOMOR PUNGGUNG
        $posisi = $row['posisi_bermain'] ?? $row['posisi'] ?? 'Gelandang (Midfielder)';
        
        // Menangani kolom "Kelompok Usia / No. Punggung" (Misal isi "U-12", kita ambil angkanya atau biarkan null)
        $noPunggungRaw = $row['kelompok_usia_no_punggung'] ?? $row['nomor_punggung'] ?? $row['no_punggung'] ?? null;
        $noPunggung = is_numeric($noPunggungRaw) ? $noPunggungRaw : null;

        // SMART DETEKSI KELOMPOK UMUR & KELOMPOK LATIHAN
        $kelompokUmur = $row['kelompok_umur'] ?? $row['ku'] ?? $row['umur'] ?? 'U-12'; // Default U-12 jika kosong
        $kelompokLatihan = $row['kelompok_latihan'] ?? $row['kelas'] ?? $row['kelas_latihan'] ?? 'Kelas Reguler';

        // 4. SMART UPDATE ATAU CREATE AKUN WALI MURID
        $cleanName = Str::slug($namaSiswa, '');
        $username = $cleanName;
        
        $athlete = Athlete::query()->where('nama', $namaSiswa)->first();
        $newUser = null;

        if ($athlete && $athlete->user_id) {
            $newUser = User::query()->find($athlete->user_id);
            if ($newUser) {
                // Update akun yang sudah ada (update nama wali dan reset password ke tgl lahir terbaru)
                $newUser->update([
                    'name'     => $row['nama_orang_tua'] ?? ('Wali dari ' . $namaSiswa),
                    'password' => Hash::make($password),
                ]);
            }
        }

        if (!$newUser) {
            // Jika user belum ada, buat baru dengan username unik
            $finalUsername = $username;
            $counter = 1;
            while (User::query()->where('username', $finalUsername)->exists()) {
                $finalUsername = $username . $counter;
                $counter++;
            }

            $newUser = User::create([
                'name'     => $row['nama_orang_tua'] ?? ('Wali dari ' . $namaSiswa),
                'username' => $finalUsername,
                'password' => Hash::make($password),
                'role'     => 'wali_murid',
            ]);
        }

        // 5. UPDATE ATAU CREATE DATA ATLET
        if ($athlete) {
            $athlete->update([
                'kelompok_umur'    => $kelompokUmur,
                'kelompok_latihan' => $kelompokLatihan,
                'nomor_punggung'   => $noPunggung,
                'tanggal_lahir'    => $tanggalLahir,
                'posisi_bermain'   => $posisi,
                'nomor_wa'         => $waSiswa,
                'nomor_wa_ortu'    => $waOrtu,
                'alamat'           => $row['alamat'] ?? $row['alamat_lengkap'] ?? null,
                'user_id'          => $newUser->id,
            ]);
            return null; // Return null agar library Excel tidak melakukan insert double
        }

        $kodeBarcode = 'SSA-' . date('Y') . '-' . rand(1000, 9999);
        while (Athlete::query()->where('kode_barcode', $kodeBarcode)->exists()) {
            $kodeBarcode = 'SSA-' . date('Y') . '-' . rand(1000, 9999);
        }

        return new Athlete([
            'nama'             => $namaSiswa,
            'kode_barcode'     => $kodeBarcode,
            'kelompok_umur'    => $kelompokUmur,
            'kelompok_latihan' => $kelompokLatihan,
            'nomor_punggung'   => $noPunggung,
            'tanggal_lahir'    => $tanggalLahir,
            'posisi_bermain'   => $posisi,
            'nomor_wa'         => $waSiswa,
            'nomor_wa_ortu'    => $waOrtu,
            'alamat'           => $row['alamat'] ?? $row['alamat_lengkap'] ?? null,
            'user_id'          => $newUser->id,
        ]); 
    }
}