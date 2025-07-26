<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Models\Genre;
use App\Models\Platform;
use App\Models\Theme;
use App\Models\Developer;
use App\Models\Publisher;
use App\Models\GameMode;
use App\Models\PlayerPerspective;
use App\Models\AgeRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['genre', 'platform', 'reviews'])
            ->where('type', 'game');

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Genre filter
        if ($request->filled('genre')) {
            $query->whereHas('genre', function ($q) use ($request) {
                $q->where('slug', $request->genre);
            });
        }

        // Platform filter
        if ($request->filled('platform')) {
            $query->whereHas('platforms', function ($q) use ($request) {
                $q->where('slug', $request->platform);
            });
        }

        // Score range filter
        if ($request->filled('score_range')) {
            $ranges = explode('-', $request->score_range);
            if (count($ranges) == 2) {
                $query->whereBetween('staff_rating', [$ranges[0], $ranges[1]]);
            }
        }

        // Developer filter
        if ($request->filled('developer')) {
            $query->whereHas('developers', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->developer . '%');
            });
        }

        // Publisher filter
        if ($request->filled('publisher')) {
            $query->whereHas('publishers', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->publisher . '%');
            });
        }

        // Player Perspective filter
        if ($request->filled('perspective')) {
            $query->whereHas('playerPerspectives', function ($q) use ($request) {
                $q->where('slug', $request->perspective);
            });
        }

        // ESRB Rating filter
        if ($request->filled('esrb_rating')) {
            $query->whereHas('esrbRating', function ($q) use ($request) {
                $q->where('slug', $request->esrb_rating);
            });
        }

        // PEGI Rating filter
        if ($request->filled('pegi_rating')) {
            $query->whereHas('pegiRating', function ($q) use ($request) {
                $q->where('slug', $request->pegi_rating);
            });
        }

        // Game Mode filter
        if ($request->filled('game_mode')) {
            $query->whereHas('gameModes', function ($q) use ($request) {
                $q->where('slug', $request->game_mode);
            });
        }

        // Theme filter
        if ($request->filled('theme')) {
            $query->whereHas('themes', function ($q) use ($request) {
                $q->where('slug', $request->theme);
            });
        }

        // Sorting
        switch ($request->get('sort', 'latest')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'rating_high':
                $query->orderBy('staff_rating', 'desc');
                break;
            case 'rating_low':
                $query->orderBy('staff_rating', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12);
        $genres = Genre::active()->get();
        $platforms = Platform::active()->get();
        $playerPerspectives = PlayerPerspective::orderBy('name')->get();
        $esrbRatings = AgeRating::where('type', 'esrb')->orderBy('name')->get();
        $pegiRatings = AgeRating::where('type', 'pegi')->orderBy('name')->get();
        $gameModes = GameMode::orderBy('name')->get();
        $themes = Theme::orderBy('name')->get();

        return view('games.index', compact('products', 'genres', 'platforms', 'playerPerspectives', 'esrbRatings', 'pegiRatings', 'gameModes', 'themes'));
    }

    public function show(Product $product)
    {
        $product->load(['genre', 'genres', 'platform', 'platforms']);
        
        // Separate staff, streamer, and user reviews using Eloquent queries for better performance
        $staffReviews = $product->reviews()
            ->where('is_staff_review', true)
            ->where('is_published', true)
            ->with('user')
            ->get();
            
        $streamerReviews = $product->reviews()
            ->where('is_staff_review', false)
            ->where('is_published', true)
            ->whereNotNull('streamer_profile_id')
            ->where('show_on_streamer_profile', true) // Only show reviews that are set to be visible on streamer profile
            ->with(['user', 'streamerProfile'])
            ->orderByDesc('created_at')
            ->get();
            
        $userReviews = $product->reviews()
            ->where('is_staff_review', false)
            ->where('is_published', true)
            ->whereNull('streamer_profile_id')
            ->with('user')
            ->orderByDesc('created_at')
            ->get();
        
        // Calculate average user rating
        $averageUserRating = $userReviews->avg('rating');
        
        // Check if current user has already rated this product
        $userRating = null;
        if (Auth::check()) {
            $userRating = $product->reviews()
                ->where('user_id', Auth::id())
                ->value('rating');
                
            // Debug: Log the user rating being passed
            \Log::info('User rating for product ' . $product->id . ': ' . $userRating);
        }
        
        return view('games.show', compact('product', 'staffReviews', 'streamerReviews', 'userReviews', 'averageUserRating', 'userRating'));
    }

    public function rate(Request $request, Product $product)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Please login to rate this product'], 401);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:10',
        ]);

        // Check if user already has a rating for this product (prioritize written reviews over quick ratings)
        $existingReview = Review::where('product_id', $product->id)
            ->where('user_id', Auth::id())
            ->orderByRaw("CASE WHEN title = 'Quick Rating' THEN 1 ELSE 0 END") // Prioritize non-quick ratings
            ->orderBy('created_at', 'desc') // Then by most recent
            ->first();

        // Debug information - get ALL reviews for this user and product
        $allUserReviews = Review::where('product_id', $product->id)
            ->where('user_id', Auth::id())
            ->get(['id', 'title', 'rating', 'is_staff_review', 'is_published', 'created_at']);
            
        // Also check for any reviews without the is_staff_review filter
        $allReviewsForProduct = Review::where('product_id', $product->id)
            ->where('user_id', Auth::id())
            ->get(['id', 'title', 'rating', 'is_staff_review', 'is_published', 'created_at']);
            
        $debugInfo = [
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'existing_review_found' => $existingReview ? true : false,
            'existing_review_title' => $existingReview ? $existingReview->title : null,
            'existing_review_rating' => $existingReview ? $existingReview->rating : null,
            'new_rating' => $request->rating,
            'all_user_reviews' => $allUserReviews->toArray(),
            'all_reviews_for_product' => $allReviewsForProduct->toArray(),
        ];

        if ($existingReview) {
            // Update existing rating
            $oldRating = $existingReview->rating;
            $existingReview->rating = $request->rating;
            $existingReview->save();
            
            // Clean up any duplicate quick ratings (keep only the written review)
            if ($existingReview->title !== 'Quick Rating') {
                Review::where('product_id', $product->id)
                    ->where('user_id', Auth::id())
                    ->where('title', 'Quick Rating')
                    ->delete();
            }
            
            // Determine message based on review type
            if ($existingReview->is_staff_review) {
                $message = 'Your review rating has been updated!';
            } elseif ($existingReview->title === 'Quick Rating') {
                $message = 'Rating updated successfully!';
            } else {
                $message = 'Your review rating has been updated!';
            }
                
            $debugInfo['action'] = 'updated_existing';
            $debugInfo['old_rating'] = $oldRating;
            $debugInfo['review_type'] = $existingReview->is_staff_review ? 'staff' : 'user';
        } else {
            // Create new rating (simple review with just rating)
            Review::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'title' => 'Quick Rating',
                'content' => 'User rating via star system',
                'rating' => $request->rating,
                'is_staff_review' => false,
                'is_published' => true,
            ]);
            
            $message = 'Rating submitted successfully!';
            $debugInfo['action'] = 'created_new';
        }

        // Recalculate the community rating (include user's own reviews in community rating)
        $communityRating = $product->reviews()
            ->where(function($query) {
                $query->where('is_staff_review', false)
                      ->orWhere('user_id', Auth::id()); // Include user's own reviews
            })
            ->avg('rating');

        $communityCount = $product->reviews()
            ->where(function($query) {
                $query->where('is_staff_review', false)
                      ->orWhere('user_id', Auth::id()); // Include user's own reviews
            })
            ->count();

        return response()->json([
            'success' => true,
            'message' => $message,
            'communityRating' => round($communityRating, 1),
            'communityCount' => $communityCount,
            'userRating' => $request->rating,
            'debug' => $debugInfo
        ]);
    }

    public function byGenre(Genre $genre)
    {
        $products = Product::with(['genre', 'platform', 'reviews'])
            ->where('type', 'game')
            ->where('genre_id', $genre->id)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $genres = Genre::active()->get();
        $platforms = Platform::active()->get();
        $playerPerspectives = PlayerPerspective::orderBy('name')->get();
        $esrbRatings = AgeRating::where('type', 'esrb')->orderBy('name')->get();
        $pegiRatings = AgeRating::where('type', 'pegi')->orderBy('name')->get();
        $gameModes = GameMode::orderBy('name')->get();
        $themes = Theme::orderBy('name')->get();
        $filterType = 'Genre';
        $filterValue = $genre->name;

        return view('games.index', compact('products', 'genres', 'platforms', 'playerPerspectives', 'esrbRatings', 'pegiRatings', 'gameModes', 'themes', 'filterType', 'filterValue'));
    }

    public function byPlatform(Platform $platform)
    {
        $products = Product::with(['genre', 'platform', 'reviews'])
            ->where('type', 'game')
            ->whereHas('platforms', function($query) use ($platform) {
                $query->where('platforms.id', $platform->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $genres = Genre::active()->get();
        $platforms = Platform::active()->get();
        $playerPerspectives = PlayerPerspective::orderBy('name')->get();
        $esrbRatings = AgeRating::where('type', 'esrb')->orderBy('name')->get();
        $pegiRatings = AgeRating::where('type', 'pegi')->orderBy('name')->get();
        $gameModes = GameMode::orderBy('name')->get();
        $themes = Theme::orderBy('name')->get();
        $filterType = 'Platform';
        $filterValue = $platform->name;

        return view('games.index', compact('products', 'genres', 'platforms', 'playerPerspectives', 'esrbRatings', 'pegiRatings', 'gameModes', 'themes', 'filterType', 'filterValue'));
    }

    public function byDeveloper(Developer $developer)
    {
        $products = Product::with(['genre', 'platform', 'reviews'])
            ->where('type', 'game')
            ->whereHas('developers', function($query) use ($developer) {
                $query->where('developers.id', $developer->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $genres = Genre::active()->get();
        $platforms = Platform::active()->get();
        $playerPerspectives = PlayerPerspective::orderBy('name')->get();
        $esrbRatings = AgeRating::where('type', 'esrb')->orderBy('name')->get();
        $pegiRatings = AgeRating::where('type', 'pegi')->orderBy('name')->get();
        $gameModes = GameMode::orderBy('name')->get();
        $themes = Theme::orderBy('name')->get();
        $filterType = 'Developer';
        $filterValue = $developer->name;

        return view('games.index', compact('products', 'genres', 'platforms', 'playerPerspectives', 'esrbRatings', 'pegiRatings', 'gameModes', 'themes', 'filterType', 'filterValue'));
    }

    public function byPublisher(Publisher $publisher)
    {
        $products = Product::with(['genre', 'platform', 'reviews'])
            ->where('type', 'game')
            ->whereHas('publishers', function($query) use ($publisher) {
                $query->where('publishers.id', $publisher->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $genres = Genre::active()->get();
        $platforms = Platform::active()->get();
        $playerPerspectives = PlayerPerspective::orderBy('name')->get();
        $esrbRatings = AgeRating::where('type', 'esrb')->orderBy('name')->get();
        $pegiRatings = AgeRating::where('type', 'pegi')->orderBy('name')->get();
        $gameModes = GameMode::orderBy('name')->get();
        $themes = Theme::orderBy('name')->get();
        $filterType = 'Publisher';
        $filterValue = $publisher->name;

        return view('games.index', compact('products', 'genres', 'platforms', 'playerPerspectives', 'esrbRatings', 'pegiRatings', 'gameModes', 'themes', 'filterType', 'filterValue'));
    }

    public function byTheme(Theme $theme)
    {
        $products = Product::with(['genre', 'platform', 'reviews'])
            ->where('type', 'game')
            ->whereHas('themes', function($query) use ($theme) {
                $query->where('themes.id', $theme->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $genres = Genre::active()->get();
        $platforms = Platform::active()->get();
        $playerPerspectives = PlayerPerspective::orderBy('name')->get();
        $esrbRatings = AgeRating::where('type', 'esrb')->orderBy('name')->get();
        $pegiRatings = AgeRating::where('type', 'pegi')->orderBy('name')->get();
        $gameModes = GameMode::orderBy('name')->get();
        $themes = Theme::orderBy('name')->get();
        $filterType = 'Theme';
        $filterValue = $theme->name;

        return view('games.index', compact('products', 'genres', 'platforms', 'playerPerspectives', 'esrbRatings', 'pegiRatings', 'gameModes', 'themes', 'filterType', 'filterValue'));
    }

    public function byGameMode(GameMode $mode)
    {
        $products = Product::with(['genre', 'platform', 'reviews'])
            ->where('type', 'game')
            ->whereHas('gameModes', function($query) use ($mode) {
                $query->where('game_modes.id', $mode->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $genres = Genre::active()->get();
        $platforms = Platform::active()->get();
        $playerPerspectives = PlayerPerspective::orderBy('name')->get();
        $esrbRatings = AgeRating::where('type', 'esrb')->orderBy('name')->get();
        $pegiRatings = AgeRating::where('type', 'pegi')->orderBy('name')->get();
        $gameModes = GameMode::orderBy('name')->get();
        $themes = Theme::orderBy('name')->get();
        $filterType = 'Game Mode';
        $filterValue = $mode->name;

        return view('games.index', compact('products', 'genres', 'platforms', 'playerPerspectives', 'esrbRatings', 'pegiRatings', 'gameModes', 'themes', 'filterType', 'filterValue'));
    }

    public function byPlayerPerspective(PlayerPerspective $perspective)
    {
        $products = Product::with(['genre', 'platform', 'reviews'])
            ->where('type', 'game')
            ->whereHas('playerPerspectives', function($query) use ($perspective) {
                $query->where('player_perspectives.id', $perspective->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $genres = Genre::active()->get();
        $platforms = Platform::active()->get();
        $playerPerspectives = PlayerPerspective::orderBy('name')->get();
        $esrbRatings = AgeRating::where('type', 'esrb')->orderBy('name')->get();
        $pegiRatings = AgeRating::where('type', 'pegi')->orderBy('name')->get();
        $gameModes = GameMode::orderBy('name')->get();
        $themes = Theme::orderBy('name')->get();
        $filterType = 'Player Perspective';
        $filterValue = $perspective->name;

        return view('games.index', compact('products', 'genres', 'platforms', 'playerPerspectives', 'esrbRatings', 'pegiRatings', 'gameModes', 'themes', 'filterType', 'filterValue'));
    }

    public function byEsrbRating(AgeRating $rating)
    {
        $products = Product::with(['genre', 'platform', 'reviews'])
            ->where('type', 'game')
            ->where('esrb_rating_id', $rating->id)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $genres = Genre::active()->get();
        $platforms = Platform::active()->get();
        $playerPerspectives = PlayerPerspective::orderBy('name')->get();
        $esrbRatings = AgeRating::where('type', 'esrb')->orderBy('name')->get();
        $pegiRatings = AgeRating::where('type', 'pegi')->orderBy('name')->get();
        $gameModes = GameMode::orderBy('name')->get();
        $themes = Theme::orderBy('name')->get();
        $filterType = 'ESRB Rating';
        $filterValue = $rating->name;

        return view('games.index', compact('products', 'genres', 'platforms', 'playerPerspectives', 'esrbRatings', 'pegiRatings', 'gameModes', 'themes', 'filterType', 'filterValue'));
    }

    public function byPegiRating(AgeRating $rating)
    {
        $products = Product::with(['genre', 'platform', 'reviews'])
            ->where('type', 'game')
            ->where('pegi_rating_id', $rating->id)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $genres = Genre::active()->get();
        $platforms = Platform::active()->get();
        $playerPerspectives = PlayerPerspective::orderBy('name')->get();
        $esrbRatings = AgeRating::where('type', 'esrb')->orderBy('name')->get();
        $pegiRatings = AgeRating::where('type', 'pegi')->orderBy('name')->get();
        $gameModes = GameMode::orderBy('name')->get();
        $themes = Theme::orderBy('name')->get();
        $filterType = 'PEGI Rating';
        $filterValue = $rating->name;

        return view('games.index', compact('products', 'genres', 'platforms', 'playerPerspectives', 'esrbRatings', 'pegiRatings', 'gameModes', 'themes', 'filterType', 'filterValue'));
    }
} 