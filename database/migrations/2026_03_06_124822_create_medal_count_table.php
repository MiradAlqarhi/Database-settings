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
        Schema::create('medalCount', function (Blueprint $table) {
            $table->id('medalID');
             $table->integer('gold');
            $table->integer('silver');
            $table->integer('bronze');

            $table->unsignedBigInteger('playerID');

            $table->foreign('playerID')
                ->references('PlayerID')
                ->on('player')
                ->onDelete('cascade');



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medal_count');
    }
};
