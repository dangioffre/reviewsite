<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use App\Models\Hardware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    /**
     * Display the specified review.
     */
    public function show(Product $product, Review $review)
    {
        // Verify the review belongs to the product
        if ($review->product_id !== $product->id) {
            abort(404);
        }
        
        // Load relationships
        $review->load(['user', 'product.genre', 'product.platform']);
        
        // Check if review is published or user owns it
        if (!$review->is_published && (!Auth::check() || Auth::id() !== $review->user_id)) {
            abort(404);
        }
        
        return view('reviews.show', compact('review', 'product'));
    }

    /**
     * Show the form for creating a new review.
     */
    public function create(Product $product)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to write a review.');
        }
        
        // Check if user already has a review for this product
        $existingReview = Review::where('product_id', $product->id)
            ->where('user_id', Auth::id())
            ->first();
            
        if ($existingReview) {
            $editRoute = $product->type === 'game' ? 'games.reviews.edit' : 'tech.reviews.edit';
            return redirect()->route($editRoute, [$product, $existingReview])
                ->with('info', 'You already have a review for this product. You can edit it here.');
        }
        
        $hardware = Hardware::active()->get();
        
        return view('reviews.create', compact('product', 'hardware'));
    }

    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request, Product $product)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to write a review.');
        }
        
        // Check if user already has a review for this product
        $existingReview = Review::where('product_id', $product->id)
            ->where('user_id', Auth::id())
            ->first();
            
        if ($existingReview) {
            $editRoute = $product->type === 'game' ? 'games.reviews.edit' : 'tech.reviews.edit';
            return redirect()->route($editRoute, [$product, $existingReview])
                ->with('error', 'You already have a review for this product.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:50',
            'rating' => 'required|integer|min:1|max:10',
            'positive_points' => 'nullable|string',
            'negative_points' => 'nullable|string',
            'platform_played_on' => 'nullable|string',
        ]);

        $review = new Review();
        $review->product_id = $product->id;
        $review->user_id = Auth::id();
        $review->title = $request->title;
        $review->content = $request->content;
        $review->rating = $request->rating;
        $review->positive_points = $request->positive_points ? array_filter(explode("\n", $request->positive_points)) : [];
        $review->negative_points = $request->negative_points ? array_filter(explode("\n", $request->negative_points)) : [];
        $review->platform_played_on = $request->platform_played_on;
        $review->is_staff_review = Auth::user()->is_admin;
        $review->is_published = true;
        $review->save();

        $showRoute = $product->type === 'game' ? 'games.reviews.show' : 'tech.reviews.show';
        
        return redirect()->route($showRoute, [$product, $review])
            ->with('success', 'Your review has been published successfully!');
    }

    /**
     * Show the form for editing the specified review.
     */
    public function edit(Product $product, Review $review)
    {
        // Verify the review belongs to the product
        if ($review->product_id !== $product->id) {
            abort(404);
        }
        
        // Check if user can edit this review
        if (!Auth::check() || (Auth::id() !== $review->user_id && !Auth::user()->is_admin)) {
            abort(403);
        }
        
        $hardware = Hardware::active()->get();
        
        return view('reviews.edit', compact('review', 'product', 'hardware'));
    }

    /**
     * Update the specified review in storage.
     */
    public function update(Request $request, Product $product, Review $review)
    {
        // Verify the review belongs to the product
        if ($review->product_id !== $product->id) {
            abort(404);
        }
        
        // Check if user can edit this review
        if (!Auth::check() || (Auth::id() !== $review->user_id && !Auth::user()->is_admin)) {
            abort(403);
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:50',
            'rating' => 'required|integer|min:1|max:10',
            'positive_points' => 'nullable|string',
            'negative_points' => 'nullable|string',
            'platform_played_on' => 'nullable|string',
        ]);

        $review->title = $request->title;
        $review->content = $request->content;
        $review->rating = $request->rating;
        $review->positive_points = $request->positive_points ? array_filter(explode("\n", $request->positive_points)) : [];
        $review->negative_points = $request->negative_points ? array_filter(explode("\n", $request->negative_points)) : [];
        $review->platform_played_on = $request->platform_played_on;
        $review->save();

        $showRoute = $product->type === 'game' ? 'games.reviews.show' : 'tech.reviews.show';
        
        return redirect()->route($showRoute, [$product, $review])
            ->with('success', 'Your review has been updated successfully!');
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(Product $product, Review $review)
    {
        // Verify the review belongs to the product
        if ($review->product_id !== $product->id) {
            abort(404);
        }
        
        // Check if user can delete this review
        if (!Auth::check() || (Auth::id() !== $review->user_id && !Auth::user()->is_admin)) {
            abort(403);
        }
        
        $review->delete();
        
        $redirectRoute = $product->type === 'game' ? 'games.show' : 'tech.show';
        
        return redirect()->route($redirectRoute, $product)
            ->with('success', 'Review deleted successfully.');
    }
}
