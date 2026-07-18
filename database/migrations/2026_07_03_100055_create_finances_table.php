<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finances', function (Blueprint $table) {
            $table->id();
            // Relasi ke siswa (Opsional, diisi jika kategorinya iuran siswa)
            $table->foreignId('athlete_id')->nullable()->constrained('athletes')->nullOnDelete();
            $table->string('bulan_tagihan', 50)->nullable(); // Contoh: "Juli 2026"
            $table->date('tanggal');
            $table->enum('jenis', ['pemasukan', 'pengeluaran']);
            $table->string('kategori'); // Iuran Uang Kas Bulanan, Sewa Lapangan, dll.
            $table->string('keterangan')->nullable();
            $table->decimal('nominal', 15, 2);
            $table->decimal('saldo_akhir', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finances');
    }
};