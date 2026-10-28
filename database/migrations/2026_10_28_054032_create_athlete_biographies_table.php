<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('athlete_biographies', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel Data Atlet (Murid) yang ada[cite: 16]
            $table->foreignId('athlete_id')->constrained()->onDelete('cascade'); 
            
            // Kategori kelas sesuai spesifikasi[cite: 16]
            // Enum: '01', '02', '03', '04' (New Born, U-10, U-13, U-16)
            $table->string('kategori_kelas'); 
            
            // Data Akademi[cite: 17]
            $table->date('tanggal_bergabung');
            $table->boolean('status_aktif')->default(true);
            
            // Profil Pemain[cite: 17]
            $table->string('posisi')->nullable(); // Kiper/Anchor/Flank/Pivot/Universal
            $table->string('kaki_dominan')->nullable();
            $table->integer('nomor_punggung')->nullable();
            $table->integer('tinggi_cm')->nullable();
            $table->decimal('berat_kg', 5, 2)->nullable();
            
            // Opsional: Field yang mungkin belum ada di tabel master athletes[cite: 17]
            $table->string('foto_profil')->nullable();
            $table->string('asal_sekolah')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('athlete_biographies');
    }
};