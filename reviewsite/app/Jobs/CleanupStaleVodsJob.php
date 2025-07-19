<?php

namespace App\Jobs;

use App\Models\StreamerVod;
use App\Services\JobMonitoringService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupStaleVodsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800; // 30 minutes
    public $tries = 2;

    public function __construct()
    {
        //
    }

    public function handle(JobMonitoringService $jobMonitoring): void
    {
        $jobMonitoring->recordJobStart(self::class);
        Log::info('Starting stale VOD cleanup job');

        // Remove VODs older than 6 months that are not manually added
        $oldVods = StreamerVod::where('is_manual', false)
            ->where('published_at', '<', Carbon::now()->subMonths(6))
            ->get();

        $removedOld = 0;
        foreach ($oldVods as $vod) {
            $vod->delete();
            $removedOld++;
        }

        // Check for broken VOD links (sample check on recent VODs)
        $recentVods = StreamerVod::where('published_at', '>', Carbon::now()->subWeeks(2))
            ->inRandomOrder()
            ->limit(50)
            ->get();

        $removedBroken = 0;
        foreach ($recentVods as $vod) {
            if ($this->isVodLinkBroken($vod)) {
                Log::warning("Removing broken VOD link: {$vod->title} ({$vod->vod_url})");
                $vod->delete();
                $removedBroken++;
            }
        }

        Log::info("VOD cleanup completed. Removed old: {$removedOld}, Removed broken: {$removedBroken}");
        $jobMonitoring->recordJobSuccess(self::class);
    }

    private function isVodLinkBroken(StreamerVod $vod): bool
    {
        try {
            // Simple HEAD request to check if URL is accessible
            $response = Http::timeout(10)->head($vod->vod_url);
            
            // Consider 404, 403, or 500+ status codes as broken
            return $response->status() >= 400;
            
        } catch (\Exception $e) {
            // If we can't reach the URL, consider it broken
            Log::debug("VOD link check failed for {$vod->vod_url}: " . $e->getMessage());
            return true;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('VOD cleanup job failed: ' . $exception->getMessage());
        app(JobMonitoringService::class)->recordJobFailure(self::class, $exception->getMessage());
    }
}