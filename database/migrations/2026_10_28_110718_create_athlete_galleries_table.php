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
        Schema::create('athlete_galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_biography_id')->constrained('athlete_biographies')->onDelete('cascade');
            
            $table->string('judul');
            $table->date('tanggal');
            $table->string('kategori_media'); // Misal: Latihan, Pertandingan, Lainnya
            $table->string('file_foto')->nullable(); // Path foto jika upload
            $table->string('tautan_video')->nullable(); // Link YouTube jika video
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('athlete_galleries');
    }
};
