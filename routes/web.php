<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LogbookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', [LogbookController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::resource('logbooks', LogbookController::class)->except(['show']);
    Route::get('/logbooks-verify', [LogbookController::class, 'verifyIndex'])->name('logbooks.verify.index');
    Route::post('/logbooks/{logbook}/verify', [LogbookController::class, 'verify'])->name('logbooks.verify');
    Route::post('/logbooks/{logbook}/reject', [LogbookController::class, 'reject'])->name('logbooks.reject');

    Route::get('/history', [LogbookController::class, 'history'])->name('history');
});

require __DIR__.'/auth.php';