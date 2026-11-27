<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permintaan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permintaan_id')
                ->constrained('permintaan')
                ->cascadeOnDelete();
            $table->string('nama_obat', 150);
            $table->string('satuan', 50)->default('Box');
            $table->unsignedInteger('qty');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_items');
    }
};
