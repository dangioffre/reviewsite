<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\PostController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\TechController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PodcastController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::resource('posts', PostController::class);

// Game system (replacing review system)
Route::get('/games', [GameController::class, 'index'])->name('games.index');
Route::get('/games/{product:slug}', [GameController::class, 'show'])->name('games.show');
Route::post('/games/{product}/rate', [GameController::class, 'rate'])->name('games.rate');

// Game filtering routes
Route::get('/games/genre/{genre}', [GameController::class, 'byGenre'])->name('games.by-genre');
Route::get('/games/platform/{platform}', [GameController::class, 'byPlatform'])->name('games.by-platform');
Route::get('/games/developer/{developer}', [GameController::class, 'byDeveloper'])->name('games.by-developer');
Route::get('/games/publisher/{publisher}', [GameController::class, 'byPublisher'])->name('games.by-publisher');
Route::get('/games/theme/{theme}', [GameController::class, 'byTheme'])->name('games.by-theme');
Route::get('/games/mode/{mode}', [GameController::class, 'byGameMode'])->name('games.by-mode');
Route::get('/games/perspective/{perspective}', [GameController::class, 'byPlayerPerspective'])->name('games.by-perspective');
Route::get('/games/esrb/{rating}', [GameController::class, 'byEsrbRating'])->name('games.by-esrb');
Route::get('/games/pegi/{rating}', [GameController::class, 'byPegiRating'])->name('games.by-pegi');
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

// Podcast Routes
Route::get('/podcasts', [PodcastController::class, 'index'])->name('podcasts.index');
Route::get('/podcasts/create', [PodcastController::class, 'create'])->name('podcasts.create');
Route::post('/podcasts', [PodcastController::class, 'store'])->name('podcasts.store');
Route::get('/podcasts/dashboard', [PodcastController::class, 'dashboard'])->name('podcasts.dashboard');
Route::get('/podcasts/{podcast}/verify', [PodcastController::class, 'verify'])->name('podcasts.verify');
Route::post('/podcasts/fetch-info', [PodcastController::class, 'fetchRssInfo'])->name('podcasts.fetch-info');
Route::post('/podcasts/{podcast}/check-verification', [PodcastController::class, 'checkVerification'])->name('podcasts.check-verification');
Route::post('/podcasts/{podcast}/sync-rss', [PodcastController::class, 'syncRss'])->name('podcasts.sync-rss');
Route::get('/podcasts/{podcast}', [PodcastController::class, 'show'])->name('podcasts.show');
Route::get('/podcasts/{podcast}/episodes/{episode}', [PodcastController::class, 'showEpisode'])->name('podcasts.episodes.show');

// Podcast Management
Route::post('/podcasts/{podcast}/update-links', [PodcastController::class, 'updateLinks'])->name('podcasts.update-links');
Route::delete('/podcasts/{podcast}', [PodcastController::class, 'destroy'])->name('podcasts.destroy');

// Episode Review Attachment Routes
Route::post('/podcasts/{podcast}/episodes/{episode}/attach-review', [PodcastController::class, 'attachReview'])->name('podcasts.episodes.attach-review');
Route::delete('/podcasts/{podcast}/episodes/{episode}/detach-review/{review}', [PodcastController::class, 'detachReview'])->name('podcasts.episodes.detach-review');

// Episode Review Routes
Route::get('/podcasts/{podcast}/episodes/{episode}/reviews/create', [ReviewController::class, 'createEpisodeReview'])->name('podcasts.episodes.reviews.create');
Route::post('/podcasts/{podcast}/episodes/{episode}/reviews', [ReviewController::class, 'storeEpisodeReview'])->name('podcasts.episodes.reviews.store');
Route::get('/podcasts/{podcast}/episodes/{episode}/reviews/{review}', [ReviewController::class, 'showEpisodeReview'])->name('podcasts.episodes.reviews.show');
Route::get('/podcasts/{podcast}/episodes/{episode}/reviews/{review}/edit', [ReviewController::class, 'editEpisodeReview'])->name('podcasts.episodes.reviews.edit');
Route::put('/podcasts/{podcast}/episodes/{episode}/reviews/{review}', [ReviewController::class, 'updateEpisodeReview'])->name('podcasts.episodes.reviews.update');
Route::delete('/podcasts/{podcast}/episodes/{episode}/reviews/{review}', [ReviewController::class, 'destroyEpisodeReview'])->name('podcasts.episodes.reviews.destroy');
Route::post('/podcasts/{podcast}/episodes/{episode}/reviews/{review}/report', [ReportController::class, 'storeEpisodeReviewReport'])->name('podcasts.episodes.reviews.report.store');

