<?php

namespace App\Http\Controllers;

use App\Models\Keyword;
use Illuminate\Http\Request;

class KeywordController extends Controller
{
    public function show($slug)
    {
        $keyword = Keyword::where('slug', $slug)->firstOrFail();
        $games = $keyword->products()->where('type', 'game')->with('genre', 'developers')->get();
        return view('keywords.show', compact('keyword', 'games'));
    }
} 