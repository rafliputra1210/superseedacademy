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
        // 1. Indexing tabel attendances
        Schema::table('attendances', function (Blueprint $table) {
            $table->index(['athlete_id', 'tanggal'], 'idx_attendances_athlete_tanggal');
            $table->index(['tanggal', 'status'], 'idx_attendances_tanggal_status');
        });

        // 2. Indexing tabel raports
        Schema::table('raports', function (Blueprint $table) {
            $table->index('periode', 'idx_raports_periode');
            $table->index(['status_publish', 'is_archived'], 'idx_raports_publish_archived');
        });

        // 3. Indexing tabel finances
        Schema::table('finances', function (Blueprint $table) {
            $table->index('tanggal', 'idx_finances_tanggal');
            $table->index(['jenis', 'status_bayar'], 'idx_finances_jenis_status');
            $table->index(['athlete_id', 'jenis', 'status_bayar'], 'idx_finances_athlete_jenis_status');
        });

        // 4. Indexing tabel assessor_scores
        Schema::table('assessor_scores', function (Blueprint $table) {
            $table->index('periode_bulan', 'idx_assessor_scores_periode_bulan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('idx_attendances_athlete_tanggal');
            $table->dropIndex('idx_attendances_tanggal_status');
        });

        Schema::table('raports', function (Blueprint $table) {
            $table->dropIndex('idx_raports_periode');
            $table->dropIndex('idx_raports_publish_archived');
        });

        Schema::table('finances', function (Blueprint $table) {
            $table->dropIndex('idx_finances_tanggal');
            $table->dropIndex('idx_finances_jenis_status');
            $table->dropIndex('idx_finances_athlete_jenis_status');
        });

        Schema::table('assessor_scores', function (Blueprint $table) {
            $table->dropIndex('idx_assessor_scores_periode_bulan');
        });
    }
};
