<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('athletes', function (Blueprint $table) {
            // Kolom kode_barcode unik untuk kebutuhan scan absensi
            $table->string('kode_barcode', 50)->unique()->nullable()->after('nama');
        });
    }

    public function down(): void
    {
        Schema::table('athletes', function (Blueprint $table) {
            $table->dropColumn('kode_barcode');
        });
    }
};