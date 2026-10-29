<?php

use App\Http\Controllers\v0\ScoreController as ScoreControllerV0;
use App\Http\Controllers\v1\ScoreController as ScoreControllerV1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Default
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public guest score flow: server-issued, one-use run tokens.
Route::get('v1/scores', [ScoreControllerV1::class, 'index'])->middleware('throttle:60,1');
Route::post('v1/runs', [ScoreControllerV1::class, 'startRun'])->middleware('throttle:30,1');
Route::post('v1/scores', [ScoreControllerV1::class, 'storeGuest'])->middleware('throttle:20,1');

Route::middleware('with_hoppy_api_key')->group(function () {

    Route::prefix('v0')->group(function () {
        Route::get('scores', [ScoreControllerV0::class, 'index']);
        Route::post('scores', [ScoreControllerV0::class, 'store']);
    });

    Route::prefix('v1')->group(function () {
    });

});
