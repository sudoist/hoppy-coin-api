<?php

use App\Http\Controllers\ScoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Default
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Phaser Tutorial API
Route::prefix('/v0')->group(function () {
    Route::resources([
        'scores' => ScoreController::class,
    ]);
});
