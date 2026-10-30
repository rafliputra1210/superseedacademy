<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AthleteAchievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_biography_id',
        'nama_turnamen',
        'pencapaian',
        'tingkat',
        'kategori',
        'tanggal',
        'penghargaan',
        'keterangan',
    ];
}