<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComprasController;
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
Route::post("compras", "App\Http\Controllers\ComprasController@store");
Route::get("compras", "App\Http\Controllers\ComprasController@index");
Route::get("compras/{id}", "App\Http\Controllers\ComprasController@show");
