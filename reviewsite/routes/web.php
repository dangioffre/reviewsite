<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\TechController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::resource('posts', PostController::class);

// Game system (replacing review system)
Route::get('/games', [GameController::class, 'index'])->name('games.index');
Route::get('/games/{product}', [GameController::class, 'show'])->name('games.show');
Route::post('/games/{product}/reviews', [GameController::class, 'storeReview'])->name('games.reviews.store');

// Tech system (hardware and accessories)
Route::get('/tech', [TechController::class, 'index'])->name('tech.index');
Route::get('/tech/{product}', [TechController::class, 'show'])->name('tech.show');
Route::post('/tech/{product}/reviews', [TechController::class, 'storeReview'])->name('tech.reviews.store');
