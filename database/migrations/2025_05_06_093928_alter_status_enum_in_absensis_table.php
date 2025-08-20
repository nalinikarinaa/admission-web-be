<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah ENUM jadi tambah 'pending'
        DB::statement("ALTER TABLE absensis MODIFY status ENUM('hadir', 'tidak_hadir', 'izin', 'pending') DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Kembalikan ENUM ke kondisi awal (tanpa 'pending')
        DB::statement("ALTER TABLE absensis MODIFY status ENUM('hadir', 'tidak_hadir', 'izin')");
    }
};
