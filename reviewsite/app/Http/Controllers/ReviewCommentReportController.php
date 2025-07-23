<?php

namespace App\Http\Controllers;

use App\Models\ReviewComment;
use App\Models\ReviewCommentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewCommentReportController extends Controller
{
    public function store(Request $request, ReviewComment $comment)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Prevent duplicate reports
        $existing = ReviewCommentReport::where('review_comment_id', $comment->id)
            ->where('user_id', Auth::id())
            ->first();
        if ($existing) {
            return response()->json(['error' => 'You have already reported this comment.'], 409);
        }

        $request->validate([
            'reason' => 'required|string|max:255',
            'details' => 'nullable|string|max:1000',
        ]);

        ReviewCommentReport::create([
            'review_comment_id' => $comment->id,
            'user_id' => Auth::id(),
            'reason' => $request->reason,
            'details' => $request->details,
            'resolved' => false,
        ]);

        return response()->json(['success' => true, 'message' => 'Report submitted.']);
    }
}
