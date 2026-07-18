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
        Schema::table('finances', function (Blueprint $table) {
            // Status pembayaran: 'lunas' = sudah dibayar, 'belum_lunas' = tagihan belum dibayar
            $table->enum('status_bayar', ['lunas', 'belum_lunas'])->default('lunas')->after('nominal');
            // Tanggal jatuh tempo tagihan (jika tagihan belum lunas)
            $table->date('tanggal_jatuh_tempo')->nullable()->after('status_bayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            $table->dropColumn(['status_bayar', 'tanggal_jatuh_tempo']);
        });
    }
};
