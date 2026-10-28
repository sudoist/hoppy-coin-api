<?php

use App\Http\Controllers\v0\ScoreController as ScoreControllerV0;
use App\Http\Controllers\v1\ScoreController as ScoreControllerV1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Default
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public arcade leaderboards need no browser-embedded API secret.
// Keep score writes protected until server-issued run validation is implemented.
Route::get('v1/scores', [ScoreControllerV1::class, 'index'])->middleware('throttle:60,1');

Route::middleware('with_hoppy_api_key')->group(function () {

    Route::prefix('v0')->group(function () {
        Route::get('scores', [ScoreControllerV0::class, 'index']);
        Route::post('scores', [ScoreControllerV0::class, 'store']);
    });

    Route::prefix('v1')->group(function () {
        Route::post('scores', [ScoreControllerV1::class, 'store']);
    });

});
