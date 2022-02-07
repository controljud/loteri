<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLtTotais extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lt_totais', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_jogo');
            $table->string('totais');
            $table->timestamps();

            $table->foreign('id_jogo')->references('id')->on('lt_jogos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lt_totais');
    }
}
