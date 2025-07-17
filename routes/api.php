<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProvinsiController;

Route::prefix('provinsi')->group(function () {
    Route::get('/', [ProvinsiController::class, 'index']);
    Route::post('/', [ProvinsiController::class, 'store']);
    Route::get('/{id}', [ProvinsiController::class, 'show']);
    Route::put('/{id}', [ProvinsiController::class, 'update']);
    Route::delete('/{id}', [ProvinsiController::class, 'destroy']);
});
