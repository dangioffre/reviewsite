<?php

namespace App\Services;

use App\Models\StreamerProfile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LiveStatusService
{
    private const CACHE_PREFIX = 'live_status_';
    private const CACHE_DURATION = 120; // 2 minutes

    /**
     * Get cached live status for a streamer.
     */
    public function getCachedLiveStatus(StreamerProfile $streamerProfile): ?bool
    {
        $cacheKey = $this->getCacheKey($streamerProfile);
        return Cache::get($cacheKey);
    }

    /**
     * Cache live status for a streamer.
     */
    public function cacheLiveStatus(StreamerProfile $streamerProfile, bool $isLive): void
    {
        $cacheKey = $this->getCacheKey($streamerProfile);
        Cache::put($cacheKey, $isLive, self::CACHE_DURATION);
    }

    /**
     * Get live status with caching fallback.
     */
    public function getLiveStatus(StreamerProfile $streamerProfile): bool
    {
        // Check manual override first
        if ($streamerProfile->manual_live_override !== null) {
            return $streamerProfile->manual_live_override;
        }

        // Check cache
        $cachedStatus = $this->getCachedLiveStatus($streamerProfile);
        if ($cachedStatus !== null) {
            return $cachedStatus;
        }

        // Fallback to database value
        return $streamerProfile->is_live ?? false;
    }

    /**
     * Get all currently live streamers with caching.
     */
    public function getLiveStreamers(): \Illuminate\Database\Eloquent\Collection
    {
        $cacheKey = 'all_live_streamers';
        
        return Cache::remember($cacheKey, 60, function () {
            return StreamerProfile::approved()
                ->live()
                ->with(['user'])
                ->get();
        });
    }

    /**
     * Clear live status cache for a streamer.
     */
    public function clearCache(StreamerProfile $streamerProfile): void
    {
        $cacheKey = $this->getCacheKey($streamerProfile);
        Cache::forget($cacheKey);
        
        // Also clear the all live streamers cache
        Cache::forget('all_live_streamers');
    }

    /**
     * Clear all live status caches.
     */
    public function clearAllCaches(): void
    {
        // Get all cache keys for live status
        $streamers = StreamerProfile::all();
        foreach ($streamers as $streamer) {
            $this->clearCache($streamer);
        }
        
        Cache::forget('all_live_streamers');
        
        Log::info('Cleared all live status caches');
    }

    /**
     * Get live status indicators for multiple streamers efficiently.
     */
    public function getBulkLiveStatus(array $streamerIds): array
    {
        $statuses = [];
        
        foreach ($streamerIds as $id) {
            $streamer = StreamerProfile::find($id);
            if ($streamer) {
                $statuses[$id] = $this->getLiveStatus($streamer);
            }
        }
        
        return $statuses;
    }

    /**
     * Generate cache key for a streamer's live status.
     */
    private function getCacheKey(StreamerProfile $streamerProfile): string
    {
        return self::CACHE_PREFIX . $streamerProfile->platform . '_' . $streamerProfile->platform_user_id;
    }
}