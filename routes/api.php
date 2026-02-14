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

    Route::prefix('v1')->group(function () {
        Route::resources([
            'scores' => ScoreControllerV1::class,
        ]);
    });

});

//Route::middleware('with_hoppy_api_key')->prefix('/v0')->group(function () {
//    Route::resources([
//        'scores' => ScoreControllerV0::class,
//    ]);
//});

//Route::middleware('with_hoppy_api_key')->prefix('/v1')->group(function () {
//    Route::resources([
//        'scores' => ScoreControllerV1::class,
//    ]);
//});
