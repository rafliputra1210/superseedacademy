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
        Schema::create('assessor_scores', function (Blueprint $table) {
    $table->id();
    $table->foreignId('athlete_id')->constrained()->cascadeOnDelete();
    $table->foreignId('assessor_id')->constrained('users')->cascadeOnDelete(); // ID Akun Asesor
    $table->string('periode_bulan'); // Contoh: "Oktober 2026"
    
    // Aspek Penilaian (Sesuaikan dengan form Anda)
    $table->decimal('nilai_teknik', 5, 2);
    $table->decimal('nilai_taktik', 5, 2);
    $table->decimal('nilai_fisik', 5, 2);
    $table->decimal('nilai_mental', 5, 2);
    $table->text('catatan_asesor')->nullable();
    
    $table->timestamps();

    // Pastikan 1 asesor hanya bisa menilai 1 atlet di periode yang sama
    $table->unique(['athlete_id', 'assessor_id', 'periode_bulan'], 'unik_penilaian_asesor');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessor_scores');
    }
};
