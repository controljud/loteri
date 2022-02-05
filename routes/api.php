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
});