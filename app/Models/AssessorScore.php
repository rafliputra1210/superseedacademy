<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AssessorScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'assessor_id',
        'periode_bulan',
        'scores',
        'nilai_teknik',
        'nilai_taktik',
        'nilai_fisik',
        'nilai_mental',
        'catatan_asesor',
    ];

    protected $casts = [
        'scores' => 'array',
    ];

    // Relasi ke tabel Atlet
    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    // Relasi ke tabel User (untuk mengambil nama Asesor)
    public function assessor()
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

    /**
     * Helper untuk mengambil nilai kategori tertentu (support dynamic scores & legacy columns)
     */
    public function getCategoryScore($categoryId, $categoryName = '')
    {
        if (is_array($this->scores) && isset($this->scores[$categoryId])) {
            return $this->scores[$categoryId];
        }

        $slug = Str::slug($categoryName);
        if ($slug === 'teknik') return $this->nilai_teknik;
        if ($slug === 'taktik') return $this->nilai_taktik;
        if ($slug === 'fisik') return $this->nilai_fisik;
        if ($slug === 'mental') return $this->nilai_mental;

        return null;
    }
}