<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\ReviewComment;
use Illuminate\Support\Facades\Auth;

class ReviewCommentController extends Controller
{
    public function store(Request $request, $product, Review $review)
    {
        $request->validate([
            'content' => 'required|string|min:2|max:2000',
            'parent_id' => 'nullable|exists:review_comments,id',
        ]);

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $comment = new ReviewComment([
            'content' => $request->input('content'),
            'parent_id' => $request->input('parent_id'),
        ]);
        $comment->user_id = Auth::id();
        $comment->review_id = $review->id;
        $comment->save();

        return redirect()->back()->with('success', 'Comment posted!');
    }
}
