<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Models\Genre;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // Show the review index page with search and browse
    public function index(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $genre = $request->input('genre');
        $platform = $request->input('platform');
        $sort = $request->input('sort', 'latest');
        $scoreRange = $request->input('score_range');

        // Build query
        $query = Product::with(['genre', 'platform']);

        // Search filter
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
        }

        // Category filter
        if ($category) {
            $query->where('type', $category);
        }

        // Genre filter
        if ($genre) {
            $query->whereHas('genre', function($q) use ($genre) {
                $q->where('slug', $genre);
            });
        }

        // Platform filter
        if ($platform) {
            $query->whereHas('platform', function($q) use ($platform) {
                $q->where('slug', $platform);
            });
        }

        // Score range filter
        if ($scoreRange) {
            [$min, $max] = explode('-', $scoreRange);
            $query->whereBetween('staff_rating', [(int)$min, (int)$max]);
        }

        // Sorting
        switch ($sort) {
            case 'oldest':
                $query->oldest();
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
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12);

        // Get active genres and platforms for filters
        $genres = Genre::active()->orderBy('name')->get();
        $platforms = Platform::active()->orderBy('name')->get();

        return view('reviews.index', compact('products', 'genres', 'platforms'));
    }

    // Show a product's detail page with staff and user reviews
    public function show(Product $product)
    {
        $staffReview = $product->staff_review;
        $staffRating = $product->staff_rating;
        $userReviews = $product->reviews()->with('user')->where('is_staff', false)->latest()->get();
        $staffReviews = $product->reviews()->with('user')->where('is_staff', true)->latest()->get();
        return view('reviews.show', compact('product', 'staffReview', 'staffRating', 'userReviews', 'staffReviews'));
    }

    // Store a new user review
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'review' => 'required|string|min:10',
            'rating' => 'required|integer|min:1|max:10',
        ]);
        $review = new Review([
            'user_id' => Auth::id(),
            'review' => $request->input('review'),
            'rating' => $request->input('rating'),
            'is_staff' => Auth::user()?->hasRole('admin') ?? false,
        ]);
        $product->reviews()->save($review);
        return redirect()->route('reviews.show', $product)->with('success', 'Review submitted!');
    }
}
