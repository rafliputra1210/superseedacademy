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
        Schema::create('assessment_periods', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->string('tipe')->default('semester'); // 'semester', 'bulanan', 'kustom'
            $table->string('tahun_ajaran')->nullable();  // e.g. '2026/2027'
            $table->boolean('is_active')->default(false);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Seed periode awal: Semester Ganjil & Genap serta periode berjalan
        $currentYear = date('Y');
        $nextYear = (int)$currentYear + 1;
        $tahunAjaran = "{$currentYear}/{$nextYear}";

        \Illuminate\Support\Facades\DB::table('assessment_periods')->insert([
            [
                'nama'         => "Semester Ganjil {$tahunAjaran}",
                'tipe'         => 'semester',
                'tahun_ajaran' => $tahunAjaran,
                'is_active'    => true,
                'keterangan'   => 'Periode Evaluasi Semester Ganjil',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nama'         => "Semester Genap {$tahunAjaran}",
                'tipe'         => 'semester',
                'tahun_ajaran' => $tahunAjaran,
                'is_active'    => false,
                'keterangan'   => 'Periode Evaluasi Semester Genap',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nama'         => 'Oktober 2026',
                'tipe'         => 'bulanan',
                'tahun_ajaran' => $tahunAjaran,
                'is_active'    => false,
                'keterangan'   => 'Periode Bulanan Oktober 2026',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_periods');
    }
};
