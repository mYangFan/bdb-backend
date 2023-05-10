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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [\App\Http\Controllers\Api\LoginController::class, 'index']);

Route::middleware('jwt.api.auth')->group(function () {
    Route::post('authUser', [\App\Http\Controllers\Api\LoginController::class, 'authUser']);
    Route::post('finishGuide', [\App\Http\Controllers\Api\FinishGuideController::class, 'index']);
    Route::post('startLevel', [\App\Http\Controllers\Api\GameStartController::class, 'index']);
    Route::post('endLevel', [\App\Http\Controllers\Api\GameOverController::class, 'index']);
    Route::post('addItem', [\App\Http\Controllers\Api\AddItemController::class, 'index']);
    Route::post('getReward', [\App\Http\Controllers\Api\GetRewardController::class, 'index']);
});


