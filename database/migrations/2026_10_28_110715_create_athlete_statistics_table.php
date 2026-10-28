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
        Schema::create('athlete_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_biography_id')->constrained('athlete_biographies')->onDelete('cascade');
            
            $table->string('nama_pertandingan');
            $table->string('nama_lawan');
            $table->date('tanggal');
            $table->integer('menit_bermain')->default(0);
            $table->integer('gol')->default(0);
            $table->integer('assist')->default(0);
            
            // Khusus Kiper
            $table->integer('saves')->nullable();
            $table->boolean('clean_sheet')->default(false);
            
            // Evaluasi Pelatih
            $table->integer('nilai_evaluasi')->nullable(); // Skala 1-10 atau 1-100
            $table->text('catatan_coach')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('athlete_statistics');
    }
};
