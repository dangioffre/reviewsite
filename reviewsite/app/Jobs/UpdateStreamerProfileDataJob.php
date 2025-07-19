<?php

namespace App\Jobs;

use App\Models\StreamerProfile;
use App\Services\PlatformApiService;
use App\Services\JobMonitoringService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateStreamerProfileDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected StreamerProfile $streamerProfile;

    public function __construct(StreamerProfile $streamerProfile)
    {
        $this->streamerProfile = $streamerProfile;
    }

    public function handle(PlatformApiService $platformApiService, JobMonitoringService $monitoring): void
    {
        $context = ['streamer_profile_id' => $this->streamerProfile->id];
        $monitoring->recordJobStart(self::class, $context);

        try {
            Log::info("Starting profile data update for streamer profile {$this->streamerProfile->id}");

            $channelData = $platformApiService->fetchChannelData($this->streamerProfile);

            // Track what fields were updated
            $updatedFields = [];
            $originalData = $this->streamerProfile->toArray();

            // Update profile information from platform
            $this->streamerProfile->update([
                'channel_name' => $channelData['channel_name'] ?? $this->streamerProfile->channel_name,
                'profile_photo_url' => $channelData['profile_photo_url'] ?? $this->streamerProfile->profile_photo_url,
                'bio' => $channelData['bio'] ?? $this->streamerProfile->bio,
                'channel_url' => $channelData['channel_url'] ?? $this->streamerProfile->channel_url,
            ]);

            // Check which fields were actually updated
            $newData = $this->streamerProfile->fresh()->toArray();
            foreach (['channel_name', 'profile_photo_url', 'bio', 'channel_url'] as $field) {
                if ($originalData[$field] !== $newData[$field]) {
                    $updatedFields[] = $field;
                }
            }

            $metrics = [
                'streamer_profile_id' => $this->streamerProfile->id,
                'fields_updated' => $updatedFields,
                'update_count' => count($updatedFields),
            ];

            $monitoring->recordJobSuccess(self::class, $metrics);
            Log::info("Profile data update completed for streamer profile {$this->streamerProfile->id}");

        } catch (\Throwable $e) {
            $monitoring->recordJobFailure(self::class, $e, $context);
            Log::error("Profile data update failed for streamer profile {$this->streamerProfile->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $context = ['streamer_profile_id' => $this->streamerProfile->id];
        app(JobMonitoringService::class)->recordJobFailure(self::class, $exception, $context);
        Log::error("Profile data update job failed for streamer profile {$this->streamerProfile->id}: " . $exception->getMessage());
    }
}