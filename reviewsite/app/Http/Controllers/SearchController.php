<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Models\StreamerProfile;
use App\Models\ListModel;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    /**
     * Display the main search page with results.
     */
    public function index(Request $request): View
    {
        $query = $request->get('q', '');
        $category = $request->get('category', 'all');
        
        $results = [
            'games' => collect(),
            'tech' => collect(),
            'streamers' => collect(),
            'streamer_reviews' => collect(),
            'lists' => collect(),
        ];
        
        if (strlen($query) >= 2) {
            // Search games
            if ($category === 'all' || $category === 'games') {
                $results['games'] = Product::where('type', 'game')
                    ->where(function($q) use ($query) {
                        $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($query) . '%'])
                          ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($query) . '%']);
                    })
                    ->with(['genre', 'platform'])
                    ->limit(10)
                    ->get();
            }
            
            // Search tech products
            if ($category === 'all' || $category === 'tech') {
                $results['tech'] = Product::where('type', 'tech')
                    ->where(function($q) use ($query) {
                        $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($query) . '%'])
                          ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($query) . '%']);
                    })
                    ->with(['genre', 'platform'])
                    ->limit(10)
                    ->get();
            }
            
            // Search streamers
            if ($category === 'all' || $category === 'streamers') {
                $results['streamers'] = StreamerProfile::approved()
                    ->where(function($q) use ($query) {
                        $q->whereRaw('LOWER(channel_name) LIKE ?', ['%' . strtolower($query) . '%'])
                          ->orWhereRaw('LOWER(bio) LIKE ?', ['%' . strtolower($query) . '%']);
                    })
                    ->with('user')
                    ->limit(10)
                    ->get();
            }
            
            // Search streamer reviews
            if ($category === 'all' || $category === 'reviews') {
                $results['streamer_reviews'] = Review::whereNotNull('streamer_profile_id')
                    ->where('is_published', true)
                    ->where(function($q) use ($query) {
                        $q->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($query) . '%'])
                          ->orWhereRaw('LOWER(content) LIKE ?', ['%' . strtolower($query) . '%']);
                    })
                    ->with(['streamerProfile', 'product', 'user'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
            }
            
            // Search public lists
            if ($category === 'all' || $category === 'lists') {
                $results['lists'] = ListModel::where('is_public', true)
                    ->where(function($q) use ($query) {
                        $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($query) . '%'])
                          ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($query) . '%']);
                    })
                    ->with(['user'])
                    ->withCount(['items', 'followers'])
                    ->limit(10)
                    ->get();
            }
        }
        
        $totalResults = $results['games']->count() + 
                       $results['tech']->count() + 
                       $results['streamers']->count() + 
                       $results['streamer_reviews']->count() + 
                       $results['lists']->count();
        
        return view('search.index', compact('query', 'category', 'results', 'totalResults'));
    }
    
    /**
     * API endpoint for live search suggestions.
     */
    public function suggestions(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $suggestions = [];
        
        // Get top 3 from each category
        $games = Product::where('type', 'game')
            ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($query) . '%'])
            ->limit(3)
            ->get(['name', 'slug', 'type'])
            ->map(function($item) {
                return [
                    'title' => $item->name,
                    'category' => 'Games',
                    'url' => route('games.show', $item->slug),
                    'type' => 'game'
                ];
            });
            
        $streamers = StreamerProfile::approved()
            ->whereRaw('LOWER(channel_name) LIKE ?', ['%' . strtolower($query) . '%'])
            ->limit(3)
            ->get()
            ->map(function($item) {
                return [
                    'title' => $item->channel_name,
                    'category' => 'Streamers',
                    'url' => route('streamer.profile.show', $item),
                    'type' => 'streamer',
                    'platform' => ucfirst($item->platform),
                    'is_live' => $item->isLive()
                ];
            });
            
        $suggestions = $games->concat($streamers)->take(6);
        
        return response()->json($suggestions);
    }
}