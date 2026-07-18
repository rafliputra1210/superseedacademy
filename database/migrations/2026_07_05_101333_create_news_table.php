<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique(); 
            $table->string('kategori', 100)->default('Turnamen'); // Turnamen, Tips Latihan, Info Akademi
            $table->longText('konten');
            $table->string('foto')->nullable(); // Foto utama liputan berita
            $table->date('tanggal');
            $table->boolean('is_active')->default(true); // Status tayang
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};