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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('template_id');
            $table->unsignedBigInteger('target_user_id')->nullable();
            $table->string('document_number')->nullable();
            $table->string('file_path')->nullable(); 
            $table->integer('lama_hari')->nullable(); // Tambahkan kolom lama_hari
            $table->string('alasan')->nullable();
            $table->timestamp('tanggal_pengajuan');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->foreign('target_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('templates')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};