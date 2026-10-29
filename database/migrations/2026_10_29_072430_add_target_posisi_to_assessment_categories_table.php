<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('assessment_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('assessment_categories', 'target_posisi')) {
                // Hapus ->after('nama_kategori') dari baris di bawah ini
                $table->enum('target_posisi', ['Player', 'Kiper'])->default('Player');
            }
        });
    }

    public function down(): void
    {
        Schema::table('assessment_categories', function (Blueprint $table) {
            if (Schema::hasColumn('assessment_categories', 'target_posisi')) {
                $table->dropColumn('target_posisi');
            }
        });
    }
};
