<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the home page.
     */
    public function index(): View
    {
        $featuredPosts = Post::where('is_featured', true)
            ->latest()
            ->take(5)
            ->get();
            
        return view('home', [
            'featuredPosts' => $featuredPosts,
        ]);
    }
} 