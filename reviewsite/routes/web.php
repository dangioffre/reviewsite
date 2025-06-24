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

// Game filtering routes
Route::get('/games/genre/{genre}', [GameController::class, 'byGenre'])->name('games.by-genre');
Route::get('/games/platform/{platform}', [GameController::class, 'byPlatform'])->name('games.by-platform');
Route::get('/games/developer/{developer}', [GameController::class, 'byDeveloper'])->name('games.by-developer');
Route::get('/games/publisher/{publisher}', [GameController::class, 'byPublisher'])->name('games.by-publisher');
Route::get('/games/theme/{theme}', [GameController::class, 'byTheme'])->name('games.by-theme');
Route::get('/games/mode/{mode}', [GameController::class, 'byGameMode'])->name('games.by-mode');
Route::get('/games/{product}/reviews/create', [ReviewController::class, 'create'])->name('games.reviews.create');
Route::post('/games/{product}/reviews', [ReviewController::class, 'store'])->name('games.reviews.store');
Route::get('/games/{product}/{review}', [ReviewController::class, 'show'])->name('games.reviews.show');
Route::get('/games/{product}/{review}/edit', [ReviewController::class, 'edit'])->name('games.reviews.edit');
Route::put('/games/{product}/{review}', [ReviewController::class, 'update'])->name('games.reviews.update');
Route::delete('/games/{product}/{review}', [ReviewController::class, 'destroy'])->name('games.reviews.destroy');
Route::post('/games/{product}/{review}/like', [ReviewController::class, 'toggleLike'])->name('games.reviews.like');

// Game Review Reports
Route::get('/games/{product}/{review}/report', [ReportController::class, 'show'])->name('games.reviews.report.show');
Route::post('/games/{product}/{review}/report', [ReportController::class, 'store'])->name('games.reviews.report.store');

// Tech system (hardware and accessories)
Route::get('/tech', [TechController::class, 'index'])->name('tech.index');
Route::get('/tech/{product}', [TechController::class, 'show'])->name('tech.show');
Route::post('/tech/{product}/rate', [TechController::class, 'rate'])->name('tech.rate');

// Tech filtering routes
Route::get('/tech/category/{genre}', [TechController::class, 'byCategory'])->name('tech.by-category');
Route::get('/tech/platform/{platform}', [TechController::class, 'byPlatform'])->name('tech.by-platform');
Route::get('/tech/brand/{developer}', [TechController::class, 'byBrand'])->name('tech.by-brand');
Route::get('/tech/publisher/{publisher}', [TechController::class, 'byPublisher'])->name('tech.by-publisher');
Route::get('/tech/theme/{theme}', [TechController::class, 'byTheme'])->name('tech.by-theme');
Route::get('/tech/{product}/reviews/create', [ReviewController::class, 'create'])->name('tech.reviews.create');
Route::post('/tech/{product}/reviews', [ReviewController::class, 'store'])->name('tech.reviews.store');
Route::get('/tech/{product}/{review}', [ReviewController::class, 'show'])->name('tech.reviews.show');
Route::get('/tech/{product}/{review}/edit', [ReviewController::class, 'edit'])->name('tech.reviews.edit');
Route::put('/tech/{product}/{review}', [ReviewController::class, 'update'])->name('tech.reviews.update');
Route::delete('/tech/{product}/{review}', [ReviewController::class, 'destroy'])->name('tech.reviews.destroy');
Route::post('/tech/{product}/{review}/like', [ReviewController::class, 'toggleLike'])->name('tech.reviews.like');

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

// User Dashboard Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/reviews', [App\Http\Controllers\DashboardController::class, 'reviews'])->name('dashboard.reviews');
    Route::get('/dashboard/likes', [App\Http\Controllers\DashboardController::class, 'likes'])->name('dashboard.likes');
    Route::get('/dashboard/reviews-and-likes', [App\Http\Controllers\DashboardController::class, 'reviewsAndLikes'])->name('dashboard.reviews-and-likes');
    Route::get('/dashboard/collection', [App\Http\Controllers\DashboardController::class, 'collection'])->name('dashboard.collection');
});

Route::get('/debug/reviews', function () {
    $reviews = \App\Models\Review::all(['id', 'product_id', 'user_id', 'title', 'rating', 'is_staff_review', 'created_at']);
    return response()->json($reviews);
})->name('debug.reviews');

Route::get('/debug/rate-test/{product}', function (\App\Models\Product $product) {
    $userId = auth()->id() ?? 1; // Use user ID 1 if not logged in for testing
    
    // Check for existing reviews
    $existingReview = \App\Models\Review::where('product_id', $product->id)
        ->where('user_id', $userId)
        ->first();
        
    $allReviews = \App\Models\Review::where('product_id', $product->id)
        ->where('user_id', $userId)
        ->get(['id', 'title', 'rating', 'is_staff_review', 'is_published', 'created_at']);
        
    return response()->json([
        'product_id' => $product->id,
        'user_id' => $userId,
        'existing_review' => $existingReview ? $existingReview->toArray() : null,
        'all_reviews' => $allReviews->toArray(),
        'product_slug' => $product->slug,
    ]);
})->name('debug.rate-test');
