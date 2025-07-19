<?php

namespace App\Jobs;

use App\Models\StreamerProfile;
use App\Services\PlatformApiService;
use App\Services\StreamerNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Exception;

class CheckLiveStatusJob implements ShouldQueue
{
    use Queueable;

    public StreamerProfile $streamerProfile;

    /**
     * Create a new job instance.
     */
    public function __construct(StreamerProfile $streamerProfile)
    {
        $this->streamerProfile = $streamerProfile;
    }

    /**
     * Execute the job.
     */
    public function handle(PlatformApiService $platformApiService, StreamerNotificationService $notificationService): void
    {
        try {
            // Skip if manual override is set
            if ($this->streamerProfile->manual_live_override !== null) {
                Log::info('Skipping live status check due to manual override', [
                    'profile_id' => $this->streamerProfile->id,
                    'manual_override' => $this->streamerProfile->manual_live_override
                ]);
                return;
            }

            // Check current live status from platform API
            $isCurrentlyLive = $platformApiService->checkLiveStatus($this->streamerProfile);
            $wasLive = $this->streamerProfile->is_live;

            // Update the live status
            $this->streamerProfile->updateLiveStatus($isCurrentlyLive);

            // If streamer just went live, notify followers
            if ($isCurrentlyLive && !$wasLive) {
                $notificationService->notifyFollowersOfLiveStream($this->streamerProfile);
                
                Log::info('Streamer went live, notifications sent', [
                    'profile_id' => $this->streamerProfile->id,
                    'channel_name' => $this->streamerProfile->channel_name,
                    'platform' => $this->streamerProfile->platform
                ]);
            }

            // Log status change
            if ($isCurrentlyLive !== $wasLive) {
                Log::info('Live status changed', [
                    'profile_id' => $this->streamerProfile->id,
                    'channel_name' => $this->streamerProfile->channel_name,
                    'platform' => $this->streamerProfile->platform,
                    'was_live' => $wasLive,
                    'is_live' => $isCurrentlyLive
                ]);
            }

        } catch (Exception $e) {
            Log::error('Failed to check live status', [
                'profile_id' => $this->streamerProfile->id,
                'channel_name' => $this->streamerProfile->channel_name,
                'platform' => $this->streamerProfile->platform,
                'error' => $e->getMessage()
            ]);

            // Don't fail the job for API errors, just log them
            // The next scheduled check will try again
        }
    }

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;
}