// Podcast Team Management Routes
Route::middleware('auth')->group(function () {
    Route::get('/podcasts/{podcast}/team/manage', [App\Http\Controllers\PodcastTeamController::class, 'manage'])->name('podcasts.team.manage');
    Route::post('/podcasts/{podcast}/team/invite', [App\Http\Controllers\PodcastTeamController::class, 'invite'])->name('podcasts.team.invite');
    Route::post('/podcasts/{podcast}/team/{teamMember}/accept', [App\Http\Controllers\PodcastTeamController::class, 'acceptInvitation'])->name('podcasts.team.accept');
    Route::post('/podcasts/{podcast}/team/{teamMember}/decline', [App\Http\Controllers\PodcastTeamController::class, 'declineInvitation'])->name('podcasts.team.decline');
    Route::delete('/podcasts/{podcast}/team/{teamMember}', [App\Http\Controllers\PodcastTeamController::class, 'removeMember'])->name('podcasts.team.remove');
    Route::put('/podcasts/{podcast}/team/{teamMember}/permissions', [App\Http\Controllers\PodcastTeamController::class, 'updatePermissions'])->name('podcasts.team.permissions');
    Route::post('/podcasts/{podcast}/team/leave', [App\Http\Controllers\PodcastTeamController::class, 'leaveTeam'])->name('podcasts.team.leave');
    Route::get('/my-podcast-invitations', [App\Http\Controllers\PodcastTeamController::class, 'myInvitations'])->name('podcasts.invitations');
});

// Streamer OAuth Routes
Route::middleware('auth')->group(function () {
    Route::get('/auth/{platform}/redirect', [App\Http\Controllers\StreamerOAuthController::class, 'redirect'])
        ->name('streamer.oauth.redirect')
        ->where('platform', 'twitch|youtube|kick');
    
    Route::get('/auth/{platform}/callback', [App\Http\Controllers\StreamerOAuthController::class, 'callback'])
        ->name('streamer.oauth.callback')
        ->where('platform', 'twitch|youtube|kick');
    
    Route::delete('/auth/{platform}/disconnect', [App\Http\Controllers\StreamerOAuthController::class, 'disconnect'])
        ->name('streamer.oauth.disconnect')
        ->where('platform', 'twitch|youtube|kick');
});

// Test route for OAuth configuration (remove in production)
Route::get('/test/streamer-oauth', [App\Http\Controllers\TestStreamerController::class, 'testOAuth'])
    ->name('test.streamer.oauth');



// Debug route for streamer profile ownership (remove in production)
Route::get('/debug/streamer-profile/{streamerProfile}', function(\App\Models\StreamerProfile $streamerProfile) {
    $currentUser = auth()->user();
    
    return response()->json([
        'current_user_id' => $currentUser ? $currentUser->id : null,
        'current_user_name' => $currentUser ? $currentUser->name : null,
        'profile_user_id' => $streamerProfile->user_id,
        'profile_owner_name' => $streamerProfile->user ? $streamerProfile->user->name : null,
        'profile_channel_name' => $streamerProfile->channel_name,
        'profile_platform' => $streamerProfile->platform,
        'ownership_match' => $currentUser && $currentUser->id === $streamerProfile->user_id,
        'is_approved' => $streamerProfile->is_approved,
        'verification_status' => $streamerProfile->verification_status,
        'can_request_verification' => $streamerProfile->canRequestVerification(),
    ], 200, [], JSON_PRETTY_PRINT);
})->middleware('auth')->name('debug.streamer.profile');

// Streamer Profile Routes
Route::get('/streamers', [App\Http\Controllers\StreamerProfileController::class, 'index'])
    ->name('streamer.profiles.index');

Route::get('/api/streamers/recommendations', [App\Http\Controllers\StreamerProfileController::class, 'recommendations'])
    ->name('streamer.profiles.recommendations')
    ->middleware('auth');

