<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('athlete_achievements', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel profil utama
            $table->foreignId('athlete_biography_id')->constrained('athlete_biographies')->onDelete('cascade');
            
            $table->string('nama_turnamen');
            $table->date('tanggal');
            $table->string('tingkat'); // Misal: Lokal, Nasional, Internasional
            $table->string('kategori'); // Misal: U-13, Umum
            $table->string('pencapaian'); // Misal: Juara 1, Runner Up
            $table->string('penghargaan')->nullable(); // Misal: Top Skor, Pemain Terbaik
            $table->string('file_sertifikat')->nullable(); // Path foto sertifikat/medali
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('athlete_achievements');
    }
};
