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
    Route::post('updateUser/{id}', [\App\Http\Controllers\Admin\UsersController::class, 'updateUser']);
    Route::post('deleteUser/{id}', [\App\Http\Controllers\Admin\UsersController::class, 'deleteUser']);
    Route::post('modifyPassword', [\App\Http\Controllers\Admin\UsersController::class, 'modifyPassword']);

    Route::get('roles', [\App\Http\Controllers\Admin\RolesController::class, 'index']);
    Route::post('addRole', [\App\Http\Controllers\Admin\RolesController::class, 'addRole']);
    Route::post('deleteRole/{id}', [\App\Http\Controllers\Admin\RolesController::class, 'deleteRole']);
    Route::post("authRole/{id}", [\App\Http\Controllers\Admin\RolesController::class, 'authRole']);
    Route::post("updateRole/{id}", [\App\Http\Controllers\Admin\RolesController::class, 'updateRole']);

    Route::get('menuTree', [\App\Http\Controllers\Admin\MenusController::class, 'menuTree']);

    Route::post('setRewardArea', [\App\Http\Controllers\Admin\RewardsController::class, 'setRewardArea']);
    Route::get('rewardAreas', [\App\Http\Controllers\Admin\RewardsController::class, 'getRewardAreas']);
    Route::post('updateRewardArea/{id}', [\App\Http\Controllers\Admin\RewardsController::class, 'updateRewardArea']);
    Route::post("deleteRewardArea/{id}", [\App\Http\Controllers\Admin\RewardsController::class, 'deleteRewardArea']);

    Route::get('customers', [\App\Http\Controllers\Admin\CustomerController::class, 'index']);

    Route::get('statReward', [\App\Http\Controllers\Admin\RewardsController::class, 'statReward']);

    Route::post("verifyReward", [\App\Http\Controllers\Admin\RewardsController::class, 'fulReward']);
});

Route::post('fulReward', [\App\Http\Controllers\Admin\RewardsController::class, 'fulReward']);
