<?php

namespace App\Services;

use App\Models\StreamerProfile;
use App\Models\User;
use App\Models\Review;
use App\Notifications\StreamerLiveNotification;
use App\Notifications\StreamerNewReviewNotification;
use App\Notifications\StreamerNewFollowerNotification;
use Illuminate\Support\Facades\Notification;

class StreamerNotificationService
{
    /**
     * Notify followers when a streamer goes live.
     */
    public function notifyFollowersOfLiveStream(StreamerProfile $streamerProfile): void
    {
        $followers = $streamerProfile->followers()
            ->wherePivot('notification_preferences->live', true)
            ->get();

        if ($followers->isEmpty()) {
            return;
        }

        Notification::send($followers, new StreamerLiveNotification($streamerProfile));
    }

    /**
     * Notify followers when a streamer posts a new review.
     */
    public function notifyFollowersOfNewReview(Review $review): void
    {
        if (!$review->streamerProfile) {
            return;
        }

        $followers = $review->streamerProfile->followers()
            ->wherePivot('notification_preferences->reviews', true)
            ->get();

        if ($followers->isEmpty()) {
            return;
        }

        Notification::send($followers, new StreamerNewReviewNotification($review));
    }

    /**
     * Send notification to streamer about new follower.
     */
    public function sendFollowNotification(StreamerProfile $streamerProfile, User $follower): void
    {
        $streamerProfile->user->notify(new StreamerNewFollowerNotification($follower, $streamerProfile));
    }

    /**
     * Get notification preferences for a user following a streamer.
     */
    public function getNotificationPreferences(User $user, StreamerProfile $streamerProfile): array
    {
        $pivot = $user->followedStreamers()
            ->where('streamer_profile_id', $streamerProfile->id)
            ->first();

        if (!$pivot) {
            return [];
        }

        $preferences = json_decode($pivot->pivot->notification_preferences, true);
        
        return $preferences ?: [
            'live' => true,
            'reviews' => true,
        ];
    }

    /**
     * Check if a user should receive live notifications for a streamer.
     */
    public function shouldReceiveLiveNotification(User $user, StreamerProfile $streamerProfile): bool
    {
        $preferences = $this->getNotificationPreferences($user, $streamerProfile);
        return $preferences['live'] ?? false;
    }

    /**
     * Check if a user should receive review notifications for a streamer.
     */
    public function shouldReceiveReviewNotification(User $user, StreamerProfile $streamerProfile): bool
    {
        $preferences = $this->getNotificationPreferences($user, $streamerProfile);
        return $preferences['reviews'] ?? false;
    }
}