Route::get('/streamers/create', [App\Http\Controllers\StreamerProfileController::class, 'create'])
    ->name('streamer.profiles.create')
    ->middleware('auth');

Route::post('/streamers', [App\Http\Controllers\StreamerProfileController::class, 'store'])
    ->name('streamer.profiles.store')
    ->middleware('auth');

Route::get('/streamers/{streamerProfile}', [App\Http\Controllers\StreamerProfileController::class, 'show'])
    ->name('streamer.profile.show');

Route::get('/streamers/{streamerProfile}/vods', [App\Http\Controllers\StreamerProfileController::class, 'showVods'])
    ->name('streamer.profile.vods');

Route::middleware('auth')->group(function () {
    Route::get('/streamers/{streamerProfile}/edit', [App\Http\Controllers\StreamerProfileController::class, 'edit'])
        ->name('streamer.profile.edit');
    
    Route::put('/streamers/{streamerProfile}', [App\Http\Controllers\StreamerProfileController::class, 'update'])
        ->name('streamer.profile.update');
    
    // Live Status Management Routes
    Route::post('/streamers/{streamerProfile}/live-status', [App\Http\Controllers\StreamerProfileController::class, 'setLiveStatus'])
        ->name('streamer.profile.set-live-status');
    
    Route::delete('/streamers/{streamerProfile}/live-status', [App\Http\Controllers\StreamerProfileController::class, 'clearLiveStatusOverride'])
        ->name('streamer.profile.clear-live-status');
    
    // VOD Management Routes
    Route::get('/streamers/{streamerProfile}/vods', [App\Http\Controllers\StreamerProfileController::class, 'manageVods'])
        ->name('streamer.profile.manage-vods');
    
    Route::post('/streamers/{streamerProfile}/vods', [App\Http\Controllers\StreamerProfileController::class, 'addVod'])
        ->name('streamer.profile.add-vod');
    
    Route::post('/streamers/{streamerProfile}/import-vods', [App\Http\Controllers\StreamerProfileController::class, 'importVods'])
        ->name('streamer.profile.import-vods');
    
    Route::delete('/streamers/{streamerProfile}/vods/{vod}', [App\Http\Controllers\StreamerProfileController::class, 'deleteVod'])
        ->name('streamer.profile.delete-vod');
    
    Route::post('/streamers/{streamerProfile}/check-vod-health', [App\Http\Controllers\StreamerProfileController::class, 'checkVodHealth'])
        ->name('streamer.profile.check-vod-health');
    
    // Game Showcase Management Routes
    Route::get('/streamer-profiles/{streamerProfile}/manage-showcase', [App\Http\Controllers\StreamerProfileController::class, 'manageShowcase'])
        ->name('streamer.profile.manage-showcase');
    
    Route::delete('/streamer-profiles/{streamerProfile}', [App\Http\Controllers\StreamerProfileController::class, 'destroy'])
        ->name('streamer.profile.destroy');
    
    // Verification system removed - all OAuth-connected streamers are automatically verified
});

// Streamer Follow Routes
Route::middleware('auth')->group(function () {
    Route::post('/streamer/follow/{streamerProfile}', [App\Http\Controllers\StreamerFollowController::class, 'follow'])
        ->name('streamer.follow');
    
    Route::delete('/streamer/follow/{streamerProfile}', [App\Http\Controllers\StreamerFollowController::class, 'unfollow'])
        ->name('streamer.unfollow');
    
    Route::get('/streamer/follow/{streamerProfile}/status', [App\Http\Controllers\StreamerFollowController::class, 'status'])
        ->name('streamer.follow.status');
    
    Route::patch('/streamer/follow/{streamerProfile}/preferences', [App\Http\Controllers\StreamerFollowController::class, 'updateNotificationPreferences'])
        ->name('streamer.notification-preferences');
    
    Route::get('/following', [App\Http\Controllers\StreamerFollowController::class, 'followers'])
        ->name('streamer.followers.index');
});

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
    Route::get('/dashboard/lists', function() { return view('dashboard.lists'); })->name('dashboard.lists');
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

