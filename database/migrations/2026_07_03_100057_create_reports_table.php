<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained('athletes')->cascadeOnDelete();
            
            // A. Data Asesmen
            $table->string('nomor_asesmen', 50)->nullable();
            $table->date('tanggal_asesmen');
            $table->string('asesor_nama', 100);
            
            // B, C, D. Data Penilaian (Skor 1-5 & Catatan) disimpan dalam format JSON agar fleksibel
            $table->json('aspek_teknis');
            $table->json('aspek_taktikal');
            $table->json('aspek_fisik_mental');
            
            // E. Observasi Kualitatif
            $table->text('kekuatan_utama')->nullable();
            $table->text('area_peningkatan')->nullable();
            $table->text('catatan_perilaku')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};