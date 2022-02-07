<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLtSorteios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lt_sorteios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_jogo');
            $table->integer('numero');
            $table->string('dezenas');
            $table->date('data');
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
        Schema::dropIfExists('lt_sorteios');
    }
}
