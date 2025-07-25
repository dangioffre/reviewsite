<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ReviewVisibilityController extends Controller
{
    /**
     * Update review visibility settings
     */
    public function updateVisibility(Request $request, Review $review): JsonResponse
    {
        // Check if user owns this review
        if ($review->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to modify this review.'
            ], 403);
        }

        $request->validate([
            'type' => 'required|in:streamer,podcast',
            'visible' => 'required|boolean'
        ]);

        $type = $request->input('type');
        $visible = $request->input('visible');

        try {
            if ($type === 'streamer') {
                // Only update if review is actually associated with a streamer profile
                if ($review->streamer_profile_id) {
                    $review->show_on_streamer_profile = $visible;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'This review is not associated with a streamer profile.'
                    ], 400);
                }
            } elseif ($type === 'podcast') {
                // Only update if review is actually associated with a podcast
                if ($review->podcast_id) {
                    $review->show_on_podcast = $visible;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'This review is not associated with a podcast.'
                    ], 400);
                }
            }

            $review->save();

            return response()->json([
                'success' => true,
                'message' => 'Review visibility updated successfully.',
                'data' => [
                    'review_id' => $review->id,
                    'type' => $type,
                    'visible' => $visible
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating visibility.'
            ], 500);
        }
    }
}