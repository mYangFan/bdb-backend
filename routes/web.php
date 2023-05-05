<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('login', [\App\Http\Controllers\Admin\LoginController::class, 'index']);

Route::middleware('jwt.auth')->group(function () {
    Route::get('/users', [\App\Http\Controllers\Admin\UsersController::class, 'index']);
    Route::get('loginInfo', [\App\Http\Controllers\Admin\InfoController::class, 'index']);
});
