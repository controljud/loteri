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
    Route::group(['prefix' => 'mega'], function() {
        Route::get('/sorteados', 'App\Http\Controllers\Api\MegaSenaController@getDadosSorteados');
        
        Route::put('/update', 'App\Http\Controllers\Api\MegaSenaController@putUpdate');
    });

    Route::post('/jogo', 'App\Http\Controllers\Api\LoteriaController@postJogo');
    Route::get('/jogos', 'App\Http\Controllers\Api\LoteriaController@getJogos');
    Route::get('/ultimo/jogo/{id_jogo}', 'App\Http\Controllers\Api\LoteriaController@getUltimoJogo');
    Route::put('/totais', 'App\Http\Controllers\Api\LoteriaController@putTotais');
    Route::get('/totais/{id_jogo}', 'App\Http\Controllers\Api\LoteriaController@getTotais');

    Route::post('/aposta', 'App\Http\Controllers\Api\LoteriaController@postAposta');
    Route::get('/apostas/{id_user}', 'App\Http\Controllers\Api\LoteriaController@getApostas');
    Route::get('/sorteio/atual', 'App\Http\Controllers\Api\LoteriaController@getSorteioAtual');
    Route::post('/sorteio', 'App\Http\Controllers\Api\LoteriaController@postSorteio');
});