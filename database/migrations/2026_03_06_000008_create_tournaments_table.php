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
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->enum('rank', ['1st', '2ND', '3RD']);

            $table->enum('certificateType', [
                'Participation Certificate',
                'Achievement Certificate'
                ]);
            $table->date('tournamentdate');
            $table->string('tournamentName');
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
