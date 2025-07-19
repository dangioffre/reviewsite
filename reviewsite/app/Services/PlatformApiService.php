<?php

namespace App\Services;

use App\Models\StreamerProfile;
use App\Models\StreamerVod;
use App\Exceptions\PlatformApiException;
use App\Services\StreamerErrorHandlingService;
use App\Services\RetryService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class PlatformApiService
{
    private StreamerErrorHandlingService $errorHandler;
    private RetryService $retryService;

    public function __construct(
        StreamerErrorHandlingService $errorHandler,
        RetryService $retryService
    ) {
        $this->errorHandler = $errorHandler;
        $this->retryService = $retryService;
    }

    /**
     * API endpoints for each platform
     */
    private const PLATFORM_ENDPOINTS = [
        'twitch' => [
            'base_url' => 'https://api.twitch.tv/helix',
            'user_endpoint' => '/users',
            'videos_endpoint' => '/videos',
            'streams_endpoint' => '/streams',
        ],
        'youtube' => [
            'base_url' => 'https://www.googleapis.com/youtube/v3',
            'channel_endpoint' => '/channels',
            'videos_endpoint' => '/search',
            'live_endpoint' => '/search',
        ],
        'kick' => [
            'base_url' => 'https://kick.com/api/v2',
            'user_endpoint' => '/channels',
            'videos_endpoint' => '/channels/{username}/videos',
            'live_endpoint' => '/channels/{username}',
        ],
    ];

    /**
     * Rate limit cache keys
     */
    private const RATE_LIMIT_PREFIX = 'platform_api_rate_limit_';
    
    /**
     * Default timeout for API requests
     */
    private const REQUEST_TIMEOUT = 30;

    /**
     * Fetch channel data from streaming platform
     *
     * @param StreamerProfile $profile
     * @return array
     * @throws PlatformApiException
     */
    public function fetchChannelData(StreamerProfile $profile): array
    {
        try {
            $this->validateProfile($profile);
            
            if ($this->isRateLimited($profile->platform)) {
                throw PlatformApiException::rateLimited($profile->platform);
            }

            $data = $this->retryService->executeForPlatform(function () use ($profile) {
                return match ($profile->platform) {
                    'twitch' => $this->fetchTwitchChannelData($profile),
                    'youtube' => $this->fetchYouTubeChannelData($profile),
                    'kick' => $this->fetchKickChannelData($profile),
                    default => throw new Exception("Unsupported platform: {$profile->platform}"),
                };
            }, $profile->platform);

            // Cache successful response for fallback
            $this->errorHandler->cacheFallbackData($profile, 'channel_data', $data);

            Log::info("Successfully fetched channel data", [
                'profile_id' => $profile->id,
                'platform' => $profile->platform,
                'channel_name' => $data['channel_name'] ?? 'unknown'
            ]);

            return $data;

        } catch (PlatformApiException $e) {
            // Try fallback data if available
            if ($this->errorHandler->shouldUseFallback($e)) {
                $fallbackData = $this->errorHandler->getFallbackData($profile, 'channel_data');
                if ($fallbackData) {
                    Log::info("Using fallback channel data", [
                        'profile_id' => $profile->id,
                        'platform' => $profile->platform
                    ]);
                    return $fallbackData;
                }
            }
            throw $e;
        } catch (Exception $e) {
            $context = ['profile_id' => $profile->id];
            throw $this->errorHandler->handlePlatformApiError($e, $profile->platform, $context);
        }
    }

    /**
     * Fetch recent VODs from streaming platform
     *
     * @param StreamerProfile $profile
     * @param int $limit
     * @return array
     * @throws PlatformApiException
     */
    public function fetchVods(StreamerProfile $profile, int $limit = 10): array
    {
        try {
            $this->validateProfile($profile);
            
            if ($this->isRateLimited($profile->platform)) {
                throw PlatformApiException::rateLimited($profile->platform);
            }

            $vods = $this->retryService->executeForPlatform(function () use ($profile, $limit) {
                return match ($profile->platform) {
                    'twitch' => $this->fetchTwitchVods($profile, $limit),
                    'youtube' => $this->fetchYouTubeVods($profile, $limit),
                    'kick' => $this->fetchKickVods($profile, $limit),
                    default => throw new Exception("Unsupported platform: {$profile->platform}"),
                };
            }, $profile->platform);

            // Cache successful response for fallback
            $this->errorHandler->cacheFallbackData($profile, 'vods', ['vods' => $vods]);

            Log::info("Successfully fetched VODs", [
                'profile_id' => $profile->id,
                'platform' => $profile->platform,
                'vod_count' => count($vods)
            ]);

            return $vods;

        } catch (PlatformApiException $e) {
            // Try fallback data if available
            if ($this->errorHandler->shouldUseFallback($e)) {
                $fallbackData = $this->errorHandler->getFallbackData($profile, 'vods');
                if ($fallbackData && isset($fallbackData['vods'])) {
                    Log::info("Using fallback VOD data", [
                        'profile_id' => $profile->id,
                        'platform' => $profile->platform,
                        'vod_count' => count($fallbackData['vods'])
                    ]);
                    return $fallbackData['vods'];
                }
            }
            throw $e;
        } catch (Exception $e) {
            $context = ['profile_id' => $profile->id, 'limit' => $limit];
            throw $this->errorHandler->handlePlatformApiError($e, $profile->platform, $context);
        }
    }

    /**
     * Import VODs for a streamer profile
     *
     * @param StreamerProfile $profile
     * @param int $limit
     * @return int Number of VODs imported
     * @throws Exception
     */
    public function importVods(StreamerProfile $profile, int $limit = 10): int
    {
        $vodData = $this->fetchVods($profile, $limit);
        $importedCount = 0;

        foreach ($vodData as $vodInfo) {
            // Check if VOD already exists
            $existingVod = $profile->vods()
                ->where('platform_vod_id', $vodInfo['platform_vod_id'])
                ->first();

            if (!$existingVod) {
                $profile->vods()->create([
                    'platform_vod_id' => $vodInfo['platform_vod_id'],
                    'title' => $vodInfo['title'],
                    'description' => $vodInfo['description'],
                    'thumbnail_url' => $vodInfo['thumbnail_url'],
                    'vod_url' => $vodInfo['vod_url'],
                    'duration_seconds' => $vodInfo['duration_seconds'],
                    'published_at' => $vodInfo['published_at'],
                    'is_manual' => false,
                ]);
                $importedCount++;
            }
        }

        Log::info("VODs imported for streamer profile", [
            'profile_id' => $profile->id,
            'platform' => $profile->platform,
            'imported_count' => $importedCount,
            'total_fetched' => count($vodData)
        ]);

        return $importedCount;
    }

    /**
     * Check VOD health (verify links are still valid)
     *
     * @param StreamerVod $vod
     * @return bool
     */
    public function checkVodHealth(StreamerVod $vod): bool
    {
        try {
            $response = Http::timeout(10)->head($vod->vod_url);
            return $response->successful();
        } catch (Exception $e) {
            Log::warning("VOD health check failed", [
                'vod_id' => $vod->id,
                'vod_url' => $vod->vod_url,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Validate channel ownership for verification
     *
     * @param StreamerProfile $profile
     * @return bool
     * @throws Exception
     */
    public function validateChannelOwnership(StreamerProfile $profile): bool
    {
        $this->validateProfile($profile);
        
        if ($this->isRateLimited($profile->platform)) {
            throw new Exception("Rate limit exceeded for {$profile->platform} API");
        }

        try {
            $isValid = match ($profile->platform) {
                'twitch' => $this->validateTwitchOwnership($profile),
                'youtube' => $this->validateYouTubeOwnership($profile),
                'kick' => $this->validateKickOwnership($profile),
                default => false,
            };

            Log::info("Channel ownership validation", [
                'profile_id' => $profile->id,
                'platform' => $profile->platform,
                'is_valid' => $isValid
            ]);

            return $isValid;

        } catch (Exception $e) {
            Log::error("Failed to validate channel ownership", [
                'profile_id' => $profile->id,
                'platform' => $profile->platform,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Check if streamer is currently live
     *
     * @param StreamerProfile $profile
     * @return bool
     */
    public function checkLiveStatus(StreamerProfile $profile): bool
    {
        try {
            $this->validateProfile($profile);
            
            // Check cache first to avoid excessive API calls
            $cacheKey = "live_status_{$profile->platform}_{$profile->platform_user_id}";
            $cachedStatus = Cache::get($cacheKey);
            
            if ($cachedStatus !== null) {
                return $cachedStatus;
            }

            if ($this->isRateLimited($profile->platform)) {
                Log::warning("Rate limit exceeded for live status check", [
                    'profile_id' => $profile->id,
                    'platform' => $profile->platform
                ]);
                return false; // Return false instead of throwing exception for live status
            }

            $isLive = $this->retryService->executeForPlatform(function () use ($profile) {
                return match ($profile->platform) {
                    'twitch' => $this->checkTwitchLiveStatus($profile),
                    'youtube' => $this->checkYouTubeLiveStatus($profile),
                    'kick' => $this->checkKickLiveStatus($profile),
                    default => false,
                };
            }, $profile->platform);

            // Cache the result for 2 minutes to reduce API calls
            Cache::put($cacheKey, $isLive, 120);

            // Cache for fallback
            $this->errorHandler->cacheFallbackData($profile, 'live_status', [
                'is_live' => $isLive,
                'checked_at' => now()->toISOString()
            ]);

            Log::info("Checked live status", [
                'profile_id' => $profile->id,
                'platform' => $profile->platform,
                'is_live' => $isLive
            ]);

            return $isLive;

        } catch (Exception $e) {
            // For live status, we gracefully degrade instead of throwing exceptions
            Log::error("Failed to check live status", [
                'profile_id' => $profile->id,
                'platform' => $profile->platform,
                'error' => $e->getMessage()
            ]);

            // Try fallback data
            $fallbackData = $this->errorHandler->getFallbackData($profile, 'live_status');
            if ($fallbackData && isset($fallbackData['is_live'])) {
                return $fallbackData['is_live'];
            }

            return false; // Conservative fallback
        }
    }    /**

     * Fetch Twitch channel data
     *
     * @param StreamerProfile $profile
     * @return array
     * @throws Exception
     */
    private function fetchTwitchChannelData(StreamerProfile $profile): array
    {
        $response = Http::timeout(self::REQUEST_TIMEOUT)
            ->withHeaders([
                'Client-ID' => config('services.twitch.client_id'),
                'Authorization' => 'Bearer ' . $profile->oauth_token,
            ])
            ->get(self::PLATFORM_ENDPOINTS['twitch']['base_url'] . self::PLATFORM_ENDPOINTS['twitch']['user_endpoint'], [
                'id' => $profile->platform_user_id
            ]);

        if ($response->failed()) {
            throw new Exception("Twitch API request failed: " . $response->body());
        }

        $data = $response->json();
        
        if (empty($data['data'])) {
            throw new Exception("No user data returned from Twitch API");
        }

        $userData = $data['data'][0];

        return [
            'channel_name' => $userData['display_name'],
            'channel_url' => 'https://twitch.tv/' . $userData['login'],
            'profile_photo_url' => $userData['profile_image_url'],
            'bio' => $userData['description'] ?? null,
            'platform_user_id' => $userData['id'],
        ];
    }

    /**
     * Fetch YouTube channel data
     *
     * @param StreamerProfile $profile
     * @return array
     * @throws Exception
     */
    private function fetchYouTubeChannelData(StreamerProfile $profile): array
    {
        $response = Http::timeout(self::REQUEST_TIMEOUT)
            ->get(self::PLATFORM_ENDPOINTS['youtube']['base_url'] . self::PLATFORM_ENDPOINTS['youtube']['channel_endpoint'], [
                'part' => 'snippet,statistics',
                'id' => $profile->platform_user_id,
                'key' => config('services.youtube.api_key'),
            ]);

        if ($response->failed()) {
            throw new Exception("YouTube API request failed: " . $response->body());
        }

        $data = $response->json();
        
        if (empty($data['items'])) {
            throw new Exception("No channel data returned from YouTube API");
        }

        $channelData = $data['items'][0];
        $snippet = $channelData['snippet'];

        return [
            'channel_name' => $snippet['title'],
            'channel_url' => 'https://youtube.com/channel/' . $profile->platform_user_id,
            'profile_photo_url' => $snippet['thumbnails']['high']['url'] ?? $snippet['thumbnails']['default']['url'],
            'bio' => $snippet['description'] ?? null,
            'platform_user_id' => $channelData['id'],
        ];
    }

    /**
     * Fetch Kick channel data
     *
     * @param StreamerProfile $profile
     * @return array
     * @throws Exception
     */
    private function fetchKickChannelData(StreamerProfile $profile): array
    {
        // Kick API might use username instead of user ID
        $username = $this->extractUsernameFromUrl($profile->channel_url) ?? $profile->platform_user_id;
        
        $response = Http::timeout(self::REQUEST_TIMEOUT)
            ->get(self::PLATFORM_ENDPOINTS['kick']['base_url'] . self::PLATFORM_ENDPOINTS['kick']['user_endpoint'] . '/' . $username);

        if ($response->failed()) {
            throw new Exception("Kick API request failed: " . $response->body());
        }

        $data = $response->json();

        return [
            'channel_name' => $data['user']['username'] ?? $data['slug'],
            'channel_url' => 'https://kick.com/' . ($data['slug'] ?? $username),
            'profile_photo_url' => $data['user']['profile_pic'] ?? null,
            'bio' => $data['user']['bio'] ?? null,
            'platform_user_id' => (string) $data['id'],
        ];
    }

    /**
     * Fetch Twitch VODs
     *
     * @param StreamerProfile $profile
     * @param int $limit
     * @return array
     * @throws Exception
     */
    private function fetchTwitchVods(StreamerProfile $profile, int $limit): array
    {
        $response = Http::timeout(self::REQUEST_TIMEOUT)
            ->withHeaders([
                'Client-ID' => config('services.twitch.client_id'),
                'Authorization' => 'Bearer ' . $profile->oauth_token,
            ])
            ->get(self::PLATFORM_ENDPOINTS['twitch']['base_url'] . self::PLATFORM_ENDPOINTS['twitch']['videos_endpoint'], [
                'user_id' => $profile->platform_user_id,
                'type' => 'archive',
                'first' => $limit
            ]);

        if ($response->failed()) {
            throw new Exception("Twitch VODs API request failed: " . $response->body());
        }

        $data = $response->json();
        $vods = [];

        foreach ($data['data'] ?? [] as $video) {
            $vods[] = [
                'platform_vod_id' => $video['id'],
                'title' => $video['title'],
                'description' => $video['description'],
                'thumbnail_url' => $video['thumbnail_url'],
                'vod_url' => $video['url'],
                'duration_seconds' => $this->parseTwitchDuration($video['duration']),
                'published_at' => $video['created_at'],
            ];
        }

        return $vods;
    }

    /**
     * Fetch YouTube VODs
     *
     * @param StreamerProfile $profile
     * @param int $limit
     * @return array
     * @throws Exception
     */
    private function fetchYouTubeVods(StreamerProfile $profile, int $limit): array
    {
        $response = Http::timeout(self::REQUEST_TIMEOUT)
            ->get(self::PLATFORM_ENDPOINTS['youtube']['base_url'] . self::PLATFORM_ENDPOINTS['youtube']['videos_endpoint'], [
                'part' => 'snippet',
                'channelId' => $profile->platform_user_id,
                'type' => 'video',
                'order' => 'date',
                'maxResults' => $limit,
                'key' => config('services.youtube.api_key'),
            ]);

        if ($response->failed()) {
            throw new Exception("YouTube VODs API request failed: " . $response->body());
        }

        $data = $response->json();
        $vods = [];

        foreach ($data['items'] ?? [] as $video) {
            $vods[] = [
                'platform_vod_id' => $video['id']['videoId'],
                'title' => $video['snippet']['title'],
                'description' => $video['snippet']['description'],
                'thumbnail_url' => $video['snippet']['thumbnails']['high']['url'] ?? $video['snippet']['thumbnails']['default']['url'],
                'vod_url' => 'https://youtube.com/watch?v=' . $video['id']['videoId'],
                'duration_seconds' => null, // Would need additional API call to get duration
                'published_at' => $video['snippet']['publishedAt'],
            ];
        }

        return $vods;
    }

    /**
     * Fetch Kick VODs
     *
     * @param StreamerProfile $profile
     * @param int $limit
     * @return array
     * @throws Exception
     */
    private function fetchKickVods(StreamerProfile $profile, int $limit): array
    {
        $username = $this->extractUsernameFromUrl($profile->channel_url) ?? $profile->platform_user_id;
        
        $response = Http::timeout(self::REQUEST_TIMEOUT)
            ->get(str_replace('{username}', $username, self::PLATFORM_ENDPOINTS['kick']['base_url'] . self::PLATFORM_ENDPOINTS['kick']['videos_endpoint']), [
                'limit' => $limit
            ]);

        if ($response->failed()) {
            throw new Exception("Kick VODs API request failed: " . $response->body());
        }

        $data = $response->json();
        $vods = [];

        foreach ($data['data'] ?? [] as $video) {
            $vods[] = [
                'platform_vod_id' => (string) $video['id'],
                'title' => $video['session_title'] ?? 'Untitled Stream',
                'description' => null,
                'thumbnail_url' => $video['thumbnail'] ?? null,
                'vod_url' => 'https://kick.com/' . $username . '/videos/' . $video['id'],
                'duration_seconds' => $video['duration'] ?? null,
                'published_at' => $video['created_at'],
            ];
        }

        return $vods;
    }    
/**
     * Check Twitch live status
     *
     * @param StreamerProfile $profile
     * @return bool
     * @throws Exception
     */
    private function checkTwitchLiveStatus(StreamerProfile $profile): bool
    {
        $response = Http::timeout(self::REQUEST_TIMEOUT)
            ->withHeaders([
                'Client-ID' => config('services.twitch.client_id'),
                'Authorization' => 'Bearer ' . $profile->oauth_token,
            ])
            ->get(self::PLATFORM_ENDPOINTS['twitch']['base_url'] . self::PLATFORM_ENDPOINTS['twitch']['streams_endpoint'], [
                'user_id' => $profile->platform_user_id
            ]);

        if ($response->failed()) {
            throw new Exception("Twitch streams API request failed: " . $response->body());
        }

        $data = $response->json();
        return !empty($data['data']);
    }

    /**
     * Check YouTube live status
     *
     * @param StreamerProfile $profile
     * @return bool
     * @throws Exception
     */
    private function checkYouTubeLiveStatus(StreamerProfile $profile): bool
    {
        $response = Http::timeout(self::REQUEST_TIMEOUT)
            ->get(self::PLATFORM_ENDPOINTS['youtube']['base_url'] . self::PLATFORM_ENDPOINTS['youtube']['live_endpoint'], [
                'part' => 'snippet',
                'channelId' => $profile->platform_user_id,
                'type' => 'video',
                'eventType' => 'live',
                'key' => config('services.youtube.api_key'),
            ]);

        if ($response->failed()) {
            throw new Exception("YouTube live API request failed: " . $response->body());
        }

        $data = $response->json();
        return !empty($data['items']);
    }

    /**
     * Check Kick live status
     *
     * @param StreamerProfile $profile
     * @return bool
     * @throws Exception
     */
    private function checkKickLiveStatus(StreamerProfile $profile): bool
    {
        $username = $this->extractUsernameFromUrl($profile->channel_url) ?? $profile->platform_user_id;
        
        $response = Http::timeout(self::REQUEST_TIMEOUT)
            ->get(str_replace('{username}', $username, self::PLATFORM_ENDPOINTS['kick']['base_url'] . self::PLATFORM_ENDPOINTS['kick']['live_endpoint']));

        if ($response->failed()) {
            throw new Exception("Kick live API request failed: " . $response->body());
        }

        $data = $response->json();
        return isset($data['livestream']) && $data['livestream'] !== null;
    }

    /**
     * Validate Twitch channel ownership
     *
     * @param StreamerProfile $profile
     * @return bool
     * @throws Exception
     */
    private function validateTwitchOwnership(StreamerProfile $profile): bool
    {
        // For Twitch, if we have a valid OAuth token that can access user data,
        // and the user ID matches, then ownership is validated
        try {
            $channelData = $this->fetchTwitchChannelData($profile);
            return $channelData['platform_user_id'] === $profile->platform_user_id;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Validate YouTube channel ownership
     *
     * @param StreamerProfile $profile
     * @return bool
     * @throws Exception
     */
    private function validateYouTubeOwnership(StreamerProfile $profile): bool
    {
        // For YouTube, we validate by checking if we can access channel data
        // with the stored credentials
        try {
            $channelData = $this->fetchYouTubeChannelData($profile);
            return $channelData['platform_user_id'] === $profile->platform_user_id;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Validate Kick channel ownership
     *
     * @param StreamerProfile $profile
     * @return bool
     * @throws Exception
     */
    private function validateKickOwnership(StreamerProfile $profile): bool
    {
        // For Kick, we validate by checking if the channel data matches
        // what we have stored
        try {
            $channelData = $this->fetchKickChannelData($profile);
            return $channelData['platform_user_id'] === $profile->platform_user_id;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Validate streamer profile
     *
     * @param StreamerProfile $profile
     * @throws PlatformApiException
     */
    private function validateProfile(StreamerProfile $profile): void
    {
        if (!$profile->oauth_token) {
            throw PlatformApiException::authenticationFailed($profile->platform, [
                'profile_id' => $profile->id,
                'reason' => 'No OAuth token available'
            ]);
        }

        if (!$profile->platform_user_id) {
            throw PlatformApiException::invalidResponse($profile->platform, 'No platform user ID available', [
                'profile_id' => $profile->id
            ]);
        }

        $supportedPlatforms = ['twitch', 'youtube', 'kick'];
        if (!in_array($profile->platform, $supportedPlatforms)) {
            throw PlatformApiException::invalidResponse($profile->platform, "Unsupported platform: {$profile->platform}", [
                'profile_id' => $profile->id
            ]);
        }
    }

    /**
     * Check if platform API is rate limited
     *
     * @param string $platform
     * @return bool
     */
    private function isRateLimited(string $platform): bool
    {
        $cacheKey = self::RATE_LIMIT_PREFIX . $platform;
        return Cache::has($cacheKey);
    }

    /**
     * Set rate limit for platform
     *
     * @param string $platform
     * @param int $seconds
     */
    private function setRateLimit(string $platform, int $seconds = 300): void
    {
        $cacheKey = self::RATE_LIMIT_PREFIX . $platform;
        Cache::put($cacheKey, true, $seconds);
    }

    /**
     * Handle API errors and implement rate limiting
     *
     * @param string $platform
     * @param Exception $exception
     */
    private function handleApiError(string $platform, Exception $exception): void
    {
        $message = $exception->getMessage();
        
        // Check for rate limit errors
        if (str_contains(strtolower($message), 'rate limit') || 
            str_contains(strtolower($message), '429') ||
            str_contains(strtolower($message), 'too many requests')) {
            
            $this->setRateLimit($platform, 900); // 15 minutes
            $this->errorHandler->trackErrorFrequency($platform, 'rate_limited');
            
            Log::warning("Rate limit hit for platform API", [
                'platform' => $platform,
                'error' => $message
            ]);
        }

        // Check for API downtime
        if (str_contains(strtolower($message), '503') ||
            str_contains(strtolower($message), '502') ||
            str_contains(strtolower($message), 'service unavailable')) {
            
            $this->setRateLimit($platform, 600); // 10 minutes
            $this->errorHandler->trackErrorFrequency($platform, 'api_unavailable');
            
            Log::error("Platform API appears to be down", [
                'platform' => $platform,
                'error' => $message
            ]);
        }

        // Track general API errors
        $this->errorHandler->trackErrorFrequency($platform, 'api_error');

        Log::error("Platform API error", [
            'platform' => $platform,
            'error' => $message
        ]);
    }

    /**
     * Parse Twitch duration format (e.g., "1h2m3s") to seconds
     *
     * @param string $duration
     * @return int|null
     */
    private function parseTwitchDuration(string $duration): ?int
    {
        if (empty($duration)) {
            return null;
        }

        $totalSeconds = 0;
        
        // Parse hours
        if (preg_match('/(\d+)h/', $duration, $matches)) {
            $totalSeconds += (int) $matches[1] * 3600;
        }
        
        // Parse minutes
        if (preg_match('/(\d+)m/', $duration, $matches)) {
            $totalSeconds += (int) $matches[1] * 60;
        }
        
        // Parse seconds
        if (preg_match('/(\d+)s/', $duration, $matches)) {
            $totalSeconds += (int) $matches[1];
        }

        return $totalSeconds > 0 ? $totalSeconds : null;
    }

    /**
     * Extract username from channel URL
     *
     * @param string $url
     * @return string|null
     */
    private function extractUsernameFromUrl(string $url): ?string
    {
        if (preg_match('/kick\.com\/([^\/\?]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        if (preg_match('/twitch\.tv\/([^\/\?]+)/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }
}