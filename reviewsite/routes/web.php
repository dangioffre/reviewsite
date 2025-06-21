<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReviewController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::resource('posts', PostController::class);

// Review system
Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
Route::get('/reviews/{product}', [ReviewController::class, 'show'])->name('reviews.show');
Route::post('/reviews/{product}', [ReviewController::class, 'store'])->name('reviews.store');