// Public List Routes
Route::get('/lists', function(Illuminate\Http\Request $request) {
    $query = \App\Models\ListModel::where('is_public', true)
        ->with(['user', 'items.product.genre', 'items.product.platform', 'items.product.themes', 'items.product.gameModes'])
        ->withCount(['items', 'followers', 'comments']);
    
    // Search by list name or description (case insensitive)
    if ($request->filled('search')) {
        $search = strtolower($request->search);
        $query->where(function($q) use ($search) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%'])
              ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $search . '%']);
        });
    }
    
    // Filter by category
    if ($request->filled('category') && $request->category !== 'all') {
        $query->where('category', $request->category);
    }
    
    // Filter by user (case insensitive)
    if ($request->filled('user')) {
        $query->whereHas('user', function($q) use ($request) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($request->user) . '%']);
        });
    }
    
    // Search by games within lists (case insensitive)
    if ($request->filled('game')) {
        $query->whereHas('items.product', function($q) use ($request) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($request->game) . '%']);
        });
    }
    
    // Filter by genre (games within lists)
    if ($request->filled('genre')) {
        $query->whereHas('items.product.genre', function($q) use ($request) {
            $q->where('slug', $request->genre);
        });
    }
    
    // Filter by platform (games within lists)
    if ($request->filled('platform')) {
        $query->whereHas('items.product.platform', function($q) use ($request) {
            $q->where('slug', $request->platform);
        });
    }
    
    // Filter by publisher (games within lists)
    if ($request->filled('publisher')) {
        $query->whereHas('items.product.publishers', function($q) use ($request) {
            $q->where('slug', $request->publisher);
        });
    }
    
    // Filter by developer (games within lists)
    if ($request->filled('developer')) {
        $query->whereHas('items.product.developers', function($q) use ($request) {
            $q->where('slug', $request->developer);
        });
    }
    
    // Filter by game mode (games within lists)
    if ($request->filled('game_mode')) {
        $query->whereHas('items.product.gameModes', function($q) use ($request) {
            $q->where('slug', $request->game_mode);
        });
    }
    
    // Sort options
    $sortBy = $request->get('sort', 'created_at');
    $sortDirection = $request->get('direction', 'desc');
    
    switch ($sortBy) {
        case 'name':
            $query->orderBy('name', $sortDirection);
            break;
        case 'items_count':
            $query->orderBy('items_count', $sortDirection);
            break;
        case 'followers_count':
            $query->orderBy('followers_count', $sortDirection);
            break;
        case 'comments_count':
            $query->orderBy('comments_count', $sortDirection);
            break;
        case 'updated_at':
            $query->orderBy('updated_at', $sortDirection);
            break;
        default:
            $query->orderBy('created_at', $sortDirection);
    }
    
    $lists = $query->paginate(12)->appends($request->query());
    
    // Get filter options
    $categories = \App\Models\ListModel::$categories;
    $genres = \App\Models\Genre::where('is_active', true)->where('type', 'game')->get();
    $platforms = \App\Models\Platform::where('is_active', true)->get();
    $publishers = \App\Models\Publisher::where('is_active', true)->get();
    $developers = \App\Models\Developer::where('is_active', true)->get();
    $gameModes = \App\Models\GameMode::where('is_active', true)->where('type', 'game')->get();
    
    return view('lists.index', compact('lists', 'categories', 'genres', 'platforms', 'publishers', 'developers', 'gameModes'));
})->name('lists.index');

// API routes for autocomplete
Route::get('/api/search/games', function(Illuminate\Http\Request $request) {
    if (!$request->filled('q') || strlen($request->q) < 2) {
        return response()->json([]);
    }
    
    $games = \App\Models\Product::where('type', 'game')
        ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($request->q) . '%'])
        ->limit(10)
        ->get(['name', 'slug'])
        ->map(function($game) {
            return [
                'name' => $game->name,
                'slug' => $game->slug
            ];
        });
    
    return response()->json($games);
})->name('api.search.games');

Route::get('/api/search/users', function(Illuminate\Http\Request $request) {
    if (!$request->filled('q') || strlen($request->q) < 2) {
        return response()->json([]);
    }
    
    $users = \App\Models\User::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($request->q) . '%'])
        ->whereHas('lists', function($query) {
            $query->where('is_public', true);
        })
        ->limit(10)
        ->get(['name'])
        ->map(function($user) {
            return [
                'name' => $user->name
            ];
        });
    
    return response()->json($users);
})->name('api.search.users');

