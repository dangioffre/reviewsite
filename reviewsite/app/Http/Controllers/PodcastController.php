<?php

namespace App\Http\Controllers;

use App\Models\Podcast;
use App\Models\Episode;
use App\Models\User;
use App\Models\Review;
use App\Services\RssVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PodcastController extends Controller
{
    protected $rssService;

    public function __construct(RssVerificationService $rssService)
    {
        $this->rssService = $rssService;
    }

    /**
     * Display podcast submission form
     */
    public function create()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to submit a podcast.');
        }

        // Check if user already has a pending podcast
        $existingPendingPodcast = Auth::user()->podcasts()->where('status', 'pending')->first();

        return view('podcasts.create', compact('existingPendingPodcast'));
    }

    /**
     * Store a new podcast submission
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to submit a podcast.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'rss_url' => 'required|url|max:500',
            'description' => 'nullable|string|max:1000',
            'website_url' => 'nullable|url|max:500',
            'hosts' => 'nullable|string|max:500',
        ]);

        // Validate RSS feed
        $rssValidation = $this->rssService->validateRssFeed($request->rss_url);
        
        if (!$rssValidation['valid']) {
            return back()->withErrors([
                'rss_url' => 'RSS feed validation failed: ' . $rssValidation['error']
            ])->withInput();
        }

        // Check if RSS URL is already registered
        if (Podcast::where('rss_url', $request->rss_url)->exists()) {
            return back()->withErrors([
                'rss_url' => 'This RSS feed is already registered.'
            ])->withInput();
        }

        // Create podcast
        $podcast = Podcast::create([
            'owner_id' => Auth::id(),
            'name' => $request->name,
            'rss_url' => $request->rss_url,
            'description' => $request->description ?: $rssValidation['description'],
            'website_url' => $request->website_url,
            'hosts' => $request->hosts ? array_map('trim', explode(',', $request->hosts)) : null,
            'status' => Podcast::STATUS_PENDING,
        ]);

        // Update podcast info from RSS if we got valid data
        $this->rssService->updatePodcastInfo($podcast);

        return redirect()->route('podcasts.verify', $podcast)
            ->with('success', 'Podcast submitted successfully! Please follow the verification steps below.');
    }

    /**
     * Show verification instructions
     */
    public function verify(Podcast $podcast)
    {
        // Check if user owns this podcast
        if (!Auth::check() || Auth::id() !== $podcast->owner_id) {
            abort(403, 'Unauthorized to view this podcast verification.');
        }

        return view('podcasts.verify', compact('podcast'));
    }

    /**
     * Fetch podcast info from RSS feed
     */
    public function fetchRssInfo(Request $request)
    {
        $request->validate(['rss_url' => 'required|url']);

        $rssUrl = $request->input('rss_url');
        
        $podcastInfo = $this->rssService->validateRssFeed($rssUrl);

        if (!$podcastInfo['valid']) {
            return response()->json(['success' => false, 'error' => $podcastInfo['error'] ?? 'Could not validate RSS feed.']);
        }

        return response()->json(['success' => true, 'data' => $podcastInfo]);
    }

    /**
     * Check verification status
     */
    public function checkVerification(Podcast $podcast)
    {
        if (!Auth::check() || Auth::id() !== $podcast->owner_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $verified = $this->rssService->verifyToken($podcast);

        if ($verified) {
            return response()->json([
                'success' => true,
                'message' => 'Verification successful! Your podcast has been verified and is now pending admin approval.',
                'redirect' => route('podcasts.dashboard')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Verification token not found in RSS feed. Please ensure you have added the token to your podcast description or latest episode description.',
            'error' => $podcast->rss_error
        ]);
    }

    /**
     * Display podcast profile
     */
    public function show(Podcast $podcast)
    {
        // Only show approved podcasts to public, or owned/team member podcasts
        if (!$podcast->isApproved() && (!Auth::check() || !$podcast->userCanPostAsThisPodcast(Auth::user()))) {
            abort(404);
        }

        $podcast->load(['owner', 'activeTeamMembers.user']);

        // Paginate all episodes instead of loading recent ones
        $episodes = $podcast->episodes()
            ->published()
            ->recent()
            ->paginate(10);

        // Get recently attached reviews (reviews that have been attached to episodes)
        $recentAttachedReviews = Review::select('reviews.*', 'episodes.title as episode_title', 'episodes.id as episode_id')
            ->join('episode_review_attachments', 'reviews.id', '=', 'episode_review_attachments.review_id')
            ->join('episodes', 'episode_review_attachments.episode_id', '=', 'episodes.id')
            ->where('episodes.podcast_id', $podcast->id)
            ->where('reviews.is_published', true)
            ->where('reviews.show_on_podcast', true) // Only show reviews that are set to be visible on podcast
            ->whereHas('product') // Ensure the review has a valid product
            ->with('user', 'product')
            ->orderBy('episode_review_attachments.created_at', 'desc')
            ->limit(5)
            ->get();

        $totalEpisodes = $podcast->episodes()->published()->count();
        $totalReviews = Review::join('episode_review_attachments', 'reviews.id', '=', 'episode_review_attachments.review_id')
            ->join('episodes', 'episode_review_attachments.episode_id', '=', 'episodes.id')
            ->where('episodes.podcast_id', $podcast->id)
            ->where('reviews.is_published', true)
            ->where('reviews.show_on_podcast', true) // Only count reviews that are set to be visible on podcast
            ->count();

        return view('podcasts.show', compact('podcast', 'episodes', 'totalEpisodes', 'totalReviews', 'recentAttachedReviews'));
    }

    /**
     * Display episode page
     */
    public function showEpisode(Podcast $podcast, Episode $episode)
    {
        // Verify episode belongs to podcast
        if ($episode->podcast_id !== $podcast->id) {
            abort(404);
        }

        // Only show episodes from approved podcasts
        if (!$podcast->isApproved()) {
            abort(404);
        }

        // Eager load relationships
        $episode->load('podcast.owner');

        // Fetch reviews written specifically for this episode
        $episodeReviews = Review::where('episode_id', $episode->id)
            ->whereNull('product_id')
            ->with('user')
            ->latest()
            ->get();

        // Fetch product reviews that are attached to this episode
        $attachedReviews = $episode->attachedReviews()->with(['product', 'user'])->get();

        // Get the current user's review for this episode, if it exists
        $userReview = null;
        if (Auth::check()) {
            $userReview = Review::where('episode_id', $episode->id)
                ->where('user_id', Auth::id())
                ->whereNull('product_id')
                ->first();
        }

        // Get available reviews for attachment if user is team member
        $availableReviews = collect();
        if (Auth::check() && $podcast->userCanPostAsThisPodcast(Auth::user())) {
            $availableReviews = $episode->getAvailableReviewsForUser(Auth::user());
        }

        // Get recent episodes from the same podcast (excluding current episode)
        $recentEpisodes = $podcast->episodes()
            ->where('id', '!=', $episode->id)
            ->published()
            ->recent()
            ->limit(5)
            ->get();

        return view('podcasts.episode', compact('podcast', 'episode', 'episodeReviews', 'attachedReviews', 'availableReviews', 'recentEpisodes', 'userReview'));
    }

    /**
     * Update podcast links
     */
    public function updateLinks(Request $request, Podcast $podcast)
    {
        if (!Auth::check() || Auth::id() !== $podcast->owner_id) {
            abort(403);
        }

        $validated = $request->validate([
            'links' => 'array',
            'links.*.platform' => 'required|string|max:100',
            'links.*.url' => 'required|url|max:500',
        ]);

        $podcast->update(['links' => $validated['links']]);

        return back()->with('success', 'Podcast links updated successfully!');
    }

    /**
     * Delete a podcast and its related data.
     */
    public function destroy(Podcast $podcast)
    {
        // Ensure only the owner can delete the podcast
        if (!Auth::check() || Auth::id() !== $podcast->owner_id) {
            abort(403, 'Unauthorized action.');
        }

        // Get all episode IDs for the podcast
        $episodeIds = $podcast->episodes()->pluck('id');

        // Delete episode-specific reviews (reviews not tied to a product)
        Review::whereIn('episode_id', $episodeIds)->whereNull('product_id')->delete();
        
        // Detach product reviews from episodes
        $podcast->episodes()->with('attachedReviews')->get()->each(function ($episode) {
            $episode->attachedReviews()->detach();
        });

        // Delete episodes
        $podcast->episodes()->delete();

        // Delete team members and invitations
        $podcast->teamMembers()->delete();

        // Finally, delete the podcast itself
        $podcast->delete();

        return redirect()->route('podcasts.dashboard')->with('success', 'Podcast has been successfully deleted.');
    }

    /**
     * Attach a review to an episode
     */
    public function attachReview(Podcast $podcast, Episode $episode, Request $request)
    {
        // Verify episode belongs to podcast
        if ($episode->podcast_id !== $podcast->id) {
            abort(404);
        }

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'review_id' => 'required|exists:reviews,id'
        ]);

        $review = Review::findOrFail($request->review_id);

        if ($episode->attachReview($review, Auth::user())) {
            return redirect()->route('podcasts.episodes.show', [$podcast, $episode])
                ->with('success', 'Review attached to episode successfully!');
        }

        return redirect()->route('podcasts.episodes.show', [$podcast, $episode])
            ->with('error', 'Unable to attach review to episode.');
    }

    /**
     * Detach a review from an episode
     */
    public function detachReview(Podcast $podcast, Episode $episode, Review $review)
    {
        // Verify episode belongs to podcast
        if ($episode->podcast_id !== $podcast->id) {
            abort(404);
        }

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if ($episode->detachReview($review, Auth::user())) {
            return redirect()->route('podcasts.episodes.show', [$podcast, $episode])
                ->with('success', 'Review detached from episode successfully!');
        }

        return redirect()->route('podcasts.episodes.show', [$podcast, $episode])
            ->with('error', 'Unable to detach review from episode.');
    }

    /**
     * Display all podcasts (public index)
     */
    public function index(Request $request)
    {
        $query = Podcast::approved()
            ->with('owner', 'episodes')
            ->withCount(['episodes', 'reviews']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('owner', function ($ownerQuery) use ($search) {
                      $ownerQuery->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereJsonContains('hosts', $search);
            });
        }

        // Featured filter
        if ($request->filled('featured')) {
            $query->where('is_featured', $request->featured === 'true');
        }

        // Host filter
        if ($request->filled('host')) {
            $query->whereJsonContains('hosts', $request->host);
        }

        // Sorting
        switch ($request->get('sort', 'newest')) {
            case 'oldest':
                $query->orderBy('approved_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'episodes_high':
                $query->orderBy('episodes_count', 'desc');
                break;
            case 'episodes_low':
                $query->orderBy('episodes_count', 'asc');
                break;
            case 'reviews_high':
                $query->orderBy('reviews_count', 'desc');
                break;
            case 'reviews_low':
                $query->orderBy('reviews_count', 'asc');
                break;
            default:
                $query->orderBy('approved_at', 'desc');
        }

        $podcasts = $query->paginate(12)->withQueryString();

        // Get unique hosts for filter dropdown
        $hosts = Podcast::approved()
            ->whereNotNull('hosts')
            ->pluck('hosts')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        return view('podcasts.index', compact('podcasts', 'hosts'));
    }

    /**
     * User's podcast dashboard
     */
    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $ownedPodcasts = Auth::user()->podcasts()
            ->with(['episodes', 'activeTeamMembers', 'pendingTeamMembers'])
            ->get();
        
        $teamMemberships = Auth::user()->activePodcastTeamMemberships()
            ->with('podcast.episodes')
            ->get();

        $pendingInvitations = Auth::user()->pendingPodcastInvitations()
            ->with('podcast')
            ->get();

        return view('dashboard.podcasts', compact('ownedPodcasts', 'teamMemberships', 'pendingInvitations'));
    }

    /**
     * Manual RSS sync (rate limited)
     */
    public function syncRss(Podcast $podcast)
    {
        if (!Auth::check() || !$podcast->userCanPostAsThisPodcast(Auth::user())) {
            return back()->with('error', 'You are not authorized to sync this podcast.');
        }

        try {
            $count = $this->rssService->importEpisodes($podcast);

            if ($count > 0) {
                return back()->with('success', "Successfully imported {$count} new episodes.");
            } else {
                return back()->with('success', 'No new episodes found. Your podcast is up to date.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to sync RSS feed: ' . $e->getMessage());
        }
    }
} 