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
            // Menambahkan kolom role_id di tabel users
            $table->unsignedBigInteger('role_id')->nullable();
            // Menambahkan foreign key constraint
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus foreign key dan kolom role_id saat rollback
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};