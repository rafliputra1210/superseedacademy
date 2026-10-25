<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Sesuaikan daftar ENUM dengan role yang sudah ada di aplikasi Anda
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'wali_murid', 'asesor') NOT NULL DEFAULT 'wali_murid'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'wali_murid') NOT NULL DEFAULT 'wali_murid'");
    }
};