<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('kelompok_usia'); // Contoh: Under 10 (U-10)
            $table->string('hari'); // Contoh: Selasa & Kamis
            $table->string('waktu'); // Contoh: 15.30 - 17.00 WIB
            $table->string('lokasi'); // Contoh: Lapangan Utama Superseed A
            $table->foreignId('coach_id')->nullable()->constrained('coaches')->nullOnDelete(); // Relasi ke tabel Coach
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};