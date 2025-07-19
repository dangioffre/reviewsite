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

class SyncAllStreamerVodsJob implements ShouldQueue
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
        Log::info('Starting VOD sync for all active streamer profiles');

        $activeProfiles = StreamerProfile::approved()
            ->whereNotNull('oauth_token')
            ->get();

        $synced = 0;
        $failed = 0;

        foreach ($activeProfiles as $profile) {
            try {
                // Dispatch individual sync job for each profile
                SyncStreamerVodsJob::dispatch($profile);
                $synced++;
                
                Log::info("Dispatched VOD sync for streamer profile {$profile->id} ({$profile->channel_name})");
                
                // Small delay to avoid overwhelming the queue
                usleep(100000); // 0.1 seconds
                
            } catch (\Exception $e) {
                $failed++;
                Log::error("Failed to dispatch VOD sync for streamer profile {$profile->id}: " . $e->getMessage());
            }
        }

        Log::info("VOD sync dispatch completed. Dispatched: {$synced}, Failed: {$failed}");
        $jobMonitoring->recordJobSuccess(self::class);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('VOD sync job failed: ' . $exception->getMessage());
        app(JobMonitoringService::class)->recordJobFailure(self::class, $exception->getMessage());
    }
}