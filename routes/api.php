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

Route::get('jadwalHarian/generate', 'App\Http\Controllers\Api\JadwalHarianController@generateJadwalHarian');
Route::get('jadwalHarian/index', 'App\Http\Controllers\Api\JadwalHarianController@index');
Route::get('jadwalHarian/indexToday', 'App\Http\Controllers\Api\JadwalHarianController@indexToday');
Route::put('jadwalHarian/libur/{id}', 'App\Http\Controllers\Api\JadwalHarianController@libur');
Route::get('jadwalHarian/{id}','Api\JadwalHarianController@show');
Route::put('jadwalHarian/gantiInstruktur/{id}', 'App\Http\Controllers\Api\JadwalHarianController@gantiInstruktur');
Route::get('jadwalHarian/indexToday/instruktur/{id}', 'App\Http\Controllers\Api\JadwalHarianController@indexTodayByInstruktur');

Route::get('aktivasi','Api\TransaksiAktivasiController@index');
Route::get('aktivasi/{id}','Api\TransaksiAktivasiController@show');
Route::post('aktivasi','Api\TransaksiAktivasiController@store');

Route::get('transaksiUang','Api\TransaksiUangController@index');
Route::get('transaksiUang/{id}','Api\TransaksiUangController@show');
Route::post('transaksiUang','Api\TransaksiUangController@store');

Route::get('transaksiKelas','Api\TransaksiKelasController@index');
Route::post('transaksiKelas','Api\TransaksiKelasController@store');
Route::get('transaksiKelas/{id}','Api\TransaksiKelasController@show');

Route::get('izinInstruktur','Api\IzinInstrukturController@index');
Route::get('izinInstruktur/not','Api\IzinInstrukturController@belumKonfirmasiIndex');
Route::put('izinInstruktur/konfirmasi/{id}', 'App\Http\Controllers\Api\IzinInstrukturController@konfirmasiIzin');
Route::get('izinInstruktur/show/{id}', 'App\Http\Controllers\Api\IzinInstrukturController@show');
Route::get('izinInstruktur/showByID/{id}', 'App\Http\Controllers\Api\IzinInstrukturController@showByID');
Route::post('izinInstruktur/ajukanIzin','Api\IzinInstrukturController@ajukanizin');

Route::get('kelas/{id}','Api\KelasController@show');
Route::get('depositKelas/{id}','Api\DepositKelasController@show');
Route::get('depositKelas/profile/{id}','Api\DepositKelasController@profile');

Route::put('instruktur/ubahPW/{id}','Api\InstrukturController@ubahPassword');
Route::put('pegawai/ubahPW/{id}','Api\PegawaiController@ubahPassword');
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
    Route::put('user/expired/deaktivasi','Api\UserController@deaktivasi');
    Route::get('user/index/expired','Api\UserController@expiredMemberIndex');
    Route::get('user/index/depoexpired','Api\UserController@expiredDepoKelasIndex');
    Route::put('user/expired/deaktivasiDepoKelas','Api\UserController@deaktivasiDepoKelas');

    Route::post('bookingKelas','Api\BookingKelasController@store');
    Route::get('bookingKelas/show/{id}','Api\BookingKelasController@show');
    Route::get('bookingKelas/history/{id}','Api\BookingKelasController@historyKelas');
    Route::get('bookingKelas/showByID/{id}','Api\BookingKelasController@showByID');
    Route::post('bookingKelas/cancel/{id}','Api\BookingKelasController@cancel');
    Route::get('bookingKelas/showKasir/{id}','Api\BookingKelasController@showKasir');

    Route::post('bookingGym','Api\BookingGymController@store');
    Route::get('bookingGym/show/{id}','Api\BookingGymController@show');
    Route::get('bookingGym/history/{id}','Api\BookingGymController@historyGym');
    Route::post('bookingGym/cancel/{id}','Api\BookingGymController@cancel');
    Route::get('sesi', 'Api\SesiGymController@index');
    Route::get('sesi/{id}', 'Api\SesiGymController@show');
    Route::get('bookingGym','Api\BookingGymController@index');
    Route::get('bookingGym/showByID/{id}','Api\BookingGymController@showByID');
    Route::put('bookingGym/presensi/{id}','Api\BookingGymController@presensi');

    Route::post('presensiInstruktur/jamMulai/{id}','Api\PresensiInstrukturController@updateJamMulai');
    Route::put('presensiInstruktur/jamSelesai/{id}','Api\PresensiInstrukturController@updateJamSelesai');
    Route::get('presensiInstruktur/history/{id}','Api\PresensiInstrukturController@history');

    Route::get('presensiKelas/showToday/{id}','Api\PresensiKelasController@showToday');
    Route::get('presensiKelas/show','Api\PresensiKelasController@show');
    Route::put('presensiKelas/presensiHadir/{id}','Api\PresensiKelasController@presensiHadir');
    Route::put('presensiKelas/presensiTidakHadir/{id}','Api\PresensiKelasController@presensiTidakHadir');

    Route::get('jumlahInstruktur','Api\LaporanController@hitungKehadiranDanKetidakhadiran');
    Route::get('laporanGym','Api\LaporanController@laporanGym');
    Route::get('laporanKelas','Api\LaporanController@laporanKelas');
    Route::get('penghitunganTotal','Api\LaporanController@penghitunganTotal');
    Route::get('totalPendapatan','Api\LaporanController@totalPendapatan');

Route::group(['middleware' => ['auth:pegawai']], function() {
    
    

    

});
    

