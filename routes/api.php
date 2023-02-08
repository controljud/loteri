<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

#Autenticação
Route::post('/login', 'App\Http\Controllers\Api\LoginController@login');
Route::post('/cadastro', 'App\Http\Controllers\Api\LoginController@cadastro');

Route::group(['middleware' => ['jwt.auth']], function () {
    Route::group(['prefix' => 'jogo'], function() {
        Route::post('/', 'App\Http\Controllers\Api\JogoController@postJogo');
        Route::get('/jogos', 'App\Http\Controllers\Api\JogoController@getJogos');
        Route::get('/quantidade', 'App\Http\Controllers\Api\JogoController@getQuantidadeJogos');
        Route::get('/ultimo/{id_jogo}', 'App\Http\Controllers\Api\JogoController@getUltimoJogo');


        Route::put('/sorteios', 'App\Http\Controllers\Api\JogoController@putJogo');
        Route::get('/sorteios/{id_jogo}', 'App\Http\Controllers\Api\JogoController@getSorteios');
        Route::get('/sorteio/atual', 'App\Http\Controllers\Api\JogoController@getSorteioAtual');
        Route::get('/sorteio/quantidade', 'App\Http\Controllers\Api\JogoController@getQuantidadeSorteios');
        Route::post('/sorteio', 'App\Http\Controllers\Api\JogoController@postSorteio');

        Route::put('/totais', 'App\Http\Controllers\Api\JogoController@putTotais');
        Route::get('/totais/{id_jogo}', 'App\Http\Controllers\Api\JogoController@getTotais');
    });

    Route::group(['prefix' => 'aposta'], function() {
        Route::post('/', 'App\Http\Controllers\Api\ApostaController@postAposta');
        Route::get('/quantidade', 'App\Http\Controllers\Api\ApostaController@getQuantidadeApostas');
        Route::delete('/{id}', 'App\Http\Controllers\Api\ApostaController@deleteAposta');

        Route::get('/apostas/{filter}', 'App\Http\Controllers\Api\ApostaController@getApostas');
    });

    Route::group(['prefix' => 'usuario'], function() {
        Route::get('/quantidade', 'App\Http\Controllers\Api\LoginController@getQuantidadeUsuarios');
    });
});
