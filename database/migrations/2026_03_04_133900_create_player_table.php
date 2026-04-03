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
        Schema::create('player', function (Blueprint $table) {
        $table->id('playerID');
        $table->integer('age')->unsigned();
        $table->string('gender')->nullable();
        $table->string('game')->nullable();
        $table->unsignedBigInteger('userID');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player');
    }
};