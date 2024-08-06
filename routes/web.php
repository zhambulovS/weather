<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [WeatherController::class, 'index'])->name('weather');
Route::get('/month', [WeatherController::class, 'showMonthlyWeather'])->name('weatherForMonth');
Route::get('/404', [WeatherController::class, 'notFound'])->name('weather.notFound');
Route::get('/weather-for-month', [WeatherController::class, 'view'])->name('downloadPDF');
