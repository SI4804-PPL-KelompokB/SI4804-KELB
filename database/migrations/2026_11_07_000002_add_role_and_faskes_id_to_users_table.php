<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('faskes_id')->nullable()->after('id')->constrained('faskes')->nullOnDelete();
            $table->string('role', 30)->default('faskes')->after('email'); // admin, dinkes, faskes, penyedia
            $table->string('jabatan')->nullable()->after('name');
            $table->boolean('is_active')->default(true)->after('password');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['faskes_id']);
            $table->dropColumn(['faskes_id', 'role', 'jabatan', 'is_active', 'last_login_at']);
        });
    }
};
