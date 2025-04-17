<?php

use App\Http\Controllers\SensorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post("/login",[SensorController::class,"loginService"]);
Route::get("/deviceSetting",[SensorController::class,"deviceSetting"]);
Route::get("/apiSetting",[SensorController::class,"apiSetting"]);
Route::get("/emailSetting",[SensorController::class,"emailSetting"]);
Route::get("/ambilParameterList",[SensorController::class,"ambilParameterList"]);

Route::get("/saveSetting",[SensorController::class,"saveSetting"]);
Route::get("/setting",[SensorController::class,"ambilSetting"]);
Route::get("/ambilPort",[SensorController::class,"ambilPort"]);
Route::get("/ambilParameter",[SensorController::class,"ambilParameter"]);
Route::get("/ambilSensor",[SensorController::class,"ambilSensor"]);
Route::get("/saveSensor",[SensorController::class,"saveSensor"]);
Route::get("/ambilParameterEdit",[SensorController::class,"ambilParameterEdit"]);
Route::get("/editSensor",[SensorController::class,"editSensor"]);
Route::put("/updateSensor/{id}",[SensorController::class,"updateSensor"]);
Route::delete("/deleteSensor/{id}",[SensorController::class,"deleteSensor"]);
Route::get("/saveRainSensor",[SensorController::class,"saveRainSensor"]);
Route::get("/ambilRainSensor",[SensorController::class,"ambilRainSensor"]);
Route::get("/manualReadingSensor",[SensorController::class,"manualReadingSensor"]);

//konfigurasi jaringan
Route::get("/cekDevice",[SensorController::class,"cekDevice"]);
Route::get("/cekKoneksi",[SensorController::class,"cekKoneksi"]);
Route::get("/ambilWifi",[SensorController::class,"ambilWifi"]);
Route::get("/konekWifi",[SensorController::class,"konekWifi"]);
Route::get("/detailConn",[SensorController::class,"detailConn"]);
