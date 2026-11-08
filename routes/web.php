<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController; 

Route::inertia('/', 'Welcome')->name('home');

Route::get('/users', [UserController::class, 'admin'])
    ->middleware(['auth', 'admin']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});
Route::get('/users/{user}', [UserController::class, 'show'])
    ->middleware(['auth', 'admin']);
Route::patch('/users/{user}', [UserController::class, 'update']);

require __DIR__.'/settings.php';
