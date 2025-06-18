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
        Schema::create('work_divisions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('detail_id');

            $table->unsignedBigInteger('division_id');
            $table->foreign('detail_id')->references('id')->on('details')->onDelete('cascade');
            $table->foreign('division_id')->references('id')->on('divisions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_divisions');
    }
};