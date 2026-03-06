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
           // Primary Key and Fields for Tournament
        $table->id('tournamentID'); 
        $table->string('tournamentName');
        $table->string('certificateType')->nullable();
        $table->string('extractedType')->nullable();
        $table->string('rank')->nullable();
        $table->unsignedBigInteger('playerID'); // Relationship with Player
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
