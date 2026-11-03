<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Menambahkan kolom ke tabel absensi siswa
        Schema::table('attendances', function (Blueprint $table) {
            $table->time('waktu_pulang')->nullable()->after('status');
            $table->string('photo_pulang_path')->nullable()->after('waktu_pulang');
        });

        // Menambahkan kolom ke tabel absensi guru
        Schema::table('absensi_gurus', function (Blueprint $table) {
            $table->time('waktu_pulang')->nullable()->after('status');
            $table->string('photo_pulang_path')->nullable()->after('waktu_pulang');
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['waktu_pulang', 'photo_pulang_path']);
        });

        Schema::table('absensi_gurus', function (Blueprint $table) {
            $table->dropColumn(['waktu_pulang', 'photo_pulang_path']);
        });
    }
};