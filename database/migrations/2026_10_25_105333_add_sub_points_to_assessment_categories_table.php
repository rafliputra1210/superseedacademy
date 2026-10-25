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
        Schema::table('assessment_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('assessment_categories', 'sub_points')) {
                $table->json('sub_points')->nullable()->after('deskripsi');
            }
        });

        // Seed initial sub-points for existing default categories
        $defaultSubPoints = [
            'Teknik' => [
                'Kontrol bola (Ball Control)',
                'Akurasi passing dan first touch',
                'Kecepatan dribbling dan arah gerak',
                'Teknik shooting dan finishing',
                'Kemampuan bertahan (defending)',
                'Kemampuan intercept dan membaca bola',
            ],
            'Taktik' => [
                'Posisi saat menyerang dan bertahan',
                'Pemahaman transisi permainan',
                'Kemampuan rotasi dan membuka ruang',
                'Komunikasi dengan rekan tim',
                'Pengambilan keputusan di bawah tekanan',
            ],
            'Fisik' => [
                'Daya tahan dan stamina',
                'Kecepatan reaksi dan kelincahan',
                'Akselerasi dan kekuatan fisik',
            ],
            'Mental' => [
                'Disiplin mengikuti instruksi pelatih',
                'Fokus dan konsentrasi',
                'Respon terhadap tekanan dan situasi sulit',
                'Semangat kerja sama dan motivasi tim',
            ],
        ];

        foreach ($defaultSubPoints as $nama => $subs) {
            \Illuminate\Support\Facades\DB::table('assessment_categories')
                ->where('nama', $nama)
                ->whereNull('sub_points')
                ->update(['sub_points' => json_encode($subs)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_categories', function (Blueprint $table) {
            if (Schema::hasColumn('assessment_categories', 'sub_points')) {
                $table->dropColumn('sub_points');
            }
        });
    }
};
