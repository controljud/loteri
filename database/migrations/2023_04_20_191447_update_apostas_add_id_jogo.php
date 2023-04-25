<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateApostasAddIdJogo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('lt_apostas', function($table) {
            $table->unsignedBigInteger('id_jogo')->after('id');

            $table->foreign('id_jogo')->references('id')->on('lt_jogos');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('lt_apostas', function($table) {
            $table->dropColumn('id_jogo');
        });
        Schema::enableForeignKeyConstraints();
    }
}
