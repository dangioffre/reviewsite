<?php

namespace App\Http\Controllers;

use App\Http\Requests\StreamerProfileStoreRequest;
use App\Http\Requests\StreamerProfileUpdateRequest;
use App\Models\StreamerProfile;
use App\Models\StreamerVod;
use App\Services\PlatformApiService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StreamerProfileController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of approved streamer profiles with filtering and sorting.
     */
    public function index(Request $request): View
    {
        $query = StreamerProfile::approved()
            ->with(['user', 'schedules', 'vods']);

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('channel_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('bio', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Platform filtering
        if ($request->filled('platform')) {
            $query->platform($request->platform);
        }

        // Live status filtering
        if ($request->filled('live_status')) {
            if ($request->live_status === 'live') {
                $query->live();
            } elseif ($request->live_status === 'offline') {
                $query->where(function($q) {
                    $q->where(function($subQ) {
                        $subQ->where('manual_live_override', false)
                             ->orWhere(function($innerQ) {
                                 $innerQ->whereNull('manual_live_override')
                                        ->where('is_live', false);
                             });
                    });
                });
            }
        }

        // Verified status filtering
        if ($request->filled('verified') && $request->verified === '1') {
            $query->verified();
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        switch ($sortBy) {
            case 'name':
                $query->orderBy('channel_name', $sortDirection);
                break;
            case 'platform':
                $query->orderBy('platform', $sortDirection);
                break;
            case 'live_status':
                // Sort by live status (live first, then offline)
                $query->orderByRaw('
                    CASE 
                        WHEN manual_live_override = 1 THEN 1
                        WHEN manual_live_override IS NULL AND is_live = 1 THEN 1
                        ELSE 2
                    END ' . ($sortDirection === 'desc' ? 'ASC' : 'DESC')
                );
                break;
            case 'followers':
                $query->withCount('followers')
                      ->orderBy('followers_count', $sortDirection);
                break;
            default:
                $query->orderBy('created_at', $sortDirection);
        }

        $profiles = $query->paginate(12)->appends($request->query());

        // Get available platforms for filter dropdown
        $platforms = StreamerProfile::approved()
            ->select('platform')
            ->distinct()
            ->pluck('platform')
            ->sort();

        return view('streamer.profiles.index', compact('profiles', 'platforms'));
    }

    /**
     * Get streamer recommendations based on user interests.
     */
    public function recommendations(Request $request)
    {
        $user = auth()->user();
        $recommendations = collect();

        if ($user) {
            // Get streamers based on user's reviewed games
            $userReviewedGames = $user->reviews()
                ->where('is_published', true)
                ->pluck('product_id')
                ->unique();

            if ($userReviewedGames->isNotEmpty()) {
                // Find streamers who reviewed the same games
                $similarStreamers = StreamerProfile::approved()
                    ->whereHas('reviews', function($query) use ($userReviewedGames) {
                        $query->whereIn('product_id', $userReviewedGames)
                              ->where('is_published', true);
                    })
                    ->whereNotIn('id', $user->followedStreamers()->pluck('streamer_profile_id'))
                    ->with(['user', 'reviews' => function($query) use ($userReviewedGames) {
                        $query->whereIn('product_id', $userReviewedGames)
                              ->where('is_published', true)
                              ->with('product');
                    }])
                    ->withCount('followers')
                    ->orderByDesc('followers_count')
                    ->limit(6)
                    ->get();

                $recommendations = $recommendations->merge($similarStreamers);
            }

            // Get streamers from platforms user hasn't followed yet
            $followedPlatforms = $user->followedStreamers()
                ->pluck('platform')
                ->unique();

            $availablePlatforms = ['twitch', 'youtube', 'kick'];
            $unexploredPlatforms = collect($availablePlatforms)->diff($followedPlatforms);

            if ($unexploredPlatforms->isNotEmpty()) {
                $platformStreamers = StreamerProfile::approved()
                    ->whereIn('platform', $unexploredPlatforms)
                    ->whereNotIn('id', $user->followedStreamers()->pluck('streamer_profile_id'))
                    ->withCount('followers')
                    ->orderByDesc('followers_count')
                    ->limit(4)
                    ->get();

                $recommendations = $recommendations->merge($platformStreamers);
            }
        }

        // Fill remaining slots with popular streamers
        $remainingSlots = 12 - $recommendations->count();
        if ($remainingSlots > 0) {
            $popularStreamers = StreamerProfile::approved()
                ->when($user, function($query) use ($user, $recommendations) {
                    $excludeIds = $user->followedStreamers()->pluck('streamer_profile_id')
                        ->merge($recommendations->pluck('id'));
                    return $query->whereNotIn('id', $excludeIds);
                })
                ->withCount('followers')
                ->orderByDesc('followers_count')
                ->limit($remainingSlots)
                ->get();

            $recommendations = $recommendations->merge($popularStreamers);
        }

        return response()->json([
            'recommendations' => $recommendations->take(12)->map(function($streamer) {
                return [
                    'id' => $streamer->id,
                    'channel_name' => $streamer->channel_name,
                    'platform' => $streamer->platform,
                    'bio' => $streamer->bio ? Str::limit($streamer->bio, 100) : null,
                    'profile_photo_url' => $streamer->profile_photo_url,
                    'is_live' => $streamer->isLive(),
                    'is_verified' => $streamer->is_verified,
                    'followers_count' => $streamer->followers_count ?? 0,
                    'url' => route('streamer.profile.show', $streamer)
                ];
            })
        ]);
    }

    /**
     * Show the form for creating a new streamer profile.
     */
    public function create(): View
    {
        return view('streamer.profiles.create');
    }

    /**
     * Store a newly created streamer profile in storage.
     */
    public function store(StreamerProfileStoreRequest $request): RedirectResponse
    {
        $user = auth()->user();
        
        // Check if user already has a streamer profile
        $streamerProfile = $user->streamerProfile;
        
        if (!$streamerProfile) {
            return redirect()->route('streamer.profiles.create')
                ->with('error', 'Please connect your streaming platform first to create a profile.');
        }

        $validated = $request->validated();

        // Update bio if provided
        if (array_key_exists('bio', $validated)) {
            $streamerProfile->update(['bio' => $validated['bio']]);
        }

        // Create schedules if provided
        if (isset($validated['schedules'])) {
            // Clear existing schedules first
            $streamerProfile->schedules()->delete();
            
            foreach ($validated['schedules'] as $scheduleData) {
                // Skip empty schedules
                if (empty($scheduleData['day_of_week']) || empty($scheduleData['start_time']) || empty($scheduleData['end_time'])) {
                    continue;
                }
                
                $streamerProfile->schedules()->create([
                    'day_of_week' => $scheduleData['day_of_week'],
                    'start_time' => $scheduleData['start_time'],
                    'end_time' => $scheduleData['end_time'],
                    'timezone' => $scheduleData['timezone'] ?? 'UTC',
                    'notes' => $scheduleData['notes'] ?? null,
                    'is_active' => true,
                ]);
            }
        }

        // Create social links if provided
        if (isset($validated['social_links'])) {
            // Clear existing social links first
            $streamerProfile->socialLinks()->delete();
            
            foreach ($validated['social_links'] as $linkData) {
                // Skip empty social links
                if (empty($linkData['url']) || empty($linkData['platform'])) {
                    continue;
                }
                
                $streamerProfile->socialLinks()->create([
                    'platform' => $linkData['platform'],
                    'url' => $linkData['url'],
                    'display_name' => $linkData['display_name'] ?? null,
                ]);
            }
        }

        return redirect()->route('streamer.profile.show', $streamerProfile)
            ->with('success', 'Profile setup completed successfully!');
    }

    /**
     * Display the specified streamer profile.
     */
    public function show(StreamerProfile $streamerProfile): View
    {
        $streamerProfile->load([
            'user', 
            'schedules', 
            'vods' => function($query) {
                $query->orderBy('published_at', 'desc')->limit(10);
            },
            'socialLinks',
            'reviews' => function($query) {
                $query->with('product')->orderBy('created_at', 'desc')->limit(5);
            }
        ]);

        return view('streamer.profiles.show', compact('streamerProfile'));
    }

    /**
     * Display all VODs for the specified streamer profile.
     */
    public function showVods(StreamerProfile $streamerProfile): View
    {
        $streamerProfile->load([
            'user',
            'vods' => function($query) {
                $query->orderBy('published_at', 'desc');
            }
        ]);

        return view('streamer.profiles.vods', compact('streamerProfile'));
    }

    /**
     * Show the form for editing the specified streamer profile.
     */
    public function edit(StreamerProfile $streamerProfile): View
    {
        $this->authorize('update', $streamerProfile);
        
        $streamerProfile->load(['schedules', 'socialLinks']);
        
        return view('streamer.profiles.edit', compact('streamerProfile'));
    }

    /**
     * Update the specified streamer profile.
     */
    public function update(StreamerProfileUpdateRequest $request, StreamerProfile $streamerProfile): RedirectResponse
    {
        $validated = $request->validated();

        // Update bio if provided
        if (array_key_exists('bio', $validated)) {
            $streamerProfile->update(['bio' => $validated['bio']]);
        }

        // Update schedules if provided
        if (isset($validated['schedules'])) {
            // Get existing schedule IDs to determine which to keep/update/delete
            $existingScheduleIds = $streamerProfile->schedules()->pluck('id')->toArray();
            $providedScheduleIds = array_filter(array_column($validated['schedules'], 'id'));

            // Delete schedules that are no longer provided
            $schedulesToDelete = array_diff($existingScheduleIds, $providedScheduleIds);
            if (!empty($schedulesToDelete)) {
                $streamerProfile->schedules()->whereIn('id', $schedulesToDelete)->delete();
            }

            // Update or create schedules
            foreach ($validated['schedules'] as $scheduleData) {
                $scheduleAttributes = [
                    'day_of_week' => $scheduleData['day_of_week'],
                    'start_time' => $scheduleData['start_time'],
                    'end_time' => $scheduleData['end_time'],
                    'timezone' => $scheduleData['timezone'],
                    'notes' => $scheduleData['notes'] ?? null,
                    'is_active' => $scheduleData['is_active'] ?? true,
                ];

                if (isset($scheduleData['id']) && $scheduleData['id']) {
                    // Update existing schedule
                    $streamerProfile->schedules()
                        ->where('id', $scheduleData['id'])
                        ->update($scheduleAttributes);
                } else {
                    // Create new schedule
                    $streamerProfile->schedules()->create($scheduleAttributes);
                }
            }
        }

        // Update social links if provided
        if (isset($validated['social_links'])) {
            // Get existing social link IDs to determine which to keep/update/delete
            $existingSocialLinkIds = $streamerProfile->socialLinks()->pluck('id')->toArray();
            $providedSocialLinkIds = array_filter(array_column($validated['social_links'], 'id'));

            // Delete social links that are no longer provided
            $socialLinksToDelete = array_diff($existingSocialLinkIds, $providedSocialLinkIds);
            if (!empty($socialLinksToDelete)) {
                $streamerProfile->socialLinks()->whereIn('id', $socialLinksToDelete)->delete();
            }

            // Update or create social links
            foreach ($validated['social_links'] as $linkData) {
                if (empty($linkData['url'])) {
                    continue;
                }

                $linkAttributes = [
                    'platform' => $linkData['platform'],
                    'url' => $linkData['url'],
                    'display_name' => $linkData['display_name'] ?? null,
                ];

                if (isset($linkData['id']) && $linkData['id']) {
                    // Update existing social link
                    $streamerProfile->socialLinks()
                        ->where('id', $linkData['id'])
                        ->update($linkAttributes);
                } else {
                    // Create new social link
                    $streamerProfile->socialLinks()->create($linkAttributes);
                }
            }
        }

        return redirect()->route('streamer.profile.show', $streamerProfile)
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Add a manual VOD link to the streamer profile.
     */
    public function addVod(Request $request, StreamerProfile $streamerProfile): RedirectResponse
    {
        $this->authorize('update', $streamerProfile);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:500',
            'vod_url' => 'required|url|max:500',
            'description' => 'nullable|string|max:1000',
            'thumbnail_url' => 'nullable|url|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        // Check if VOD with this URL already exists
        $existingVod = $streamerProfile->vods()
            ->where('vod_url', $validated['vod_url'])
            ->first();

        if ($existingVod) {
            return redirect()->back()
                ->with('error', 'A VOD with this URL already exists.');
        }

        $streamerProfile->vods()->create([
            'platform_vod_id' => 'manual_' . time() . '_' . $streamerProfile->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'thumbnail_url' => $validated['thumbnail_url'],
            'vod_url' => $validated['vod_url'],
            'duration_seconds' => null,
            'published_at' => now(),
            'is_manual' => true,
        ]);

        return redirect()->back()
            ->with('success', 'VOD added successfully!');
    }

    /**
     * Import VODs from the streaming platform.
     */
    public function importVods(StreamerProfile $streamerProfile, PlatformApiService $platformApiService): RedirectResponse
    {
        $this->authorize('update', $streamerProfile);

        try {
            $importedCount = $platformApiService->importVods($streamerProfile, 20);
            
            if ($importedCount > 0) {
                return redirect()->back()
                    ->with('success', "Successfully imported {$importedCount} new VODs!");
            } else {
                return redirect()->back()
                    ->with('info', 'No new VODs found to import.');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to import VODs: ' . $e->getMessage());
        }
    }

    /**
     * Delete a VOD from the streamer profile.
     */
    public function deleteVod(StreamerProfile $streamerProfile, StreamerVod $vod): RedirectResponse
    {
        $this->authorize('update', $streamerProfile);

        if ($vod->streamer_profile_id !== $streamerProfile->id) {
            abort(403, 'This VOD does not belong to your profile.');
        }

        $vod->delete();

        return redirect()->back()
            ->with('success', 'VOD deleted successfully!');
    }

    /**
     * Show VOD management page for the streamer.
     */
    public function manageVods(StreamerProfile $streamerProfile): View
    {
        $this->authorize('update', $streamerProfile);

        $streamerProfile->load([
            'vods' => function($query) {
                $query->orderBy('published_at', 'desc');
            }
        ]);

        return view('streamer.profiles.manage-vods', compact('streamerProfile'));
    }

    /**
     * Check health of all VODs for the streamer profile.
     */
    public function checkVodHealth(StreamerProfile $streamerProfile): RedirectResponse
    {
        $this->authorize('update', $streamerProfile);

        try {
            $vodCount = $streamerProfile->vods()->count();
            
            if ($vodCount === 0) {
                return redirect()->back()
                    ->with('info', 'No VODs found to check.');
            }

            // Dispatch health check jobs for all VODs
            $jobsDispatched = 0;
            foreach ($streamerProfile->vods as $vod) {
                \App\Jobs\CheckVodHealth::dispatch($vod);
                $jobsDispatched++;
            }
            
            return redirect()->back()
                ->with('success', "Health check started for {$jobsDispatched} VODs. Results will be updated shortly.");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to start VOD health check: ' . $e->getMessage());
        }
    }

    /**
     * Set manual live status override for the streamer.
     */
    public function setLiveStatus(Request $request, StreamerProfile $streamerProfile)
    {
        $this->authorize('update', $streamerProfile);

        $validator = Validator::make($request->all(), [
            'is_live' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid live status value.'], 400);
        }

        $isLive = $request->boolean('is_live');
        $streamerProfile->setManualLiveOverride($isLive);

        $message = $isLive ? 'Live status set to LIVE' : 'Live status set to OFFLINE';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_live' => $streamerProfile->isLive(),
                'manual_override' => $streamerProfile->manual_live_override
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Clear manual live status override for the streamer.
     */
    public function clearLiveStatusOverride(StreamerProfile $streamerProfile)
    {
        $this->authorize('update', $streamerProfile);

        $streamerProfile->setManualLiveOverride(null);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Manual live status override cleared. Using automatic detection.',
                'is_live' => $streamerProfile->isLive(),
                'manual_override' => null
            ]);
        }

        return redirect()->back()->with('success', 'Manual live status override cleared. Using automatic detection.');
    }
}