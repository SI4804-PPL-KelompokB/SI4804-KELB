<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * PBI-21: Manajemen Pesan Internal / Inbox Faskes (F22)
     */
    public function up(): void
    {
        Schema::create('inbox', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengirim_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('penerima_id')->constrained('users')->cascadeOnDelete();
            $table->string('subjek', 200);
            $table->text('isi');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('deleted_by_pengirim')->default(false);
            $table->boolean('deleted_by_penerima')->default(false);
            $table->timestamps();

            $table->index(['penerima_id', 'is_read']);
            $table->index(['pengirim_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inbox');
    }
};
