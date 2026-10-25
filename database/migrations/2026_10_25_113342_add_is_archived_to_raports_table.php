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
            if (!Schema::hasColumn('raports', 'is_archived')) {
                $table->boolean('is_archived')->default(false)->after('status_publish');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raports', function (Blueprint $table) {
            if (Schema::hasColumn('raports', 'is_archived')) {
                $table->dropColumn('is_archived');
            }
        });
    }
};
