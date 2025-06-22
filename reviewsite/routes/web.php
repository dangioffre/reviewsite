<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\PostController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\TechController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReportController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::resource('posts', PostController::class);

// Game system (replacing review system)
Route::get('/games', [GameController::class, 'index'])->name('games.index');
Route::get('/games/{product}', [GameController::class, 'show'])->name('games.show');
Route::post('/games/{product}/rate', [GameController::class, 'rate'])->name('games.rate');
Route::get('/games/{product}/reviews/create', [ReviewController::class, 'create'])->name('games.reviews.create');
Route::post('/games/{product}/reviews', [ReviewController::class, 'store'])->name('games.reviews.store');
Route::get('/games/{product}/{review}', [ReviewController::class, 'show'])->name('games.reviews.show');
Route::get('/games/{product}/{review}/edit', [ReviewController::class, 'edit'])->name('games.reviews.edit');
Route::put('/games/{product}/{review}', [ReviewController::class, 'update'])->name('games.reviews.update');
Route::delete('/games/{product}/{review}', [ReviewController::class, 'destroy'])->name('games.reviews.destroy');

// Game Review Reports
Route::get('/games/{product}/{review}/report', [ReportController::class, 'show'])->name('games.reviews.report.show');
Route::post('/games/{product}/{review}/report', [ReportController::class, 'store'])->name('games.reviews.report.store');

// Tech system (hardware and accessories)
Route::get('/tech', [TechController::class, 'index'])->name('tech.index');
Route::get('/tech/{product}', [TechController::class, 'show'])->name('tech.show');
Route::post('/tech/{product}/rate', [TechController::class, 'rate'])->name('tech.rate');
Route::get('/tech/{product}/reviews/create', [ReviewController::class, 'create'])->name('tech.reviews.create');
Route::post('/tech/{product}/reviews', [ReviewController::class, 'store'])->name('tech.reviews.store');
Route::get('/tech/{product}/{review}', [ReviewController::class, 'show'])->name('tech.reviews.show');
Route::get('/tech/{product}/{review}/edit', [ReviewController::class, 'edit'])->name('tech.reviews.edit');
Route::put('/tech/{product}/{review}', [ReviewController::class, 'update'])->name('tech.reviews.update');
Route::delete('/tech/{product}/{review}', [ReviewController::class, 'destroy'])->name('tech.reviews.destroy');

// Tech Review Reports
Route::get('/tech/{product}/{review}/report', [ReportController::class, 'show'])->name('tech.reviews.report.show');
Route::post('/tech/{product}/{review}/report', [ReportController::class, 'store'])->name('tech.reviews.report.store');

// Authentication Routes
use Illuminate\Support\Facades\Auth;

// Regular user authentication (separate from admin)
Route::get('/login', function () {
    if (Auth::check()) {
        // If user is already logged in, redirect based on admin status
        if (Auth::user()->is_admin) {
            return redirect('/admin');
        }
        return redirect('/');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', function (Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        
        // Redirect based on user type
        if (Auth::user()->is_admin) {
            return redirect()->intended('/admin');
        }
        
        return redirect()->intended('/');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
})->name('login.post');

Route::post('/logout', function (Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Register routes for new users
Route::get('/register', function () {
    if (Auth::check()) {
        return redirect('/');
    }
    return view('auth.register');
})->name('register');

Route::post('/register', function (Illuminate\Http\Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'is_admin' => false, // Regular users are not admin by default
    ]);

    Auth::login($user);

    return redirect('/');
})->name('register.post');
