<?php

use App\Http\Controllers\KinerjaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HelloWorldController::class, 'index'])->name('index');
Route::get('/predikat-kinerja', [KinerjaController::class, 'index'])->name('predikat-kinerja.index');
Route::post('/predikat-kinerja', [KinerjaController::class, 'predikat_kinerja'])->name('predikat-kinerja');

