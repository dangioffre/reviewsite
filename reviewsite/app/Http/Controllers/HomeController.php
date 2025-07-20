<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Review;
use App\Models\Product;
use App\Models\StreamerProfile;
use App\Models\Podcast;
use App\Models\ListModel;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the home page.
     */
    public function index(): View
    {
        // Hero Section - Recent Staff Reviews
        $heroReviews = Review::where('is_staff_review', true)
            ->where('is_published', true)
            ->with(['product.genre', 'product.platform', 'user'])
            ->latest()
            ->take(4)
            ->get();

        // Games - Featured first, then recent (total 5)
        $featuredGames = Product::where('type', 'game')
            ->where('is_featured', true)
            ->whereNotNull('release_date')
            ->where('release_date', '<=', now())
            ->with(['genre', 'platform', 'developers'])
            ->orderBy('release_date', 'desc')
            ->take(5)
            ->get();
            
        $recentGamesCount = 5 - $featuredGames->count();
        $nonFeaturedGames = Product::where('type', 'game')
            ->where('is_featured', false)
            ->whereNotNull('release_date')
            ->where('release_date', '<=', now())
            ->with(['genre', 'platform', 'developers'])
            ->orderBy('release_date', 'desc')
            ->take($recentGamesCount)
            ->get();
            
        $recentGames = $featuredGames->concat($nonFeaturedGames);

        // Streamers - Featured first, then recent (total 5)
        $featuredStreamers = StreamerProfile::approved()
            ->where('is_featured', true)
            ->with(['user'])
            ->withCount('followers')
            ->latest()
            ->take(5)
            ->get();
            
        $recentStreamersCount = 5 - $featuredStreamers->count();
        $nonFeaturedStreamers = StreamerProfile::approved()
            ->where('is_featured', false)
            ->with(['user'])
            ->withCount('followers')
            ->latest()
            ->take($recentStreamersCount)
            ->get();
            
        $recentStreamers = $featuredStreamers->concat($nonFeaturedStreamers);

        // Podcasts - Featured first, then recent (total 5)
        $featuredPodcasts = Podcast::approved()
            ->where('is_featured', true)
            ->with(['owner'])
            ->withCount(['episodes', 'reviews'])
            ->latest()
            ->take(5)
            ->get();
            
        $recentPodcastsCount = 5 - $featuredPodcasts->count();
        $nonFeaturedPodcasts = Podcast::approved()
            ->where('is_featured', false)
            ->with(['owner'])
            ->withCount(['episodes', 'reviews'])
            ->latest()
            ->take($recentPodcastsCount)
            ->get();
            
        $recentPodcasts = $featuredPodcasts->concat($nonFeaturedPodcasts);

        // Lists - Featured first, then recent (total 5, public only)
        $featuredLists = ListModel::where('is_public', true)
            ->where('is_featured', true)
            ->with(['user'])
            ->withCount(['items', 'followers', 'comments'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        $recentListsCount = 5 - $featuredLists->count();
        $nonFeaturedLists = ListModel::where('is_public', true)
            ->where('is_featured', false)
            ->with(['user'])
            ->withCount(['items', 'followers', 'comments'])
            ->latest()
            ->take($recentListsCount)
            ->get();
            
        $recentLists = $featuredLists->concat($nonFeaturedLists);

        // Featured Posts (keeping existing functionality)
        $featuredPosts = Post::where('is_featured', true)
            ->latest()
            ->take(3)
            ->get();
            
        return view('home', [
            'heroReviews' => $heroReviews,
            'recentGames' => $recentGames,
            'recentStreamers' => $recentStreamers,
            'recentPodcasts' => $recentPodcasts,
            'recentLists' => $recentLists,
            'featuredPosts' => $featuredPosts,
        ]);
    }
} 