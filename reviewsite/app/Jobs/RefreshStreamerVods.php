<?php

namespace App\Jobs;

use App\Models\StreamerProfile;
use App\Services\PlatformApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Exception;

class RefreshStreamerVods implements ShouldQueue
{
    use Queueable;

    /**
     * The streamer profile to refresh VODs for.
     */
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
    public function handle(PlatformApiService $platformApiService): void
    {
        try {
            // Only refresh for approved profiles with valid OAuth tokens
            if (!$this->streamerProfile->is_approved || !$this->streamerProfile->oauth_token) {
                Log::info("Skipping VOD refresh for profile", [
                    'profile_id' => $this->streamerProfile->id,
                    'reason' => 'not_approved_or_no_token'
                ]);
                return;
            }

            $importedCount = $platformApiService->importVods($this->streamerProfile, 20);
            
            Log::info("VOD refresh completed", [
                'profile_id' => $this->streamerProfile->id,
                'platform' => $this->streamerProfile->platform,
                'imported_count' => $importedCount
            ]);

        } catch (Exception $e) {
            Log::error("VOD refresh failed", [
                'profile_id' => $this->streamerProfile->id,
                'platform' => $this->streamerProfile->platform,
                'error' => $e->getMessage()
            ]);

            // Re-throw the exception to mark the job as failed
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error("VOD refresh job failed permanently", [
            'profile_id' => $this->streamerProfile->id,
            'platform' => $this->streamerProfile->platform,
            'error' => $exception->getMessage()
        ]);
    }
}
