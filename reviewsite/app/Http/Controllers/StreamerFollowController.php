<?php

namespace App\Http\Controllers;

use App\Models\StreamerProfile;
use App\Services\StreamerNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class StreamerFollowController extends Controller
{
    public function __construct(
        private StreamerNotificationService $notificationService
    ) {
        // Middleware is applied in routes, not needed here
    }

    /**
     * Follow a streamer.
     */
    public function follow(StreamerProfile $streamerProfile): RedirectResponse
    {
        $user = Auth::user();
        
        if ($user->followedStreamers()->where('streamer_profile_id', $streamerProfile->id)->exists()) {
            return back()->with('error', 'You are already following this streamer.');
        }

        // Default notification preferences
        $notificationPreferences = [
            'live' => true,
            'reviews' => true,
        ];

        $user->followedStreamers()->attach($streamerProfile->id, [
            'notification_preferences' => json_encode($notificationPreferences)
        ]);

        // Send notification to streamer about new follower
        $this->notificationService->sendFollowNotification($streamerProfile, $user);

        return back()->with('success', "You are now following {$streamerProfile->getDisplayName()}!");
    }

    /**
     * Unfollow a streamer.
     */
    public function unfollow(StreamerProfile $streamerProfile): RedirectResponse
    {
        $user = Auth::user();
        
        if (!$user->followedStreamers()->where('streamer_profile_id', $streamerProfile->id)->exists()) {
            return back()->with('error', 'You are not following this streamer.');
        }

        $user->followedStreamers()->detach($streamerProfile->id);

        return back()->with('success', "You have unfollowed {$streamerProfile->getDisplayName()}.");
    }

    /**
     * Show the list of streamers the user is following.
     */
    public function followers(): View
    {
        $user = Auth::user();
        
        $followedStreamers = $user->followedStreamers()
            ->approved()
            ->with(['user', 'vods' => function ($query) {
                $query->latest()->limit(3);
            }])
            ->get();

        return view('streamer.followers.index', compact('followedStreamers'));
    }

    /**
     * Update notification preferences for a followed streamer.
     */
    public function updateNotificationPreferences(Request $request, StreamerProfile $streamerProfile): RedirectResponse
    {
        $request->validate([
            'live' => 'boolean',
            'reviews' => 'boolean',
        ]);

        $user = Auth::user();
        
        if (!$user->followedStreamers()->where('streamer_profile_id', $streamerProfile->id)->exists()) {
            return back()->with('error', 'You are not following this streamer.');
        }

        $notificationPreferences = [
            'live' => $request->boolean('live'),
            'reviews' => $request->boolean('reviews'),
        ];

        $user->followedStreamers()->updateExistingPivot($streamerProfile->id, [
            'notification_preferences' => json_encode($notificationPreferences)
        ]);

        return back()->with('success', 'Notification preferences updated successfully.');
    }
}