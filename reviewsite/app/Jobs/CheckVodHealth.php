<?php

namespace App\Jobs;

use App\Models\StreamerVod;
use App\Services\PlatformApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Exception;

class CheckVodHealth implements ShouldQueue
{
    use Queueable;

    /**
     * The VOD to check health for.
     */
    public StreamerVod $vod;

    /**
     * Create a new job instance.
     */
    public function __construct(StreamerVod $vod)
    {
        $this->vod = $vod;
    }

    /**
     * Execute the job.
     */
    public function handle(PlatformApiService $platformApiService): void
    {
        try {
            $isHealthy = $platformApiService->checkVodHealth($this->vod);
            
            if (!$isHealthy) {
                $this->vod->markAsUnhealthy('VOD URL is not accessible');
                
                Log::warning("VOD health check failed - marked as unhealthy", [
                    'vod_id' => $this->vod->id,
                    'vod_url' => $this->vod->vod_url,
                    'streamer_profile_id' => $this->vod->streamer_profile_id
                ]);
            } else {
                $this->vod->markAsHealthy();
                
                Log::info("VOD health check passed - marked as healthy", [
                    'vod_id' => $this->vod->id
                ]);
            }

        } catch (Exception $e) {
            $this->vod->markAsUnhealthy('Health check failed: ' . $e->getMessage());
            
            Log::error("VOD health check job failed", [
                'vod_id' => $this->vod->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error("VOD health check job failed permanently", [
            'vod_id' => $this->vod->id,
            'error' => $exception->getMessage()
        ]);
    }
}