Route::get('/api/search/publishers', function(Illuminate\Http\Request $request) {
    if (!$request->filled('q') || strlen($request->q) < 2) {
        return response()->json([]);
    }
    
    $publishers = \App\Models\Publisher::where('is_active', true)
        ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($request->q) . '%'])
        ->limit(10)
        ->get(['name', 'slug'])
        ->map(function($publisher) {
            return [
                'name' => $publisher->name,
                'slug' => $publisher->slug
            ];
        });
    
    return response()->json($publishers);
})->name('api.search.publishers');

Route::get('/api/search/developers', function(Illuminate\Http\Request $request) {
    if (!$request->filled('q') || strlen($request->q) < 2) {
        return response()->json([]);
    }
    
    $developers = \App\Models\Developer::where('is_active', true)
        ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($request->q) . '%'])
        ->limit(10)
        ->get(['name', 'slug'])
        ->map(function($developer) {
            return [
                'name' => $developer->name,
                'slug' => $developer->slug
            ];
        });
    
    return response()->json($developers);
})->name('api.search.developers');

Route::get('/api/search/streamers', function(Illuminate\Http\Request $request) {
    if (!$request->filled('q') || strlen($request->q) < 2) {
        return response()->json([]);
    }
    
    $streamers = \App\Models\StreamerProfile::approved()
        ->whereRaw('LOWER(channel_name) LIKE ?', ['%' . strtolower($request->q) . '%'])
        ->orWhereRaw('LOWER(bio) LIKE ?', ['%' . strtolower($request->q) . '%'])
        ->with('user')
        ->limit(10)
        ->get()
        ->map(function($streamer) {
            return [
                'id' => $streamer->id,
                'channel_name' => $streamer->channel_name,
                'platform' => $streamer->platform,
                'bio' => $streamer->bio ? Str::limit($streamer->bio, 100) : null,
                'profile_photo_url' => $streamer->profile_photo_url,
                'is_live' => $streamer->isLive(),
                'is_verified' => $streamer->is_verified,
                'url' => route('streamer.profile.show', $streamer)
            ];
        });
    
    return response()->json($streamers);
})->name('api.search.streamers');

// Main Search Routes
Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search.index');
Route::get('/api/search/suggestions', [App\Http\Controllers\SearchController::class, 'suggestions'])->name('api.search.suggestions');

Route::get('/api/search/streamer-reviews', function(Illuminate\Http\Request $request) {
    if (!$request->filled('q') || strlen($request->q) < 2) {
        return response()->json([]);
    }
    
    $reviews = \App\Models\Review::whereNotNull('streamer_profile_id')
        ->where('is_published', true)
        ->where(function($query) use ($request) {
            $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($request->q) . '%'])
                  ->orWhereRaw('LOWER(content) LIKE ?', ['%' . strtolower($request->q) . '%']);
        })
        ->with(['streamerProfile', 'product', 'user'])
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get()
        ->map(function($review) {
            return [
                'id' => $review->id,
                'title' => $review->title,
                'content' => Str::limit($review->content, 150),
                'rating' => $review->rating,
                'streamer_name' => $review->streamerProfile->channel_name,
                'streamer_platform' => $review->streamerProfile->platform,
                'product_name' => $review->product->name,
                'product_type' => $review->product->type,
                'author_display' => $review->user->name . ' (' . $review->streamerProfile->channel_name . ')',
                'created_at' => $review->created_at->format('M j, Y'),
                'url' => $review->product->type === 'game' 
                    ? route('games.reviews.show', [$review->product, $review])
                    : route('tech.reviews.show', [$review->product, $review]),
                'streamer_url' => route('streamer.profile.show', $review->streamerProfile),
                'category' => 'streamer_review'
            ];
        });
    
    return response()->json($reviews);
})->name('api.search.streamer-reviews');

