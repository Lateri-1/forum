<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController; 
use App\Http\Controllers\ThreadController;  

// als gast
Route::get('/', [ThreadController::class, 'index'])->name('home');
Route::get('/threads/{thread}', [ThreadController::class, 'show']);

// ald ingelogd
Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

// als admin
Route::middleware(['auth', 'admin'])->group(Function () {
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::patch('/users/{user}', [UserController::class, 'update']);
    Route::get('/users', [UserController::class, 'admin']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
});

require __DIR__.'/settings.php';
