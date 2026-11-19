<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel wilayah sebagai pengelompokan geografis Fasilitas Kesehatan.
     * Sesuai PBI-01: Manajemen Data Wilayah / Zona Faskes.
     */
    public function up(): void
    {
        Schema::create('wilayah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_wilayah')->unique(); // Nama unik wilayah/zona faskes
            $table->text('deskripsi')->nullable();    // Keterangan opsional wilayah
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wilayah');
    }
};
