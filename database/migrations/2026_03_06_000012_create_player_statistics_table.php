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
        Schema::create('player_statistics', function (Blueprint $table) {
          $table->id();

          $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();

            $table->foreignId('statistics_id')
            ->constrained('statistics')
            ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_statistics');
    }
};