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
    Schema::create('coaches', function (Blueprint $table) {
        $table->id();
        $table->string('nama');
        $table->text('alamat')->nullable();
        $table->string('nomor_wa', 20)->nullable();
        $table->enum('status_lisensi', ['berlisensi', 'tidak_berlisensi'])->default('tidak_berlisensi');
        $table->string('detail_lisensi')->nullable(); // Misal: AFC B, PSSI D, dll.
        $table->text('referensi')->nullable(); // Referensi & Pengalaman melatih
        $table->string('foto')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coaches');
    }
};
