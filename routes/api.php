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

Route::post('loginWeb', 'Api\AuthController@loginWeb');
Route::post('loginMobile', 'Api\AuthController@loginMobile');
Route::get('jadwalUmum/index', 'Api\JadwalUmumController@index');
Route::get('kelas', 'Api\KelasController@index');
Route::get('instruktur','Api\InstrukturController@index');
Route::post('jadwalUmum/add', 'Api\JadwalUmumController@add');
Route::put('jadwalUmum/update/{id}', 'Api\JadwalUmumController@update');
Route::delete('jadwalUmum/{id}', 'Api\JadwalUmumController@delete');
Route::get('jadwalUmum/{id}','Api\JadwalUmumController@show');

Route::group(['middleware' => ['auth:pegawai']], function() {
    Route::get('pegawai','Api\PegawaiController@index');
    Route::get('pegawai/{id}','Api\PegawaiController@show');
    Route::post('pegawai','Api\PegawaiController@store');
    Route::put('pegawai/{id}','Api\PegawaiController@update');

    
    Route::get('instruktur/{id}','Api\InstrukturController@show');
    Route::post('instruktur','Api\InstrukturController@store');
    Route::put('instruktur/{id}','Api\InstrukturController@update');
    Route::delete('instruktur/{id}','Api\InstrukturController@destroy');

    Route::get('user','Api\UserController@index');
    Route::get('user/{id}','Api\UserController@show');
    Route::post('user','Api\UserController@store');
    Route::put('user/{id}','Api\UserController@update');
    Route::delete('user/{id}','Api\UserController@destroy');
    Route::put('resetPW/{id}','Api\UserController@resetPassword');

    

    

});
    

