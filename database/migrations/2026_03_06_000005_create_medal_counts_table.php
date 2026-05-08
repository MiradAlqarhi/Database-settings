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
        Schema::create('medal_counts', function (Blueprint $table) {
           $table->id();
           $table->integer('silver')->default(0);
           $table->integer('bronze')->default(0);
           $table->integer('gold')->default(0);
           $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();

           $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medal_counts');
    }
};
