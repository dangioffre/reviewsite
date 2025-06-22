<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Store a new report for a review.
     */
    public function store(Request $request, Product $product, Review $review)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to report a review.');
        }

        // Verify the review belongs to the product
        if ($review->product_id !== $product->id) {
            abort(404);
        }

        // Check if user has already reported this review
        $existingReport = Report::where('review_id', $review->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReport) {
            $showRoute = $product->type === 'game' ? 'games.reviews.show' : 'tech.reviews.show';
            return redirect()->route($showRoute, [$product, $review])
                ->with('error', 'You have already reported this review.');
        }

        // Validate the request
        $request->validate([
            'reason' => 'required|string|in:' . implode(',', array_keys(Report::getReasons())),
            'additional_info' => 'nullable|string|max:1000',
        ]);

        // Create the report
        Report::create([
            'review_id' => $review->id,
            'user_id' => Auth::id(),
            'reason' => $request->reason,
            'additional_info' => $request->additional_info,
            'status' => 'pending',
        ]);

        $showRoute = $product->type === 'game' ? 'games.reviews.show' : 'tech.reviews.show';
        return redirect()->route($showRoute, [$product, $review])
            ->with('success', 'Report submitted successfully. We will review it shortly.');
    }

    /**
     * Show the report form modal (AJAX endpoint).
     */
    public function show(Product $product, Review $review)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Verify the review belongs to the product
        if ($review->product_id !== $product->id) {
            return response()->json(['error' => 'Review not found'], 404);
        }

        // Check if user has already reported this review
        $existingReport = Report::where('review_id', $review->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReport) {
            return response()->json(['error' => 'You have already reported this review'], 409);
        }

        return response()->json([
            'reasons' => Report::getReasons(),
            'review_title' => $review->title,
            'review_author' => $review->user->name,
        ]);
    }

    /**
     * Admin action to approve a report (delete the review).
     */
    public function approve(Request $request, Report $report)
    {
        // Check if user is admin
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403);
        }

        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $report->approve(Auth::id(), $request->admin_notes);

        return redirect()->back()->with('success', 'Report approved and review deleted.');
    }

    /**
     * Admin action to deny a report (keep the review).
     */
    public function deny(Request $request, Report $report)
    {
        // Check if user is admin
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403);
        }

        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $report->deny(Auth::id(), $request->admin_notes);

        return redirect()->back()->with('success', 'Report denied and review kept.');
    }
}
