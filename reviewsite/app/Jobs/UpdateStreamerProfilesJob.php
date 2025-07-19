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

class UpdateStreamerProfilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800; // 30 minutes
    public $tries = 2;

    public function __construct()
    {
        //
    }

    public function handle(PlatformApiService $platformApiService, JobMonitoringService $jobMonitoring): void
    {
        $jobMonitoring->recordJobStart(self::class);
        Log::info('Starting streamer profile update job');

        $activeProfiles = StreamerProfile::approved()
            ->whereNotNull('oauth_token')
            ->get();

        $updated = 0;
        $failed = 0;

        foreach ($activeProfiles as $profile) {
            try {
                $channelData = $platformApiService->fetchChannelData($profile);
                
                $hasChanges = false;
                
                // Update profile photo if changed
                if (isset($channelData['profile_photo_url']) && 
                    $channelData['profile_photo_url'] !== $profile->profile_photo_url) {
                    $profile->profile_photo_url = $channelData['profile_photo_url'];
                    $hasChanges = true;
                }
                
                // Update bio if changed
                if (isset($channelData['bio']) && 
                    $channelData['bio'] !== $profile->bio) {
                    $profile->bio = $channelData['bio'];
                    $hasChanges = true;
                }
                
                // Update channel name if changed
                if (isset($channelData['channel_name']) && 
                    $channelData['channel_name'] !== $profile->channel_name) {
                    $profile->channel_name = $channelData['channel_name'];
                    $hasChanges = true;
                }
                
                if ($hasChanges) {
                    $profile->save();
                    $updated++;
                    Log::info("Updated profile information for streamer {$profile->id} ({$profile->channel_name})");
                }
                
                // Small delay to respect API rate limits
                usleep(500000); // 0.5 seconds
                
            } catch (\Exception $e) {
                $failed++;
                Log::error("Failed to update profile for streamer {$profile->id}: " . $e->getMessage());
            }
        }

        Log::info("Profile update completed. Updated: {$updated}, Failed: {$failed}");
        $jobMonitoring->recordJobSuccess(self::class);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Profile update job failed: ' . $exception->getMessage());
        app(JobMonitoringService::class)->recordJobFailure(self::class, $exception->getMessage());
    }
}