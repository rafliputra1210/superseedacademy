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
        Schema::table('raports', function (Blueprint $table) {
            $table->decimal('rekap_skor_100', 5, 2)->default(0)->after('catatan_pelatih');
            $table->decimal('rekap_rata_rata', 5, 2)->default(0)->after('rekap_skor_100');
            $table->json('observasi_kualitatif')->nullable()->after('rekap_rata_rata');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raports', function (Blueprint $table) {
            $table->dropColumn(['rekap_skor_100', 'rekap_rata_rata', 'observasi_kualitatif']);
        });
    }
};
