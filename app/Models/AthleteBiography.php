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
    
    // Mutator untuk menghitung lama bergabung[cite: 17]
    public function getLamaBergabungAttribute()
    {
        return \Carbon\Carbon::parse($this->tanggal_bergabung)->diffForHumans();
    }
}