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
        
        // Add review activities
        $recentReviews = $user->reviews()
            ->with('product')
            ->orderBy('reviews.created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($review) {
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
            });
            
        $activities = $activities->merge($recentReviews);
        
        // Add like activities (when others liked user's reviews)
        $recentLikes = $user->reviews()
            ->whereHas('likes', function($query) {
                $query->where('review_likes.created_at', '>=', now()->subDays(30));
            })
            ->with(['product', 'likes'])
            ->get()
            ->flatMap(function($review) {
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
    
    public function reviewsAndLikes(Request $request)
    {
        $user = Auth::user();
        $filter = $request->get('filter', 'all'); // all, reviews, likes
        
        $reviews = collect();
        $likedReviews = collect();
        
        if ($filter === 'all' || $filter === 'reviews') {
            $reviews = $user->reviews()
                ->with(['product.genre', 'product.platform'])
                ->withCount('likes')
                ->orderBy('reviews.created_at', 'desc')
                ->get();
        }
        
        if ($filter === 'all' || $filter === 'likes') {
            $likedReviews = $user->likedReviews()
                ->with(['product.genre', 'product.platform'])
                ->orderBy('pivot_created_at', 'desc')
                ->get();
        }
        
        // Combine and sort by date
        $combinedItems = collect();
        
        foreach ($reviews as $review) {
            $combinedItems->push([
                'type' => 'review',
                'data' => $review,
                'date' => $review->created_at,
                'sort_date' => $review->created_at
            ]);
        }
        
        foreach ($likedReviews as $review) {
            $combinedItems->push([
                'type' => 'like',
                'data' => $review,
                'date' => $review->pivot->created_at,
                'sort_date' => $review->pivot->created_at
            ]);
        }
        
        // Sort by date and paginate
        $combinedItems = $combinedItems->sortByDesc('sort_date');
        $perPage = 12;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedItems = $combinedItems->slice($offset, $perPage);
        
        // Create a custom paginator
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $combinedItems->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        return view('dashboard.reviews-and-likes', compact('user', 'paginator', 'filter'));
    }
} 