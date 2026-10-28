<?php

use App\Http\Controllers\v0\ScoreController as ScoreControllerV0;
use App\Http\Controllers\v1\ScoreController as ScoreControllerV1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Default
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('with_hoppy_api_key')->group(function () {

    Route::prefix('v0')->group(function () {
        Route::get('scores', [ScoreControllerV0::class, 'index']);
        Route::post('scores', [ScoreControllerV0::class, 'store']);
    });

    Route::prefix('v1')->group(function () {
        Route::get('scores', [ScoreControllerV1::class, 'index']);
        Route::post('scores', [ScoreControllerV1::class, 'store']);
    });

});
