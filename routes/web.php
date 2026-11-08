<?php

use App\Http\Controllers\ReplyController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// als gast
Route::get('/', [ThreadController::class, 'index'])->name('home');
Route::get('/threads/{thread}', [ThreadController::class, 'show'])->whereNumber('thread');
Route::get('/topics/{topic}', [TopicController::class, 'show'])->whereNumber('topic');

// als ingelogd
Route::middleware('auth')->group(function () {
    Route::get('/threads/{thread}/topics/create', [TopicController::class, 'create']);
    Route::post('/threads/{thread}/topics', [TopicController::class, 'store']);
    Route::post('/topics/{topic}/replies', [ReplyController::class, 'store']);

    Route::get('/topics/{topic}/edit', [TopicController::class, 'edit'])->can('update', 'topic');
    Route::patch('/topics/{topic}', [TopicController::class, 'update'])->can('update', 'topic');
    Route::patch('/replies/{reply}', [ReplyController::class, 'update'])->can('update', 'reply');
});

Route::redirect('/dashboard', '/')->name('dashboard');

// als admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/threads/create', [ThreadController::class, 'create']);
    Route::post('/threads', [ThreadController::class, 'store']);
    Route::delete('/threads/{thread}', [ThreadController::class, 'destroy']);
    Route::delete('/topics/{topic}', [TopicController::class, 'destroy']);
    Route::delete('/replies/{reply}', [ReplyController::class, 'destroy']);

    Route::get('/users', [UserController::class, 'admin']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::patch('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
});

require __DIR__.'/settings.php';