<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Raport extends Model
{
    use HasFactory;

    protected $table = 'raports';

    protected $fillable = [
        'athlete_id',
        'periode',
        'scores_summary',
        'nilai_teknik_final',
        'nilai_taktik_final',
        'nilai_fisik_final',
        'nilai_mental_final',
        'catatan_pelatih',
        'rekap_skor_100',
        'rekap_rata_rata',
        'observasi_kualitatif',
        'status_publish',
        'is_archived',
    ];

    protected $casts = [
        'scores_summary'       => 'array',
        'observasi_kualitatif' => 'array',
        'is_archived'          => 'boolean',
    ];

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }
}
