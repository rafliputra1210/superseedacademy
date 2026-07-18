<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coach extends Model
{
    protected $fillable = [
        'nama',
        'alamat',
        'nomor_wa',
        'status_lisensi',
        'detail_lisensi',
        'referensi',
        'foto',
    ];
}
