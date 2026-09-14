<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    protected $fillable = [
        'athlete_id',
        'bulan_tagihan',
        'tanggal',
        'jenis',
        'kategori',
        'keterangan',
        'nominal',
        'status_bayar',
        'metode_pembayaran',
        'nama_pengirim_transfer',
        'tanggal_jatuh_tempo',
        'saldo_akhir',
    ];

    public function athlete()
    {
        return $this->belongsTo(Athlete::class, 'athlete_id');
    }
}