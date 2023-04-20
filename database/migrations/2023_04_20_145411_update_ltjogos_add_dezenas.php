<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLtjogosAddDezenas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lt_jogos', function($table) {
            $table->integer('quantidade_dezenas')->after('jogo')->nullable();
            $table->integer('quantidade_acertos')->after('quantidade_dezenas')->nullable();
            $table->string('imagem')->after('quantidade_acertos')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lt_jogos', function($table) {
            $table->dropColumn('quantidade_dezenas');
            $table->dropColumn('quantidade_acertos');
            $table->dropColumn('imagem');
        });
    }
}
