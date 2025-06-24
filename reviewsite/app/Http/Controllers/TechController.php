<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Models\Genre;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TechController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['genre', 'platform', 'reviews'])
            ->whereIn('type', ['hardware', 'accessory']);

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Category filter
        if ($request->filled('category')) {
            if ($request->category === 'hardware') {
                $query->where('type', 'hardware');
            } elseif ($request->category === 'accessories') {
                $query->where('type', 'accessory');
            }
        }

        // Genre filter (for categorization)
        if ($request->filled('genre')) {
            $query->whereHas('genre', function ($q) use ($request) {
                $q->where('slug', $request->genre);
            });
        }

        // Platform filter (for compatibility)
        if ($request->filled('platform')) {
            $query->whereHas('platform', function ($q) use ($request) {
                $q->where('slug', $request->platform);
            });
        }

        // Hardware compatibility filter (for accessories)
        // Note: This filter was removed as we consolidated hardware into the Product model

        // Score range filter
        if ($request->filled('score_range')) {
            $ranges = explode('-', $request->score_range);
            if (count($ranges) == 2) {
                $query->whereBetween('staff_rating', [$ranges[0], $ranges[1]]);
            }
        }

        // Sorting
        switch ($request->get('sort', 'latest')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'rating_high':
                $query->orderBy('staff_rating', 'desc');
                break;
            case 'rating_low':
                $query->orderBy('staff_rating', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12);
        $genres = Genre::active()->get();
        $platforms = Platform::active()->get();

        return view('tech.index', compact('products', 'genres', 'platforms'));
    }

    public function show(Product $product)
    {
        // Ensure the product is hardware or accessory
        if (!in_array($product->type, ['hardware', 'accessory'])) {
            abort(404);
        }

        $product->load(['genre', 'platform', 'reviews.user']);
        
        // Separate staff and user reviews
        $staffReviews = $product->reviews->where('is_staff_review', true);
        $userReviews = $product->reviews->where('is_staff_review', false)->sortByDesc('created_at');
        
        // Calculate average user rating
        $averageUserRating = $userReviews->avg('rating');
        
        // Check if current user has already rated this product
        $userRating = null;
        if (Auth::check()) {
            $userRating = $product->reviews()
                ->where('user_id', Auth::id())
                ->where('is_staff_review', false)
                ->value('rating');
        }
        
        return view('tech.show', compact('product', 'staffReviews', 'userReviews', 'averageUserRating', 'userRating'));
    }

    public function rate(Request $request, Product $product)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Please login to rate this product'], 401);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:10',
        ]);

        // Check if user already has a rating for this product
        $existingReview = Review::where('product_id', $product->id)
            ->where('user_id', Auth::id())
            ->where('is_staff_review', false)
            ->first();

        if ($existingReview) {
            // Update existing rating
            $existingReview->rating = $request->rating;
            $existingReview->save();
            
            // Determine message based on review type
            $message = $existingReview->title === 'Quick Rating' 
                ? 'Rating updated successfully!' 
                : 'Your review rating has been updated!';
        } else {
            // Create new rating (simple review with just rating)
            Review::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'title' => 'Quick Rating',
                'content' => 'User rating via star system',
                'rating' => $request->rating,
                'is_staff_review' => false,
                'is_published' => true,
            ]);
            
            $message = 'Rating submitted successfully!';
        }

        // Recalculate the community rating
        $communityRating = $product->reviews()
            ->where('is_staff_review', false)
            ->avg('rating');

        $communityCount = $product->reviews()
            ->where('is_staff_review', false)
            ->count();

        return response()->json([
            'success' => true,
            'message' => $message,
            'communityRating' => round($communityRating, 1),
            'communityCount' => $communityCount,
            'userRating' => $request->rating
        ]);
    }

    public function byCategory(Genre $genre)
    {
        $products = Product::with(['genre', 'platform', 'reviews'])
            ->whereIn('type', ['hardware', 'accessory'])
            ->where(function($query) use ($genre) {
                $query->where('genre_id', $genre->id)
                      ->orWhereJsonContains('genres', $genre->name);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $genres = Genre::active()->get();
        $platforms = Platform::active()->get();
        $filterType = 'Category';
        $filterValue = $genre->name;

        return view('tech.index', compact('products', 'genres', 'platforms', 'filterType', 'filterValue'));
    }

    public function byPlatform(Platform $platform)
    {
        $products = Product::with(['genre', 'platform', 'reviews'])
            ->whereIn('type', ['hardware', 'accessory'])
            ->where(function($query) use ($platform) {
                $query->where('platform_id', $platform->id)
                      ->orWhereJsonContains('platforms', $platform->name);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $genres = Genre::active()->get();
        $platforms = Platform::active()->get();
        $filterType = 'Platform';
        $filterValue = $platform->name;

        return view('tech.index', compact('products', 'genres', 'platforms', 'filterType', 'filterValue'));
    }

    public function byBrand($developer)
    {
        $products = Product::with(['genre', 'platform', 'reviews'])
            ->whereIn('type', ['hardware', 'accessory'])
            ->where(function($query) use ($developer) {
                $query->where('developer', $developer)
                      ->orWhereJsonContains('developers', $developer);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $genres = Genre::active()->get();
        $platforms = Platform::active()->get();
        $filterType = 'Brand';
        $filterValue = $developer;

        return view('tech.index', compact('products', 'genres', 'platforms', 'filterType', 'filterValue'));
    }

    public function byPublisher($publisher)
    {
        $products = Product::with(['genre', 'platform', 'reviews'])
            ->whereIn('type', ['hardware', 'accessory'])
            ->where(function($query) use ($publisher) {
                $query->where('publisher', $publisher)
                      ->orWhereJsonContains('publishers', $publisher);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $genres = Genre::active()->get();
        $platforms = Platform::active()->get();
        $filterType = 'Publisher';
        $filterValue = $publisher;

        return view('tech.index', compact('products', 'genres', 'platforms', 'filterType', 'filterValue'));
    }

    public function byTheme($theme)
    {
        $products = Product::with(['genre', 'platform', 'reviews'])
            ->whereIn('type', ['hardware', 'accessory'])
            ->where(function($query) use ($theme) {
                $query->where('theme', $theme)
                      ->orWhereJsonContains('themes', $theme);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $genres = Genre::active()->get();
        $platforms = Platform::active()->get();
        $filterType = 'Theme';
        $filterValue = $theme;

        return view('tech.index', compact('products', 'genres', 'platforms', 'filterType', 'filterValue'));
    }
} 