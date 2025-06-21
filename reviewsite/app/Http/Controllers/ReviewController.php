<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // Show the review index page with search and browse
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');

        // Simple, reliable query
        $query = Product::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($type) {
            $query->where('type', $type);
        }

        $products = $query->latest()->paginate(12);

        return view('reviews.index', compact('products'));
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
