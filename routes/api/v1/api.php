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
Route::get('/accessFail', 'App\Http\Controllers\LoginController@accessFail');
Route::prefix('/user')->group(function(){
    Route::post('/login', 'App\Http\Controllers\LoginController@login');
    Route::post('/updatePassword','App\Http\Controllers\LoginController@updatePassword');
    Route::middleware('auth:api')->get('/all','App\Http\Controllers\UserController@all');
    Route::middleware('auth:api')->get('/informacion','App\Http\Controllers\UserController@informacion');
    Route::middleware('auth:api')->post('/register','App\Http\Controllers\UserController@register');
    Route::middleware('auth:api')->post('/activo','App\Http\Controllers\UserController@activo');
});

Route::prefix('/usuarios')->group(function(){
    Route::middleware('auth:api')->get('/buscar','App\Http\Controllers\UserController@buscarUsuario');
    Route::middleware('auth:api')->get('/informacion','App\Http\Controllers\UserController@informacion');
    Route::middleware('auth:api')->post('/guardar','App\Http\Controllers\UserController@guardar');
    Route::middleware('auth:api')->post('/reiniciar-passsword','App\Http\Controllers\UserController@reiniciarPasssword');
});

Route::prefix('/menu')->group(function(){
    Route::middleware('auth:api')->post('/add','App\Http\Controllers\MenuController@add');
    Route::middleware('auth:api')->get('/completo','App\Http\Controllers\MenuController@menu');
    Route::middleware('auth:api')->post('/activo','App\Http\Controllers\UserController@activo');
});
Route::prefix('/cp')->group(function(){
    Route::middleware('auth:api')->get('/search','App\Http\Controllers\CodigosPostalesController@dameCP');
});
