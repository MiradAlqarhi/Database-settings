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
        Schema::create('tournamentCalender', function (Blueprint $table) {
           // Primary Key and Fields for Tournament
           $table->id();
           $table->string('tournamentName');
           $table->string('tournamentLinkURL')->nullable();
           $table->dateTime('tournamentStartTime')->nullable();
           $table->date('upcomingTournamentDate');
           $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
           $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournamentCalender');
    }
};
