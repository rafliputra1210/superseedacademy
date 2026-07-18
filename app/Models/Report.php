<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'athlete_id',
        'nomor_asesmen',
        'tanggal_asesmen',
        'asesor_nama',
        'aspek_teknis',
        'aspek_taktikal',
        'aspek_fisik_mental',
        'kekuatan_utama',
        'area_peningkatan',
        'catatan_perilaku',
        'is_archived',
    ];

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }
}
