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
    Schema::create('attendances', function (Blueprint $table) {
        $table->id();
        $table->foreignId('athlete_id')->constrained('athletes')->cascadeOnDelete();
        $table->date('tanggal');
        $table->enum('status', ['hadir', 'izin', 'sakit', 'alpa'])->default('hadir');
        $table->string('kode_barcode')->unique()->nullable(); // Untuk generate barcode/QR
        $table->string('foto_bukti')->nullable(); // Foto absensi
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
