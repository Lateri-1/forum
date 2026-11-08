<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController; 

Route::inertia('/', 'Welcome')->name('home');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});
Route::middleware(['auth', 'admin'])->group(Function () {
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::patch('/users/{user}', [UserController::class, 'update']);
    Route::get('/users', [UserController::class, 'admin']);

});

require __DIR__.'/settings.php';