Route::get('/lists/{slug}', function($slug, Illuminate\Http\Request $request) {
    $list = \App\Models\ListModel::where('slug', $slug)
        ->where('is_public', true)
        ->with(['user', 'items.product', 'collaborators.user', 'comments.user'])
        ->withCount(['followers', 'comments'])
        ->firstOrFail();
    
    $showCollaborationManager = $request->get('manage') === 'collaboration' && 
                               auth()->check() && 
                               $list->user_id === auth()->id() && 
                               $list->allow_collaboration;
    
    return view('lists.public', [
        'slug' => $slug,
        'list' => $list,
        'showCollaborationManager' => $showCollaborationManager
    ]);
})->name('lists.public');

// List Interaction Routes (require authentication)
Route::middleware('auth')->group(function () {
    Route::post('/lists/{list}/follow', function(\App\Models\ListModel $list) {
        if (!$list->is_public) {
            abort(404);
        }
        
        if (!$list->isFollowedBy(auth()->id())) {
            $list->followers()->create(['user_id' => auth()->id()]);
            $list->increment('followers_count');
        }
        
        return redirect()->back()->with('success', 'Now following this list!');
    })->name('lists.follow');
    
    Route::delete('/lists/{list}/unfollow', function(\App\Models\ListModel $list) {
        if (!$list->is_public) {
            abort(404);
        }
        
        if ($list->isFollowedBy(auth()->id())) {
            $list->followers()->where('user_id', auth()->id())->delete();
            $list->decrement('followers_count');
        }
        
        return redirect()->back()->with('success', 'Unfollowed list.');
    })->name('lists.unfollow');
    
    // Comment Routes
    Route::post('/lists/{list}/comments', function(\App\Models\ListModel $list, Illuminate\Http\Request $request) {
        if (!$list->is_public || !$list->allow_comments) {
            abort(404);
        }
        
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:list_comments,id'
        ]);
        
        $comment = $list->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
            'parent_id' => $request->parent_id,
        ]);
        
        // Update comment count only for top-level comments
        if (!$request->parent_id) {
            $list->increment('comments_count');
        }
        
        return redirect()->back()->with('success', 'Comment posted!');
    })->name('lists.comments.store');
    
    Route::post('/comments/{comment}/like', function(\App\Models\ListComment $comment) {
        $existingLike = $comment->likes()->where('user_id', auth()->id())->first();
        
        if ($existingLike) {
            $existingLike->delete();
            $comment->decrement('likes_count');
            $message = 'Like removed.';
        } else {
            $comment->likes()->create(['user_id' => auth()->id()]);
            $comment->increment('likes_count');
            $message = 'Comment liked!';
        }
        
        return redirect()->back()->with('success', $message);
    })->name('lists.comments.like');
    
    // Collaboration request route
    Route::post('/lists/{list}/collaborate', function(\App\Models\ListModel $list) {
        if (!$list->is_public || !$list->allow_collaboration) {
            abort(404);
        }
        
        // Check if user already has a collaboration request/invitation
        $existingCollaboration = $list->collaborators()->where('user_id', auth()->id())->first();
        
        if ($existingCollaboration) {
            if ($existingCollaboration->isPending()) {
                return redirect()->back()->with('error', 'You already have a pending collaboration request for this list.');
            } else {
                return redirect()->back()->with('error', 'You are already a collaborator on this list.');
            }
        }
        
        // Create collaboration request with default permissions
        $list->collaborators()->create([
            'user_id' => auth()->id(),
            'invited_by_owner' => false, // This is a user request, not an owner invitation
            'can_add_games' => true,
            'can_delete_games' => true,
            'can_rename_list' => false,
            'can_manage_users' => false,
            'can_change_privacy' => false,
            'can_change_category' => false,
            'invited_at' => now(),
            // accepted_at remains null for pending requests
        ]);
        
        return redirect()->back()->with('success', 'Collaboration request sent! The list owner will be notified.');
    })->name('lists.collaborate');
});

Route::post('/games/{product}/{review}/comments', [\App\Http\Controllers\ReviewCommentController::class, 'store'])->name('games.reviews.comments.store');
Route::post('/tech/{product}/{review}/comments', [\App\Http\Controllers\ReviewCommentController::class, 'store'])->name('tech.reviews.comments.store');
Route::post('/review-comments/{comment}/like', [\App\Http\Controllers\ReviewCommentLikeController::class, 'toggle'])->name('review-comments.like');
Route::post('/review-comments/{comment}/report', [\App\Http\Controllers\ReviewCommentReportController::class, 'store'])->name('review-comments.report');


