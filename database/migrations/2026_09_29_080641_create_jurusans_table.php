<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('jurusans', function (Blueprint $table) {
        $table->id();
        $table->string('kode_jurusan')->unique(); // Contoh: RPL
        $table->string('nama_jurusan');           // Contoh: Rekayasa Perangkat Lunak
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurusans');
    }
};
