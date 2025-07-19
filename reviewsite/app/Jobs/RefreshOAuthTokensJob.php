<?php

namespace App\Jobs;

use App\Models\StreamerProfile;
use App\Services\StreamerOAuthService;
use App\Services\JobMonitoringService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RefreshOAuthTokensJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;

    public function __construct()
    {
        //
    }

    public function handle(StreamerOAuthService $oauthService, JobMonitoringService $jobMonitoring): void
    {
        $jobMonitoring->recordJobStart(self::class);
        Log::info('Starting OAuth token refresh job');

        // Get profiles with tokens expiring in the next 24 hours
        $expiringProfiles = StreamerProfile::whereNotNull('oauth_expires_at')
            ->where('oauth_expires_at', '<=', Carbon::now()->addDay())
            ->where('oauth_expires_at', '>', Carbon::now())
            ->whereNotNull('oauth_refresh_token')
            ->get();

        $refreshed = 0;
        $failed = 0;

        foreach ($expiringProfiles as $profile) {
            try {
                if ($oauthService->refreshToken($profile)) {
                    $refreshed++;
                    Log::info("Refreshed OAuth token for streamer profile {$profile->id}");
                } else {
                    $failed++;
                    Log::warning("Failed to refresh OAuth token for streamer profile {$profile->id}");
                }
            } catch (\Exception $e) {
                $failed++;
                Log::error("Exception refreshing OAuth token for streamer profile {$profile->id}: " . $e->getMessage());
            }
        }

        Log::info("OAuth token refresh completed. Refreshed: {$refreshed}, Failed: {$failed}");
        $jobMonitoring->recordJobSuccess(self::class);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('OAuth token refresh job failed: ' . $exception->getMessage());
        app(JobMonitoringService::class)->recordJobFailure(self::class, $exception->getMessage());
    }
}