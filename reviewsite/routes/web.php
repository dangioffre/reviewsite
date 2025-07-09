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
Route::post('/podcasts/{podcast}/check-verification', [PodcastController::class, 'checkVerification'])->name('podcasts.check-verification');
Route::post('/podcasts/{podcast}/sync-rss', [PodcastController::class, 'syncRss'])->name('podcasts.sync-rss');
Route::get('/podcasts/{podcast}', [PodcastController::class, 'show'])->name('podcasts.show');
Route::get('/podcasts/{podcast}/episodes/{episode}', [PodcastController::class, 'showEpisode'])->name('podcasts.episodes.show');

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


