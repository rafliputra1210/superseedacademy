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
}