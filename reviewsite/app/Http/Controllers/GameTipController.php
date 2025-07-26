<?php

namespace App\Http\Controllers;

use App\Models\GameTip;
use App\Models\GameTipCategory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class GameTipController extends Controller
{
    public function index(Product $product)
    {
        $tips = $product->gameTips()
            ->with(['user', 'category', 'comments.user', 'likes'])
            ->approved()
            ->orderBy('likes_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $categories = GameTipCategory::all();

        return view('games.tips.index', compact('product', 'tips', 'categories'));
    }

    public function store(Request $request, Product $product)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:10000',
            'game_tip_category_id' => 'required|exists:game_tip_categories,id',
            'youtube_link' => 'nullable|url',
            'tags' => 'nullable|array',
            'tags.*' => 'string|in:Spoiler,Patch Dependent,Outdated,Beginner,Advanced,Exploit',
        ]);

        $tip = $product->gameTips()->create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'game_tip_category_id' => $request->game_tip_category_id,
            'youtube_link' => $request->youtube_link,
            'tags' => $request->tags,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Tip submitted successfully! It will be reviewed by our team.');
    }



    public function comment(Request $request, GameTip $tip)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = $tip->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
            'status' => 'approved',
        ]);

        $tip->increment('comments_count');

        return redirect()->back()->with('success', 'Comment posted successfully!');
    }

    public function show(Product $product, GameTip $tip)
    {
        $tip->load(['user', 'category', 'comments.user', 'likes']);
        
        return view('games.tips.show', compact('product', 'tip'));
    }
}
