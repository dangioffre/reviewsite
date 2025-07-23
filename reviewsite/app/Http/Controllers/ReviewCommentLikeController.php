<?php

namespace App\Http\Controllers;

use App\Models\ReviewComment;
use App\Models\ReviewCommentLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewCommentLikeController extends Controller
{
    public function toggle(Request $request, ReviewComment $comment)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = Auth::user();
        $like = $comment->likes()->where('user_id', $user->id)->first();
        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            $comment->likes()->create(['user_id' => $user->id]);
            $liked = true;
        }
        return response()->json([
            'liked' => $liked,
            'likes_count' => $comment->likes()->count(),
        ]);
    }
}
