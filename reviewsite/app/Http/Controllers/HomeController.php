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
            ->take(3)
            ->get();

        // Recently Released Games (most recently released games)
        $recentGames = Product::where('type', 'game')
            ->whereNotNull('release_date')
            ->where('release_date', '<=', now()) // Only released games, not future ones
            ->with(['genre', 'platform', 'developers'])
            ->orderBy('release_date', 'desc')
            ->take(6)
            ->get();

        // Recent Streamers (recently joined and approved)
        $recentStreamers = StreamerProfile::approved()
            ->with(['user'])
            ->withCount('followers')
            ->latest()
            ->take(6)
            ->get();

        // Recent Podcasts (recently added and approved)
        $recentPodcasts = Podcast::approved()
            ->with(['owner'])
            ->withCount(['episodes', 'reviews'])
            ->latest()
            ->take(6)
            ->get();

        // Recent Lists (recently created public lists)
        $recentLists = ListModel::where('is_public', true)
            ->with(['user'])
            ->withCount(['items', 'followers', 'comments'])
            ->latest()
            ->take(6)
            ->get();

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