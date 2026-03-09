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
         Schema::create('tournament_calender', function (Blueprint $table) {
        $table->id();
        $table->string('tournamentName');
        $table->string('tournamentLinkURL');
        $table->dateTime('tournamentStartTime');
        $table->dateTime('upcomingTournamentDate');

        // اذا دمجنا
        // $table->unsignedBigInteger('PlayerID');

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_calender');
    }
};
