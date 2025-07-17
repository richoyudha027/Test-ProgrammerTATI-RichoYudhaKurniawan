<?php

use App\Http\Controllers\HelloWorldController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HelloWorldController::class, 'index'])->name('index');
Route::get('/helloworld', [HelloWorldController::class, 'index'])->name('helloworld');

