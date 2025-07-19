<?php

namespace App\Services;

use App\Models\StreamerProfile;
use Illuminate\Support\Facades\Log;

class LiveStatusMonitoringService
{
    public function __construct(
        private LiveStatusService $liveStatusService,
        private StreamerNotificationService $notificationService
    ) {}

    /**
     * Update live status and trigger notifications if status changed.
     */
    public function updateLiveStatusWithNotifications(StreamerProfile $streamerProfile, bool $newLiveStatus): void
    {
        $previousStatus = $streamerProfile->isLive();
        
        // Update the live status
        $streamerProfile->updateLiveStatus($newLiveStatus);
        
        // Clear cache to ensure fresh data
        $this->liveStatusService->clearCache($streamerProfile);
        
        // If streamer just went live (was offline, now online), send notifications
        if (!$previousStatus && $newLiveStatus) {
            $this->triggerLiveNotifications($streamerProfile);
        }
        
        Log::info('Live status updated for streamer', [
            'streamer_id' => $streamerProfile->id,
            'channel_name' => $streamerProfile->channel_name,
            'platform' => $streamerProfile->platform,
            'previous_status' => $previousStatus,
            'new_status' => $newLiveStatus,
            'notifications_sent' => !$previousStatus && $newLiveStatus
        ]);
    }

    /**
     * Set manual live override and trigger notifications if going live.
     */
    public function setManualLiveOverrideWithNotifications(StreamerProfile $streamerProfile, ?bool $isLive): void
    {
        $previousStatus = $streamerProfile->isLive();
        
        // Set the manual override
        $streamerProfile->setManualLiveOverride($isLive);
        
        // Clear cache to ensure fresh data
        $this->liveStatusService->clearCache($streamerProfile);
        
        $newStatus = $streamerProfile->isLive();
        
        // If streamer just went live (was offline, now online), send notifications
        if (!$previousStatus && $newStatus) {
            $this->triggerLiveNotifications($streamerProfile);
        }
        
        Log::info('Manual live override set for streamer', [
            'streamer_id' => $streamerProfile->id,
            'channel_name' => $streamerProfile->channel_name,
            'platform' => $streamerProfile->platform,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'manual_override' => $isLive,
            'notifications_sent' => !$previousStatus && $newStatus
        ]);
    }

    /**
     * Trigger live notifications for a streamer's followers.
     */
    public function triggerLiveNotifications(StreamerProfile $streamerProfile): void
    {
        // Only send notifications for approved streamers
        if (!$this->shouldTriggerLiveNotifications($streamerProfile)) {
            Log::info('Skipping live notifications for unapproved streamer or streamer with no followers', [
                'streamer_id' => $streamerProfile->id,
                'channel_name' => $streamerProfile->channel_name,
                'is_approved' => $streamerProfile->is_approved
            ]);
            return;
        }

        try {
            $this->notificationService->notifyFollowersOfLiveStream($streamerProfile);
            
            Log::info('Live notifications sent for streamer', [
                'streamer_id' => $streamerProfile->id,
                'channel_name' => $streamerProfile->channel_name,
                'platform' => $streamerProfile->platform,
                'follower_count' => $streamerProfile->followers()->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send live notifications for streamer', [
                'streamer_id' => $streamerProfile->id,
                'channel_name' => $streamerProfile->channel_name,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check if a streamer should trigger notifications when going live.
     */
    public function shouldTriggerLiveNotifications(StreamerProfile $streamerProfile): bool
    {
        // Only approved streamers should trigger notifications
        if (!$streamerProfile->is_approved) {
            return false;
        }

        // Check if streamer has any followers with live notifications enabled
        $followersWithLiveNotifications = $streamerProfile->followers()
            ->wherePivot('notification_preferences->live', true)
            ->count();

        return $followersWithLiveNotifications > 0;
    }

    /**
     * Bulk update live status for multiple streamers with notifications.
     */
    public function bulkUpdateLiveStatusWithNotifications(array $streamerStatusUpdates): void
    {
        foreach ($streamerStatusUpdates as $update) {
            $streamerProfile = $update['streamer'];
            $newStatus = $update['is_live'];
            
            $this->updateLiveStatusWithNotifications($streamerProfile, $newStatus);
        }
    }

    /**
     * Get live status change statistics.
     */
    public function getLiveStatusChangeStats(): array
    {
        $totalStreamers = StreamerProfile::approved()->count();
        $liveStreamers = StreamerProfile::approved()->live()->count();
        $manualOverrides = StreamerProfile::approved()
            ->whereNotNull('manual_live_override')
            ->count();

        return [
            'total_approved_streamers' => $totalStreamers,
            'currently_live' => $liveStreamers,
            'manual_overrides_active' => $manualOverrides,
            'live_percentage' => $totalStreamers > 0 ? round(($liveStreamers / $totalStreamers) * 100, 2) : 0
        ];
    }
}