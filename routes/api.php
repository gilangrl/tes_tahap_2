<?php

use App\Http\Controllers\Api\BankController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\RekeningController;
use App\Http\Controllers\Api\TransaksiController;

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


Route::post('/register', RegisterController::class)->name('register');
Route::post('/login', LoginController::class)->name('login');
Route::post('/logout', LogoutController::class)->name('logout');
Route::post('/update-token', [LoginController::class, 'updateToken']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/transfer', TransaksiController::class);
    Route::post('/bank', BankController::class);
    Route::post('/rekening', RekeningController::class);
});