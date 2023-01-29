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
        Route::put('/', 'App\Http\Controllers\Api\JogoController@putJogo');
        Route::get('/jogos', 'App\Http\Controllers\Api\JogoController@getJogos');
        
        Route::get('/sorteio/atual', 'App\Http\Controllers\Api\JogoController@getSorteioAtual');
        Route::post('/sorteio', 'App\Http\Controllers\Api\JogoController@postSorteio');
        // Route::get('/ultimo/{id_jogo}', 'App\Http\Controllers\Api\JogoController@getUltimoJogo');

        Route::put('/totais', 'App\Http\Controllers\Api\JogoController@putTotais');
        Route::get('/totais/{id_jogo}', 'App\Http\Controllers\Api\JogoController@getTotais');
    });

    Route::group(['prefix' => 'aposta'], function() {
        Route::post('/', 'App\Http\Controllers\Api\ApostaController@postAposta');
        Route::delete('/{id}', 'App\Http\Controllers\Api\ApostaController@deleteAposta');
        
        Route::get('/apostas/{filter}', 'App\Http\Controllers\Api\ApostaController@getApostas');
    });
});