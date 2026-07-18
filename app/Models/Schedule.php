<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi: Satu jadwal latihan dibimbing oleh satu pelatih utama
    public function coach()
    {
        return $this->belongsTo(Coach::class, 'coach_id');
    }
}