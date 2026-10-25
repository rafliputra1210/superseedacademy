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
        Schema::create('raports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained('athletes')->cascadeOnDelete();
            $table->string('periode'); // Contoh: "Oktober 2026"
            $table->decimal('nilai_teknik_final', 5, 2)->default(0);
            $table->decimal('nilai_taktik_final', 5, 2)->default(0);
            $table->decimal('nilai_fisik_final', 5, 2)->default(0);
            $table->decimal('nilai_mental_final', 5, 2)->default(0);
            $table->text('catatan_pelatih')->nullable();
            $table->string('status_publish')->default('Published');
            $table->timestamps();

            $table->unique(['athlete_id', 'periode'], 'unik_raport_atlet_periode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raports');
    }
};
