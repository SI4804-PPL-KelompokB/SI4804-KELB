<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Membuat tabel header permintaan darurat obat oleh Fasilitas Kesehatan (PBI-07).
     *
     * CATATAN INTEGRASI LINTAS PBI:
     * - PBI-08 (Item Permintaan): Foreign key `permintaan_item.permintaan_id` ke `permintaan.id`
     *   direkomendasikan menggunakan `cascadeOnDelete()` agar item anak ikut terhapus saat header
     *   permintaan berstatus `diajukan` dihapus (tunduk pada konfirmasi aturan bisnis tim).
     * - PBI-15 (Surat Jalan Pengiriman): Kolom `pengiriman_id` ditangguhkan (deferred) dari migrasi
     *   awal PBI-07 dan akan ditambahkan oleh PBI-15 setelah tabel `pengiriman` tersedia.
     */
    public function up(): void
    {
        Schema::create('permintaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faskes_id')->constrained('faskes')->restrictOnDelete();
            $table->foreignId('urgensi_id')->constrained('urgensi')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('status', 30)->default('diajukan')->index();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan');
    }
};
