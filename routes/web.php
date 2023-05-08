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
    Route::get('loginInfo', [\App\Http\Controllers\Admin\InfoController::class, 'index']);
    Route::post('logout', [\App\Http\Controllers\Admin\LoginController::class, 'logout']);

    Route::get('users', [\App\Http\Controllers\Admin\UsersController::class, 'index']);
    Route::post('addUser', [\App\Http\Controllers\Admin\UsersController::class, 'createUser']);
    Route::post('updateUser', [\App\Http\Controllers\Admin\UsersController::class, 'updateUser']);
    Route::delete('deleteUser/{id}', [\App\Http\Controllers\Admin\UsersController::class, 'deleteUser']);
    Route::post('modifyPassword', [\App\Http\Controllers\Admin\UsersController::class, 'modifyPassword']);

    Route::get('roles', [\App\Http\Controllers\Admin\RolesController::class, 'index']);
    Route::post('addRole', [\App\Http\Controllers\Admin\RolesController::class, 'addRole']);
    Route::delete('deleteRole/{id}', [\App\Http\Controllers\Admin\RolesController::class, 'deleteRole']);

    Route::get('menuTree', [\App\Http\Controllers\Admin\MenusController::class, 'menuTree']);
});
