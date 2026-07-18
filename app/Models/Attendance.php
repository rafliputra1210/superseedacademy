<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'athlete_id',
        'tanggal',
        'status',
        'kode_barcode',
        'foto_bukti'
    ];

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }
}
