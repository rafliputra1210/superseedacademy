<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Agar rute detail di web publik bisa menggunakan slug, bukan id angka biasa
    public function getRouteKeyName()
    {
        return 'slug';
    }
}