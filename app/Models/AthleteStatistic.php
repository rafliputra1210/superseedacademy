<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AthleteStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_biography_id',
        'nama_pertandingan',
        'nama_lawan',
        'tanggal',
        'menit_bermain',
        'gol',
        'assist',
        'saves',
        'clean_sheet',
        'nilai_evaluasi',
        'catatan_coach',
    ];
}
