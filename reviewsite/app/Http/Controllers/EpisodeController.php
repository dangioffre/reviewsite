<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\Podcast;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EpisodeController extends Controller
{
    /**
     * Display a listing of episodes for review management.
     */
    public function index(Podcast $podcast)
    {
        // Check if user can manage episodes for this podcast
        if (!$podcast->userCanPostAsThisPodcast(Auth::user())) {
            abort(403, 'You do not have permission to manage episodes for this podcast.');
        }

        $episodes = $podcast->episodes()
            ->withCount(['reviews', 'attachedReviews'])
            ->with('attachedReviews.product', 'attachedReviews.user')
            ->orderBy('published_at', 'desc')
            ->orderBy('episode_number', 'desc')
            ->paginate(20);

        \Log::info('Episodes index loaded', [
            'podcast_slug' => $podcast->slug,
            'podcast_id' => $podcast->id,
            'episodes_count' => $episodes->count(),
            'first_episode_slug' => $episodes->count() > 0 ? $episodes->first()->slug : null
        ]);

        return view('episodes.index', compact('podcast', 'episodes'));
    }

    /**
     * Get available reviews for episode attachment
     */
    public function getAvailableReviews(Podcast $podcast, Episode $episode)
    {
        \Log::info('getAvailableReviews called', [
            'podcast_slug' => $podcast->slug,
            'episode_slug' => $episode->slug,
            'user_id' => Auth::id()
        ]);
        
        try {
            // Verify episode belongs to podcast
            if ($episode->podcast_id !== $podcast->id) {
                \Log::error('Episode does not belong to podcast', [
                    'episode_podcast_id' => $episode->podcast_id,
                    'podcast_id' => $podcast->id
                ]);
                return response()->json(['success' => false, 'message' => 'Episode not found'], 404);
            }

            // Check if user can manage episodes for this podcast
            if (!$podcast->userCanPostAsThisPodcast(Auth::user())) {
                \Log::error('User cannot manage podcast', [
                    'user_id' => Auth::id(),
                    'podcast_id' => $podcast->id
                ]);
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            // Get all team members
            $teamMembers = $podcast->activeTeamMembers()->pluck('user_id')
                              ->push($podcast->owner_id)
                              ->unique();

            // Get all reviews by team members
            $allReviews = Review::whereIn('user_id', $teamMembers)
                        ->whereNull('episode_id') // Only product reviews
                        ->where('is_published', true)
                        ->where('show_on_podcast', true) // Only reviews visible on podcast
                        ->with(['product', 'user'])
                        ->orderBy('created_at', 'desc')
                        ->get();

            \Log::info('Found reviews for episode', [
                'podcast_id' => $podcast->id,
                'episode_slug' => $episode->slug,
                'team_members_count' => $teamMembers->count(),
                'reviews_count' => $allReviews->count()
            ]);

            // Get currently attached review IDs
            $attachedReviewIds = $episode->attachedReviews()->pluck('review_id')->toArray();

            // Format reviews for frontend
            $reviews = $allReviews->map(function ($review) use ($attachedReviewIds) {
                return [
                    'id' => $review->id,
                    'title' => $review->title,
                    'rating' => $review->rating,
                    'created_at' => $review->created_at->toISOString(),
                    'is_attached' => in_array($review->id, $attachedReviewIds),
                    'product' => [
                        'id' => $review->product->id,
                        'name' => $review->product->name,
                        'type' => $review->product->type,
                    ],
                    'user' => [
                        'id' => $review->user->id,
                        'name' => $review->user->name,
                    ],
                ];
            });

            return response()->json([
                'success' => true,
                'reviews' => $reviews
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getAvailableReviews: ' . $e->getMessage(), [
                'podcast_id' => $podcast->id,
                'episode_id' => $episode->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Failed to load reviews: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get currently attached reviews for an episode
     */
    public function getAttachedReviews(Podcast $podcast, Episode $episode)
    {
        try {
            // Verify episode belongs to podcast
            if ($episode->podcast_id !== $podcast->id) {
                return response()->json(['success' => false, 'message' => 'Episode not found'], 404);
            }

            // Check if user can manage episodes for this podcast
            if (!$podcast->userCanPostAsThisPodcast(Auth::user())) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $attachedReviews = $episode->attachedReviews()
                ->with(['product', 'user'])
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'title' => $review->title,
                        'rating' => $review->rating,
                        'created_at' => $review->created_at->toISOString(),
                        'product' => [
                            'id' => $review->product->id,
                            'name' => $review->product->name,
                            'type' => $review->product->type,
                        ],
                        'user' => [
                            'id' => $review->user->id,
                            'name' => $review->user->name,
                        ],
                    ];
                });

            return response()->json([
                'success' => true,
                'reviews' => $attachedReviews
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getAttachedReviews: ' . $e->getMessage(), [
                'podcast_id' => $podcast->id,
                'episode_id' => $episode->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Failed to load attached reviews: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk attach/detach reviews to/from episode
     */
    public function bulkReviewAction(Request $request, Podcast $podcast, Episode $episode)
    {
        try {
            // Verify episode belongs to podcast
            if ($episode->podcast_id !== $podcast->id) {
                return response()->json(['success' => false, 'message' => 'Episode not found'], 404);
            }

            // Check if user can manage episodes for this podcast
            if (!$podcast->userCanPostAsThisPodcast(Auth::user())) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'action' => 'required|in:attach,detach',
                'review_ids' => 'required|array',
                'review_ids.*' => 'integer|exists:reviews,id',
            ]);

            $action = $validated['action'];
            $reviewIds = $validated['review_ids'];
            $successCount = 0;

            foreach ($reviewIds as $reviewId) {
                $review = Review::find($reviewId);
                
                if (!$review) {
                    continue;
                }

                if ($action === 'attach') {
                    if ($episode->canAttachReview($review, Auth::user())) {
                        $episode->attachReview($review, Auth::user());
                        $successCount++;
                    }
                } else { // detach
                    if ($episode->attachedReviews()->where('review_id', $reviewId)->exists()) {
                        $episode->detachReview($review, Auth::user());
                        $successCount++;
                    }
                }
            }

            $actionName = $action === 'attach' ? 'attached' : 'detached';
            
            return response()->json([
                'success' => true,
                'message' => "{$successCount} reviews {$actionName} successfully",
                'processed_count' => $successCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in bulkReviewAction: ' . $e->getMessage(), [
                'podcast_id' => $podcast->id,
                'episode_id' => $episode->id,
                'action' => $request->input('action'),
                'review_ids' => $request->input('review_ids'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Failed to process reviews: ' . $e->getMessage()
            ], 500);
        }
    }
}