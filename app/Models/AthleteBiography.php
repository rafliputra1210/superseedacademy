<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AthleteBiography extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'kategori_kelas',
        'tanggal_bergabung',
        'status_aktif',
        'posisi',
        'kaki_dominan',
        'nomor_punggung',
        'tinggi_cm',
        'berat_kg',
        'foto_profil',
        'asal_sekolah',
    ];

    // Relasi ke model Athlete (Data Murid)[cite: 16]
    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }
    
    // Accessor atribut untuk kompatibilitas view
    public function getNamaAtletAttribute()
    {
        return $this->athlete->nama ?? null;
    }

    public function getNisAttribute()
    {
        return $this->athlete->nomor_induk ?? $this->athlete->kode_barcode ?? null;
    }

    public function getNoPunggungAttribute()
    {
        return $this->nomor_punggung ?? $this->athlete->nomor_punggung ?? null;
    }

    public function getPosisiBermainAttribute()
    {
        return $this->posisi ?? $this->athlete->posisi_bermain ?? null;
    }

    public function getTinggiAttribute()
    {
        return $this->tinggi_cm;
    }

    public function getBeratAttribute()
    {
        return $this->berat_kg;
    }

    // Mutator untuk menghitung lama bergabung[cite: 17]
    public function getLamaBergabungAttribute()
    {
        return \Carbon\Carbon::parse($this->tanggal_bergabung)->diffForHumans();
    }
    public function achievements()
    {
        return $this->hasMany(AthleteAchievement::class);
    }

    public function statistics()
    {
        return $this->hasMany(AthleteStatistic::class);
    }

    public function galleries()
    {
        return $this->hasMany(AthleteGallery::class);
    }

    /**
     * URL Foto Profil Atlet: Cek foto_profil biografi, fallback ke foto atlet jika kosong.
     */
    public function getFotoUrlAttribute()
    {
        // 1. Ambil dari foto_profil biografi
        if (!empty($this->foto_profil)) {
            return storage_img_url($this->foto_profil);
        }

        // 2. Fallback ke foto master atlet jika ada
        if ($this->athlete && !empty($this->athlete->foto)) {
            return storage_img_url($this->athlete->foto);
        }

        return null;
    }

    /**
     * Base64 Data URI Foto Profil Atlet (sangat aman untuk halaman print PDF).
     */
    public function getFotoBase64Attribute()
    {
        // 1. Cek dari foto_profil biografi
        if (!empty($this->foto_profil)) {
            $b64 = file_to_base64($this->foto_profil);
            if ($b64) return $b64;
        }

        // 2. Fallback ke foto master atlet
        if ($this->athlete && !empty($this->athlete->foto)) {
            $b64 = file_to_base64($this->athlete->foto);
            if ($b64) return $b64;
        }

        return null;
    }
}