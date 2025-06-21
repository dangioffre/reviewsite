<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Models\Genre;
use App\Models\Platform;
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

        // Category filter
        if ($request->filled('category')) {
            if ($request->category === 'games') {
                $query->where('type', 'game');
            } elseif ($request->category === 'hardware') {
                $query->where('type', 'hardware');
            } elseif ($request->category === 'accessories') {
                $query->where('type', 'accessory');
            }
        }

        // Genre filter
        if ($request->filled('genre')) {
            $query->whereHas('genre', function ($q) use ($request) {
                $q->where('slug', $request->genre);
            });
        }

        // Platform filter
        if ($request->filled('platform')) {
            $query->whereHas('platform', function ($q) use ($request) {
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

        return view('games.index', compact('products', 'genres', 'platforms'));
    }

    public function show(Product $product)
    {
        $product->load(['genre', 'platform', 'reviews.user']);
        
        // Separate staff and user reviews
        $staffReviews = $product->reviews->where('is_staff_review', true);
        $userReviews = $product->reviews->where('is_staff_review', false)->sortByDesc('created_at');
        
        // Calculate average user rating
        $averageUserRating = $userReviews->avg('rating');
        
        return view('games.show', compact('product', 'staffReviews', 'userReviews', 'averageUserRating'));
    }

    public function storeReview(Request $request, Product $product)
    {
        $request->validate([
            'review' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:10',
        ]);

        $product->reviews()->create([
            'user_id' => Auth::id(),
            'review' => $request->review,
            'rating' => $request->rating,
            'is_staff_review' => false,
        ]);

        return redirect()->route('games.show', $product)
            ->with('success', 'Your review has been submitted successfully!');
    }
} 