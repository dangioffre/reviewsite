<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;
use App\Models\Product;
use App\Models\GameUserStatus;
use App\Models\Genre;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get user statistics
        $stats = $this->getUserStats($user);
        
        // Get recent reviews
        $recentReviews = $user->reviews()
            ->with(['product.genre', 'product.platform'])
            ->orderBy('reviews.created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Get most liked reviews
        $mostLikedReviews = $user->reviews()
            ->with(['product.genre', 'product.platform'])
            ->withCount('likes')
            ->orderBy('likes_count', 'desc')
            ->limit(5)
            ->get();
            
        // Get recent likes received
        $recentLikes = $user->reviews()
            ->with(['product.genre', 'product.platform', 'likes'])
            ->whereHas('likes', function($query) {
                $query->where('review_likes.created_at', '>=', now()->subDays(7));
            })
            ->orderBy('reviews.created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Get user's game collection stats
        $gameStats = $this->getGameStats($user);
        
        // Get recent activity
        $recentActivity = $this->getRecentActivity($user);
        
        return view('dashboard.index', compact(
            'user',
            'stats',
            'recentReviews',
            'mostLikedReviews',
            'recentLikes',
            'gameStats',
            'recentActivity'
        ));
    }
    
    private function getUserStats($user)
    {
        $totalReviews = $user->reviews()->count();
        $publishedReviews = $user->reviews()->where('is_published', true)->count();
        $draftReviews = $user->reviews()->where('is_published', false)->count();
        
        $totalLikesReceived = $user->reviews()->withCount('likes')->get()->sum('likes_count');
        $totalLikesGiven = $user->likedReviews()->count();
        
        $averageRating = $user->reviews()->avg('rating');
        
        $reviewsThisMonth = $user->reviews()
            ->where('reviews.created_at', '>=', now()->startOfMonth())
            ->count();
            
        $likesThisMonth = $user->reviews()
            ->whereHas('likes', function($query) {
                $query->where('review_likes.created_at', '>=', now()->startOfMonth());
            })
            ->withCount('likes')
            ->get()
            ->sum('likes_count');
        
        return [
            'total_reviews' => $totalReviews,
            'published_reviews' => $publishedReviews,
            'draft_reviews' => $draftReviews,
            'total_likes_received' => $totalLikesReceived,
            'total_likes_given' => $totalLikesGiven,
            'average_rating' => round($averageRating, 1),
            'reviews_this_month' => $reviewsThisMonth,
            'likes_this_month' => $likesThisMonth,
        ];
    }
    
    private function getGameStats($user)
    {
        $gameStatuses = GameUserStatus::where('user_id', $user->id)->get();
        
        $haveCount = $gameStatuses->where('have', true)->count();
        $wantCount = $gameStatuses->where('want', true)->count();
        $playedCount = $gameStatuses->where('played', true)->count();
        
        $recentlyPlayed = GameUserStatus::where('user_id', $user->id)
            ->where('played', true)
            ->with('product.genre')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
            
        return [
            'have_count' => $haveCount,
            'want_count' => $wantCount,
            'played_count' => $playedCount,
            'recently_played' => $recentlyPlayed,
        ];
    }
    
    private function getRecentActivity($user)
    {
        $activities = collect();
        
        // Add review activities for products
        $recentReviews = $user->reviews()
            ->whereNotNull('product_id') // Only product reviews
            ->with('product')
            ->orderBy('reviews.created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($review) {
                if (!$review->product) {
                    return null; // Skip reviews without a product
                }
                return [
                    'type' => 'review',
                    'action' => $review->is_published ? 'published' : 'created',
                    'title' => $review->title,
                    'product' => $review->product->name,
                    'product_type' => $review->product->type,
                    'rating' => $review->rating,
                    'created_at' => $review->created_at,
                    'url' => route($review->product->type === 'game' ? 'games.reviews.show' : 'tech.reviews.show', [$review->product, $review])
                ];
            })->filter(); // Remove nulls from the collection;
            
        $activities = $activities->merge($recentReviews);
        
        // Add comment activities for podcast episodes
        $recentComments = $user->reviews()
            ->whereNotNull('episode_id') // Only episode comments
            ->with('episode.podcast')
            ->orderBy('reviews.created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($comment) {
                if (!$comment->episode || !$comment->episode->podcast) {
                    return null; // Skip comments without an episode/podcast
                }
                return [
                    'type' => 'comment',
                    'action' => 'published',
                    'title' => $comment->title,
                    'product' => $comment->episode->podcast->name,
                    'product_type' => 'Podcast',
                    'episode_title' => $comment->episode->title,
                    'rating' => $comment->rating,
                    'created_at' => $comment->created_at,
                    'url' => route('podcasts.episodes.show', [$comment->episode->podcast, $comment->episode])
                ];
            })->filter();
            
        $activities = $activities->merge($recentComments);

        // Add like activities (when others liked user's reviews)
        $recentLikes = $user->reviews()
            ->whereHas('likes', function($query) {
                $query->where('review_likes.created_at', '>=', now()->subDays(30));
            })
            ->with(['product', 'likes'])
            ->get()
            ->flatMap(function($review) {
                if (!$review->product) {
                    return []; // Skip reviews without a product
                }
                return $review->likes->map(function($like) use ($review) {
                    return [
                        'type' => 'like',
                        'action' => 'received',
                        'title' => $review->title,
                        'product' => $review->product->name,
                        'product_type' => $review->product->type,
                        'liked_by' => $like->name,
                        'created_at' => $like->pivot->created_at,
                        'url' => route($review->product->type === 'game' ? 'games.reviews.show' : 'tech.reviews.show', [$review->product, $review])
                    ];
                });
            });
            
        $activities = $activities->merge($recentLikes);
        
        // Sort by created_at and take the most recent 15
        return $activities->sortByDesc('created_at')->take(15);
    }
    
    public function reviewsAndLikes(Request $request)
    {
        $user = Auth::user();
        $filter = $request->input('filter', 'all');

        $reviewsQuery = $user->reviews()
            ->with(['product.genre', 'product.platform', 'episode.podcast'])
            ->withCount('likes')
            ->orderBy('created_at', 'desc');

        $likesQuery = $user->likedReviews()
            ->with(['product.genre', 'product.platform', 'episode.podcast', 'user'])
            ->withCount('likes')
            ->orderBy('pivot_created_at', 'desc');

        if ($filter === 'reviews') {
            $paginator = $reviewsQuery->paginate(10)->through(function ($review) {
                return ['type' => 'review', 'data' => $review, 'date' => $review->created_at];
            });
        } elseif ($filter === 'likes') {
            $paginator = $likesQuery->paginate(10)->through(function ($like) {
                return ['type' => 'like', 'data' => $like, 'date' => $like->pivot->created_at];
            });
        } else {
            $allReviews = $reviewsQuery->get()->map(function ($review) {
                return ['type' => 'review', 'data' => $review, 'date' => $review->created_at];
            });
            $allLikes = $likesQuery->get()->map(function ($like) {
                return ['type' => 'like', 'data' => $like, 'date' => $like->pivot->created_at];
            });

            $allItems = $allReviews->merge($allLikes)->sortByDesc('date');
            
            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $allItems->forPage(\Illuminate\Pagination\Paginator::resolveCurrentPage(), 10),
                $allItems->count(),
                10,
                \Illuminate\Pagination\Paginator::resolveCurrentPage(),
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );
        }

        return view('dashboard.reviews-and-likes', compact('user', 'paginator', 'filter'));
    }

    public function reviews()
    {
        $user = Auth::user();
        
        $reviews = $user->reviews()
            ->with(['product.genre', 'product.platform'])
            ->withCount('likes')
            ->orderBy('reviews.created_at', 'desc')
            ->paginate(12);
            
        return view('dashboard.reviews', compact('user', 'reviews'));
    }
    
    public function likes()
    {
        $user = Auth::user();
        
        $likedReviews = $user->likedReviews()
            ->with(['product.genre', 'product.platform'])
            ->orderBy('pivot_created_at', 'desc')
            ->paginate(12);
            
        return view('dashboard.likes', compact('user', 'likedReviews'));
    }
    
    public function collection(Request $request)
    {
        $user = Auth::user();
        
        // Base query
        $query = GameUserStatus::where('user_id', $user->id)
            ->with(['product.genre', 'product.platform']);
        
        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('product', function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        // Status filters
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'have':
                    $query->where('have', true);
                    break;
                case 'want':
                    $query->where('want', true);
                    break;
                case 'played':
                    $query->where('played', true);
                    break;
                case 'favorites':
                    $query->where('is_favorite', true);
                    break;
                case 'dropped':
                    $query->where('dropped', true);
                    break;
            }
        }
        
        // Completion status filter
        if ($request->filled('completion')) {
            $query->where('completion_status', $request->completion);
        }
        
        // Genre filter
        if ($request->filled('genre')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('genre_id', $request->genre);
            });
        }
        
        // Sorting
        $sort = $request->get('sort', 'updated_desc');
        switch ($sort) {
            case 'name_asc':
                $query->join('products', 'game_user_statuses.product_id', '=', 'products.id')
                      ->orderBy('products.name', 'asc')
                      ->select('game_user_statuses.*');
                break;
            case 'name_desc':
                $query->join('products', 'game_user_statuses.product_id', '=', 'products.id')
                      ->orderBy('products.name', 'desc')
                      ->select('game_user_statuses.*');
                break;
            case 'rating_desc':
                $query->orderByDesc('rating');
                break;
            case 'rating_asc':
                $query->orderBy('rating');
                break;
            case 'playtime_desc':
                $query->orderByDesc('hours_played');
                break;
            case 'playtime_asc':
                $query->orderBy('hours_played');
                break;
            case 'completion_desc':
                $query->orderByDesc('completion_percentage');
                break;
            case 'updated_desc':
            default:
                $query->latest('updated_at');
                break;
        }
        
        // Paginate results
        $gameStatuses = $query->paginate(12)->withQueryString();
        
        // Get all genres for filter dropdown
        $genres = Genre::orderBy('name')->get();
        
        // Calculate stats for the entire collection (not filtered)
        $allStatuses = GameUserStatus::where('user_id', $user->id)->get();
        $totalGames = $allStatuses->count();
        $completedGames = $allStatuses->whereIn('completion_status', ['completed', 'fully_completed'])->count();
        $favoriteGames = $allStatuses->where('is_favorite', true)->count();
        $totalPlaytime = $allStatuses->sum('hours_played');
        
        return view('dashboard.collection', compact(
            'gameStatuses', 
            'genres',
            'totalGames', 
            'completedGames', 
            'favoriteGames', 
            'totalPlaytime'
        ));
    }
} 