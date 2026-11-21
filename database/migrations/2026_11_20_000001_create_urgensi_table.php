<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Membuat tabel master tingkat urgensi (urgensi) sebagai shared schema contract
     * antara PBI-07 (Pencatatan Permintaan Darurat Faskes) dan PBI-11 (Manajemen Master Tingkat Urgensi).
     *
     * CATATAN INTEGRASI PBI-11:
     * PBI-11 mewarisi tabel dan struktur ini secara langsung. PBI-11 TIDAK BOLEH membuat
     * file migrasi baru untuk membuat ulang tabel `urgensi`.
     */
    public function up(): void
    {
        Schema::create('urgensi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_level', 50)->unique();
            $table->unsignedInteger('batas_waktu_jam');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('urgensi');
    }
};
