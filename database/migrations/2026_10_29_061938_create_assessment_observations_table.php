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
        Schema::create('assessment_observations', function (Blueprint $table) {
            $table->id();
            // Menghubungkan observasi dengan ID penilaian/raport utama
            $table->foreignId('assessor_score_id')->constrained('assessor_scores')->onDelete('cascade');
            
            // Kolom Kualitatif (Tanpa Skor)
            $table->text('kekuatan_utama')->nullable();
            $table->text('area_ditingkatkan')->nullable();
            $table->text('catatan_perilaku')->nullable();
            $table->text('fokus_latihan')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_observations');
    }
};
