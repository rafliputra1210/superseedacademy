<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom metode_pembayaran (cash / transfer) ke tabel finances.
     * Nullable agar data lama tidak terganggu.
     */
    public function up(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            $table->enum('metode_pembayaran', ['cash', 'transfer'])
                  ->nullable()
                  ->after('status_bayar')
                  ->comment('Metode pembayaran: cash (tunai) atau transfer (bank)');
            $table->string('nama_pengirim_transfer', 100)
                  ->nullable()
                  ->after('metode_pembayaran')
                  ->comment('Nama atas nama pengirim transfer / pemilik rekening');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            $table->dropColumn(['metode_pembayaran', 'nama_pengirim_transfer']);
        });
    }
};
