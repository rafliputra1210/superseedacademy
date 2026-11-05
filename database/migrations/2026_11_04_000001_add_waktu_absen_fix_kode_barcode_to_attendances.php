<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Tambah kolom waktu_absen (waktu masuk scan)
            if (!Schema::hasColumn('attendances', 'waktu_absen')) {
                $table->time('waktu_absen')->nullable()->after('status');
            }

            // Hapus UNIQUE constraint kode_barcode agar atlet bisa absen di banyak hari
            // (kode_barcode di attendances = referensi saja, bukan primary key unik)
            if (Schema::hasColumn('attendances', 'kode_barcode')) {
                $table->dropUnique('attendances_kode_barcode_unique');
                $table->string('kode_barcode')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'waktu_absen')) {
                $table->dropColumn('waktu_absen');
            }
        });
    }
};

