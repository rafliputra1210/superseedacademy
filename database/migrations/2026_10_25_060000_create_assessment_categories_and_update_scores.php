<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Buat tabel kategori penilaian
        if (!Schema::hasTable('assessment_categories')) {
            Schema::create('assessment_categories', function (Blueprint $table) {
                $table->id();
                $table->string('nama'); // Nama Kategori: e.g. Teknik, Taktik, Fisik, Mental
                $table->text('deskripsi')->nullable(); // Keterangan aspek, e.g. "Dribble, passing, shooting"
                $table->integer('urutan')->default(0); // Urutan tampil
                $table->boolean('is_active')->default(true); // Status aktif/non-aktif
                $table->timestamps();
            });

            // Isi kategori bawaan awal agar data tidak kosong
            DB::table('assessment_categories')->insert([
                [
                    'nama' => 'Teknik',
                    'deskripsi' => 'Penguasaan bola, dribble, passing, ball control, dan shooting',
                    'urutan' => 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama' => 'Taktik',
                    'deskripsi' => 'Pemahaman posisi, visi bermain, transisi menyerang/bertahan, kerjasama tim',
                    'urutan' => 2,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama' => 'Fisik',
                    'deskripsi' => 'Stamina, kecepatan, daya tahan tubuh, akselerasi, dan kelincahan',
                    'urutan' => 3,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama' => 'Mental',
                    'deskripsi' => 'Disiplin, fokus, mental bertanding, sportivitas, dan komitmen latihan',
                    'urutan' => 4,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // 2. Tambah kolom JSON scores di assessor_scores untuk menyimpan nilai custom kategori
        if (Schema::hasTable('assessor_scores')) {
            Schema::table('assessor_scores', function (Blueprint $table) {
                if (!Schema::hasColumn('assessor_scores', 'scores')) {
                    $table->json('scores')->nullable()->after('assessor_id');
                }
            });
        }

        // 3. Tambah kolom JSON scores_summary di raports untuk rekap nilai final per kategori
        if (Schema::hasTable('raports')) {
            Schema::table('raports', function (Blueprint $table) {
                if (!Schema::hasColumn('raports', 'scores_summary')) {
                    $table->json('scores_summary')->nullable()->after('periode');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('raports')) {
            Schema::table('raports', function (Blueprint $table) {
                if (Schema::hasColumn('raports', 'scores_summary')) {
                    $table->dropColumn('scores_summary');
                }
            });
        }

        if (Schema::hasTable('assessor_scores')) {
            Schema::table('assessor_scores', function (Blueprint $table) {
                if (Schema::hasColumn('assessor_scores', 'scores')) {
                    $table->dropColumn('scores');
                }
            });
        }

        Schema::dropIfExists('assessment_categories');
    }
};
