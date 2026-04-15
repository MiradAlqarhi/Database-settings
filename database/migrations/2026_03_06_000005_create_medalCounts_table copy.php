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
        Schema::create('medalCounts', function (Blueprint $table) {
           $table->id();
           $table->integer('silverMedal')->default(0);
           $table->integer('BronzeMedal')->default(0);
           $table->integer('GoldMedal')->default(0);
           $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
           $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medalCounts');
    }
};
