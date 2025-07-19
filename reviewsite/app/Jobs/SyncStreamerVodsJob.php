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

class SyncStreamerVodsJob implements ShouldQueue
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
            Log::info("Starting VOD sync for streamer profile {$this->streamerProfile->id}");

            $vods = $platformApiService->fetchVods($this->streamerProfile);
            
            $imported = 0;
            $updated = 0;

            foreach ($vods as $vodData) {
                $existingVod = $this->streamerProfile->vods()
                    ->where('platform_vod_id', $vodData['platform_vod_id'])
                    ->first();

                if ($existingVod) {
                    $existingVod->update([
                        'title' => $vodData['title'],
                        'description' => $vodData['description'] ?? null,
                        'thumbnail_url' => $vodData['thumbnail_url'] ?? null,
                        'duration_seconds' => $vodData['duration_seconds'] ?? null,
                    ]);
                    $updated++;
                } else {
                    $this->streamerProfile->vods()->create([
                        'platform_vod_id' => $vodData['platform_vod_id'],
                        'title' => $vodData['title'],
                        'description' => $vodData['description'] ?? null,
                        'thumbnail_url' => $vodData['thumbnail_url'] ?? null,
                        'vod_url' => $vodData['vod_url'],
                        'duration_seconds' => $vodData['duration_seconds'] ?? null,
                        'published_at' => $vodData['published_at'] ?? null,
                        'is_manual' => false,
                    ]);
                    $imported++;
                }
            }

            $metrics = [
                'streamer_profile_id' => $this->streamerProfile->id,
                'vods_imported' => $imported,
                'vods_updated' => $updated,
                'total_vods_processed' => count($vods),
            ];

            $monitoring->recordJobSuccess(self::class, $metrics);
            Log::info("VOD sync completed for streamer profile {$this->streamerProfile->id}. Imported: {$imported}, Updated: {$updated}");

        } catch (\Throwable $e) {
            $monitoring->recordJobFailure(self::class, $e, $context);
            Log::error("VOD sync failed for streamer profile {$this->streamerProfile->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $context = ['streamer_profile_id' => $this->streamerProfile->id];
        app(JobMonitoringService::class)->recordJobFailure(self::class, $exception, $context);
        Log::error("VOD sync job failed for streamer profile {$this->streamerProfile->id}: " . $exception->getMessage());
    }
}