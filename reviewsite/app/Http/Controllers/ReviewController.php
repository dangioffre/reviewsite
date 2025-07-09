<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use App\Models\Podcast;
use App\Models\Episode;
use App\Http\Controllers\PodcastTeamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use App\Notifications\NewPodcastComment;

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
            // If it's just a quick rating (star rating), allow them to upgrade to full review
            if ($existingReview->title === 'Quick Rating' && $existingReview->content === 'User rating via star system') {
                // Pre-populate the form with existing rating
                $hardware = Product::whereIn('type', ['hardware', 'accessory'])->get();
                $availablePodcasts = $this->getAvailablePodcasts();
                return view('reviews.create', compact('product', 'hardware', 'existingReview', 'availablePodcasts'));
            } else {
                // If it's already a full review, redirect to edit
                $editRoute = $product->type === 'game' ? 'games.reviews.edit' : 'tech.reviews.edit';
                return redirect()->route($editRoute, [$product, $existingReview])
                    ->with('info', 'You already have a review for this product. You can edit it here.');
            }
        }
        
        $hardware = Product::whereIn('type', ['hardware', 'accessory'])->get();
        $availablePodcasts = $this->getAvailablePodcasts();
        
        return view('reviews.create', compact('product', 'hardware', 'availablePodcasts'));
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
            
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:10',
            'positive_points' => 'nullable|string',
            'negative_points' => 'nullable|string',
            'platform_played_on' => 'nullable|string',
            'podcast_id' => 'nullable|exists:podcasts,id',
        ]);

        // Validate podcast permission if podcast_id is provided
        if ($request->podcast_id) {
            $podcast = Podcast::find($request->podcast_id);
            if (!$podcast || !$podcast->userCanPostAsThisPodcast(Auth::user())) {
                return back()->withErrors([
                    'podcast_id' => 'You do not have permission to post reviews as this podcast.'
                ])->withInput();
            }
        }

        if ($existingReview && $existingReview->title === 'Quick Rating' && $existingReview->content === 'User rating via star system') {
            // Update the existing quick rating to a full review
            $existingReview->title = $request->title;
            $existingReview->content = $request->content;
            $existingReview->rating = $request->rating;
            $existingReview->positive_points = $request->positive_points ? array_filter(explode("\n", $request->positive_points)) : [];
            $existingReview->negative_points = $request->negative_points ? array_filter(explode("\n", $request->negative_points)) : [];
            $existingReview->platform_played_on = $request->platform_played_on;
            $existingReview->podcast_id = $request->podcast_id;
            $existingReview->is_staff_review = Auth::user()->is_admin;
            $existingReview->is_published = true;
            $existingReview->save();
            
            $showRoute = $product->type === 'game' ? 'games.reviews.show' : 'tech.reviews.show';
            
            return redirect()->route($showRoute, [$product, $existingReview])
                ->with('success', 'Your review has been published successfully!');
        } elseif ($existingReview) {
            // If they already have a full review, redirect to edit
            $editRoute = $product->type === 'game' ? 'games.reviews.edit' : 'tech.reviews.edit';
            return redirect()->route($editRoute, [$product, $existingReview])
                ->with('error', 'You already have a review for this product.');
        }

        // Create new review
        $review = new Review();
        $review->product_id = $product->id;
        $review->user_id = Auth::id();
        $review->title = $request->title;
        $review->content = $request->content;
        $review->rating = $request->rating;
        $review->positive_points = $request->positive_points ? array_filter(explode("\n", $request->positive_points)) : [];
        $review->negative_points = $request->negative_points ? array_filter(explode("\n", $request->negative_points)) : [];
        $review->platform_played_on = $request->platform_played_on;
        $review->podcast_id = $request->podcast_id;
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
        
        $hardware = Product::whereIn('type', ['hardware', 'accessory'])->get();
        $availablePodcasts = $this->getAvailablePodcasts();
        
        return view('reviews.edit', compact('review', 'product', 'hardware', 'availablePodcasts'));
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
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:10',
            'positive_points' => 'nullable|string',
            'negative_points' => 'nullable|string',
            'platform_played_on' => 'nullable|string',
            'podcast_id' => 'nullable|exists:podcasts,id',
        ]);

        // Validate podcast permission if podcast_id is provided
        if ($request->podcast_id) {
            $podcast = Podcast::find($request->podcast_id);
            if (!$podcast || !$podcast->userCanPostAsThisPodcast(Auth::user())) {
                return back()->withErrors([
                    'podcast_id' => 'You do not have permission to post reviews as this podcast.'
                ])->withInput();
            }
        }

        $review->title = $request->title;
        $review->content = $request->content;
        $review->rating = $request->rating;
        $review->positive_points = $request->positive_points ? array_filter(explode("\n", $request->positive_points)) : [];
        $review->negative_points = $request->negative_points ? array_filter(explode("\n", $request->negative_points)) : [];
        $review->platform_played_on = $request->platform_played_on;
        $review->podcast_id = $request->podcast_id;
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

    /**
     * Toggle like for a review (AJAX).
     */
    public function toggleLike(Product $product, Review $review)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        if ($review->product_id !== $product->id) {
            return response()->json(['error' => 'Invalid review for this product'], 404);
        }
        $user = auth()->user();
        $liked = $review->isLikedBy($user);
        if ($liked) {
            $review->likes()->detach($user->id);
        } else {
            $review->likes()->attach($user->id);
        }
        $newCount = $review->likes()->count();
        return response()->json([
            'liked' => !$liked,
            'likes_count' => $newCount,
        ]);
    }

    /**
     * Get available podcasts for the current user to post reviews as
     */
    private function getAvailablePodcasts()
    {
        $user = Auth::user();
        if (!$user) {
            return collect();
        }

        // Get podcasts where user is owner or an accepted team member with posting rights
        $availablePodcasts = Podcast::where('status', 'approved')
            ->where(function ($query) use ($user) {
                $query->where('owner_id', $user->id)
                      ->orWhereHas('teamMembers', function ($q) use ($user) {
                          $q->where('user_id', $user->id)
                            ->where('role', '!=', 'guest') // Check role instead
                            ->whereNotNull('accepted_at');
                      });
            })
            ->get();
        
        return $availablePodcasts;
    }

    // Episode Review Methods

    /**
     * Show the form for creating a new episode review.
     */
    public function createEpisodeReview(Podcast $podcast, Episode $episode)
    {
        // Verify episode belongs to podcast
        if ($episode->podcast_id !== $podcast->id) {
            abort(404);
        }

        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to write a review.');
        }

        // Check if user already has a review for this episode
        $existingReview = Review::where('episode_id', $episode->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            return redirect()->route('podcasts.episodes.reviews.edit', [$podcast, $episode, $existingReview])
                ->with('info', 'You already have a review for this episode. You can edit it here.');
        }
        
        return view('reviews.create-episode', compact('podcast', 'episode'));
    }

    /**
     * Store a newly created episode review in storage.
     */
    public function storeEpisodeReview(Request $request, Podcast $podcast, Episode $episode)
    {
        // Verify episode belongs to podcast
        if ($episode->podcast_id !== $podcast->id) {
            abort(404);
        }

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to write a review.');
        }

        // Check if user already has a review for this episode
        $existingReview = Review::where('episode_id', $episode->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            return redirect()->route('podcasts.episodes.reviews.edit', [$podcast, $episode, $existingReview])
                ->with('error', 'You already have a review for this episode.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:10',
        ]);

        // Create new review
        $review = new Review();
        $review->episode_id = $episode->id;
        $review->podcast_id = $podcast->id;
        $review->user_id = Auth::id();
        $review->title = $request->title;
        $review->content = $request->content;
        $review->rating = $request->rating;
        $review->is_published = true;
        // product_id is intentionally left null for episode reviews
        $review->save();

        // Notify podcast owner
        $podcastOwner = $podcast->owner;
        if ($podcastOwner->id !== Auth::id()) { // Don't notify if commenter is the owner
            // We pass all the data the notification needs so it doesn't have to touch the DB in the queue
            $podcastOwner->notify(new NewPodcastComment(
                $review->id,
                $episode->title,
                $episode->slug,
                $podcast->slug,
                Auth::user()->name,
                Auth::id()
            ));
        }

        return redirect()->route('podcasts.episodes.show', [$podcast, $episode])
            ->with('success', 'Your episode review has been published successfully!');
    }

    /**
     * Display the specified episode review.
     */
    public function showEpisodeReview(Podcast $podcast, Episode $episode, Review $review)
    {
        // Verify episode belongs to podcast
        if ($episode->podcast_id !== $podcast->id) {
            abort(404);
        }

        // Verify the review belongs to the episode
        if ($review->episode_id !== $episode->id) {
            abort(404);
        }

        // Load relationships
        $review->load(['user', 'podcast', 'episode']);

        // Check if review is published or user owns it
        if (!$review->is_published && (!Auth::check() || Auth::id() !== $review->user_id)) {
            abort(404);
        }

        return view('reviews.show-episode', compact('review', 'podcast', 'episode'));
    }

    /**
     * Show the form for editing the specified episode review.
     */
    public function editEpisodeReview(Podcast $podcast, Episode $episode, Review $review)
    {
        // Verify episode belongs to podcast
        if ($episode->podcast_id !== $podcast->id) {
            abort(404);
        }

        // Verify the review belongs to the episode
        if ($review->episode_id !== $episode->id) {
            abort(404);
        }

        // Check if user can edit this review
        if (!Auth::check() || (Auth::id() !== $review->user_id && !Auth::user()->is_admin)) {
            abort(403);
        }

        return view('reviews.edit-episode', compact('review', 'podcast', 'episode'));
    }

    /**
     * Update the specified episode review in storage.
     */
    public function updateEpisodeReview(Request $request, Podcast $podcast, Episode $episode, Review $review)
    {
        // Verify episode belongs to podcast
        if ($episode->podcast_id !== $podcast->id) {
            abort(404);
        }

        // Verify the review belongs to the episode
        if ($review->episode_id !== $episode->id) {
            abort(404);
        }

        // Check if user can edit this review
        if (!Auth::check() || (Auth::id() !== $review->user_id && !Auth::user()->is_admin)) {
            abort(403);
        }

        $request->validate([
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:10',
        ]);

        $review->content = $request->content;
        $review->rating = $request->rating;
        $review->save();

        return redirect()->route('podcasts.episodes.show', [$podcast, $episode])->with('success', 'Review updated successfully!');
    }

    /**
     * Remove the specified episode review from storage.
     */
    public function destroyEpisodeReview(Podcast $podcast, Episode $episode, Review $review)
    {
        // Verify episode belongs to podcast
        if ($episode->podcast_id !== $podcast->id) {
            abort(404);
        }

        // Verify the review belongs to the episode
        if ($review->episode_id !== $episode->id) {
            abort(404);
        }

        // Check if user can delete this review
        if (!Auth::check() || (Auth::id() !== $review->user_id && !Auth::user()->is_admin)) {
            abort(403);
        }

        $review->delete();

        return redirect()->route('podcasts.episodes.show', [$podcast, $episode])
            ->with('success', 'Episode review deleted successfully.');
    }
}
