<?php

namespace App\Services;

use App\Exceptions\OAuthException;
use App\Exceptions\PlatformApiException;
use App\Exceptions\StreamerProfileException;
use App\Models\StreamerProfile;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\Two\InvalidStateException;

/**
 * Centralized error handling service for streamer profile operations
 */
class StreamerErrorHandlingService
{
    private const ERROR_CACHE_PREFIX = 'streamer_error_';
    private const ERROR_CACHE_TTL = 300; // 5 minutes

    /**
     * Handle OAuth-related errors
     *
     * @param Exception $exception
     * @param string $platform
     * @param array $context
     * @return OAuthException
     */
    public function handleOAuthError(Exception $exception, string $platform, array $context = []): OAuthException
    {
        $this->logError('oauth', $exception, array_merge($context, ['platform' => $platform]));

        if ($exception instanceof InvalidStateException) {
            return OAuthException::invalidState($platform, $context);
        }

        $message = strtolower($exception->getMessage());

        if (str_contains($message, 'token') && str_contains($message, 'expired')) {
            return OAuthException::tokenExpired($platform, $context);
        }

        if (str_contains($message, 'already connected') || str_contains($message, 'duplicate')) {
            return OAuthException::accountAlreadyConnected($platform, $context);
        }

        if (str_contains($message, 'unsupported') || str_contains($message, 'invalid platform')) {
            return OAuthException::unsupportedPlatform($platform, $context);
        }

        // Generic OAuth provider error
        return OAuthException::providerError($platform, $exception->getMessage(), $context);
    }

    /**
     * Handle platform API errors
     *
     * @param Exception $exception
     * @param string $platform
     * @param array $context
     * @return PlatformApiException
     */
    public function handlePlatformApiError(Exception $exception, string $platform, array $context = []): PlatformApiException
    {
        $this->logError('platform_api', $exception, array_merge($context, ['platform' => $platform]));

        if ($exception instanceof ConnectionException) {
            return PlatformApiException::networkError($platform, $exception->getMessage(), $context);
        }

        if ($exception instanceof RequestException) {
            $response = $exception->response;
            
            if ($response) {
                $statusCode = $response->status();
                
                switch ($statusCode) {
                    case 401:
                    case 403:
                        return PlatformApiException::authenticationFailed($platform, $context);
                    case 404:
                        return PlatformApiException::resourceNotFound($platform, 'Resource', $context);
                    case 429:
                        $retryAfter = $this->extractRetryAfter($response->headers());
                        return PlatformApiException::rateLimited($platform, $retryAfter, $context);
                    case 502:
                    case 503:
                    case 504:
                        return PlatformApiException::apiUnavailable($platform, $context);
                }
            }
        }

        $message = strtolower($exception->getMessage());

        if (str_contains($message, 'rate limit') || str_contains($message, '429')) {
            $retryAfter = $this->extractRetryAfterFromMessage($message);
            return PlatformApiException::rateLimited($platform, $retryAfter, $context);
        }

        if (str_contains($message, 'quota') && str_contains($message, 'exceeded')) {
            return PlatformApiException::quotaExceeded($platform, $context);
        }

        if (str_contains($message, 'service unavailable') || 
            str_contains($message, '503') || 
            str_contains($message, '502')) {
            return PlatformApiException::apiUnavailable($platform, $context);
        }

        if (str_contains($message, 'invalid response') || 
            str_contains($message, 'malformed') ||
            str_contains($message, 'unexpected format')) {
            return PlatformApiException::invalidResponse($platform, $exception->getMessage(), $context);
        }

        // Generic network error
        return PlatformApiException::networkError($platform, $exception->getMessage(), $context);
    }

    /**
     * Get user-friendly error message
     *
     * @param Exception $exception
     * @return string
     */
    public function getUserFriendlyMessage(Exception $exception): string
    {
        if ($exception instanceof OAuthException) {
            return $this->getOAuthUserMessage($exception);
        }

        if ($exception instanceof PlatformApiException) {
            return $this->getPlatformApiUserMessage($exception);
        }

        if ($exception instanceof StreamerProfileException) {
            return $exception->getMessage();
        }

        // Generic fallback message
        return 'An unexpected error occurred. Please try again later or contact support if the problem persists.';
    }

    /**
     * Check if error should trigger a fallback mechanism
     *
     * @param Exception $exception
     * @return bool
     */
    public function shouldUseFallback(Exception $exception): bool
    {
        if ($exception instanceof PlatformApiException) {
            return in_array($exception->getCode(), [
                PlatformApiException::API_UNAVAILABLE,
                PlatformApiException::RATE_LIMITED,
                PlatformApiException::QUOTA_EXCEEDED,
                PlatformApiException::NETWORK_ERROR
            ]);
        }

        return false;
    }

    /**
     * Get fallback data when API is unavailable
     *
     * @param StreamerProfile $profile
     * @param string $operation
     * @return array|null
     */
    public function getFallbackData(StreamerProfile $profile, string $operation): ?array
    {
        $cacheKey = "fallback_{$operation}_{$profile->id}";
        
        return Cache::get($cacheKey, function () use ($profile, $operation) {
            return match ($operation) {
                'channel_data' => $this->getChannelDataFallback($profile),
                'live_status' => $this->getLiveStatusFallback($profile),
                'vods' => $this->getVodsFallback($profile),
                default => null
            };
        });
    }

    /**
     * Cache successful API responses for fallback use
     *
     * @param StreamerProfile $profile
     * @param string $operation
     * @param array $data
     */
    public function cacheFallbackData(StreamerProfile $profile, string $operation, array $data): void
    {
        $cacheKey = "fallback_{$operation}_{$profile->id}";
        Cache::put($cacheKey, $data, 3600); // Cache for 1 hour
    }

    /**
     * Track error frequency for monitoring
     *
     * @param string $platform
     * @param string $errorType
     */
    public function trackErrorFrequency(string $platform, string $errorType): void
    {
        $cacheKey = self::ERROR_CACHE_PREFIX . "{$platform}_{$errorType}_count";
        $count = Cache::get($cacheKey, 0);
        Cache::put($cacheKey, $count + 1, self::ERROR_CACHE_TTL);

        // Log high error frequency
        if ($count > 10) {
            Log::warning("High error frequency detected", [
                'platform' => $platform,
                'error_type' => $errorType,
                'count' => $count + 1,
                'time_window' => self::ERROR_CACHE_TTL . ' seconds'
            ]);
        }
    }

    /**
     * Check if platform is experiencing issues
     *
     * @param string $platform
     * @return bool
     */
    public function isPlatformExperiencingIssues(string $platform): bool
    {
        $errorTypes = ['api_unavailable', 'rate_limited', 'network_error'];
        $totalErrors = 0;

        foreach ($errorTypes as $errorType) {
            $cacheKey = self::ERROR_CACHE_PREFIX . "{$platform}_{$errorType}_count";
            $totalErrors += Cache::get($cacheKey, 0);
        }

        return $totalErrors > 20; // Threshold for considering platform problematic
    }

    /**
     * Log error with context
     *
     * @param string $category
     * @param Exception $exception
     * @param array $context
     */
    private function logError(string $category, Exception $exception, array $context = []): void
    {
        Log::error("Streamer {$category} error", [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'context' => $context
        ]);
    }

    /**
     * Get user-friendly OAuth error message
     *
     * @param OAuthException $exception
     * @return string
     */
    private function getOAuthUserMessage(OAuthException $exception): string
    {
        return match ($exception->getCode()) {
            OAuthException::INVALID_STATE => 'Connection security check failed. Please try connecting again.',
            OAuthException::TOKEN_EXPIRED => 'Your account connection has expired. Please reconnect your account.',
            OAuthException::TOKEN_REFRESH_FAILED => 'Unable to refresh your account connection. Please reconnect your account.',
            OAuthException::ACCOUNT_ALREADY_CONNECTED => 'This streaming account is already connected to another user.',
            OAuthException::UNSUPPORTED_PLATFORM => 'This streaming platform is not currently supported.',
            default => 'There was a problem connecting your streaming account. Please try again.'
        };
    }

    /**
     * Get user-friendly platform API error message
     *
     * @param PlatformApiException $exception
     * @return string
     */
    private function getPlatformApiUserMessage(PlatformApiException $exception): string
    {
        $context = $exception->getContext();
        $platform = ucfirst($context['platform'] ?? 'streaming platform');

        return match ($exception->getCode()) {
            PlatformApiException::RATE_LIMITED => "We're making too many requests to {$platform}. Please wait a moment and try again.",
            PlatformApiException::API_UNAVAILABLE => "{$platform} is currently experiencing issues. Please try again later.",
            PlatformApiException::AUTHENTICATION_FAILED => "Your {$platform} connection has expired. Please reconnect your account.",
            PlatformApiException::RESOURCE_NOT_FOUND => "The requested content was not found on {$platform}.",
            PlatformApiException::QUOTA_EXCEEDED => "We've reached our daily limit for {$platform} requests. Please try again tomorrow.",
            PlatformApiException::NETWORK_ERROR => "Unable to connect to {$platform}. Please check your internet connection and try again.",
            default => "There was a problem communicating with {$platform}. Please try again later."
        };
    }

    /**
     * Extract retry-after value from response headers
     *
     * @param array $headers
     * @return int|null
     */
    private function extractRetryAfter(array $headers): ?int
    {
        $retryAfter = $headers['Retry-After'][0] ?? $headers['retry-after'][0] ?? null;
        
        if ($retryAfter && is_numeric($retryAfter)) {
            return (int) $retryAfter;
        }

        return null;
    }

    /**
     * Extract retry-after value from error message
     *
     * @param string $message
     * @return int|null
     */
    private function extractRetryAfterFromMessage(string $message): ?int
    {
        if (preg_match('/retry.*?(\d+).*?second/i', $message, $matches)) {
            return (int) $matches[1];
        }

        if (preg_match('/wait.*?(\d+).*?second/i', $message, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * Get channel data fallback
     *
     * @param StreamerProfile $profile
     * @return array
     */
    private function getChannelDataFallback(StreamerProfile $profile): array
    {
        return [
            'channel_name' => $profile->channel_name,
            'channel_url' => $profile->channel_url,
            'profile_photo_url' => $profile->profile_photo_url,
            'bio' => $profile->bio,
            'platform_user_id' => $profile->platform_user_id,
            'is_fallback' => true
        ];
    }

    /**
     * Get live status fallback
     *
     * @param StreamerProfile $profile
     * @return array
     */
    private function getLiveStatusFallback(StreamerProfile $profile): array
    {
        return [
            'is_live' => false, // Conservative fallback
            'is_fallback' => true,
            'message' => 'Live status unavailable'
        ];
    }

    /**
     * Get VODs fallback
     *
     * @param StreamerProfile $profile
     * @return array
     */
    private function getVodsFallback(StreamerProfile $profile): array
    {
        // Return cached VODs from database
        $vods = $profile->vods()
            ->orderBy('published_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($vod) {
                return [
                    'platform_vod_id' => $vod->platform_vod_id,
                    'title' => $vod->title,
                    'description' => $vod->description,
                    'thumbnail_url' => $vod->thumbnail_url,
                    'vod_url' => $vod->vod_url,
                    'duration_seconds' => $vod->duration_seconds,
                    'published_at' => $vod->published_at,
                ];
            })
            ->toArray();

        return [
            'vods' => $vods,
            'is_fallback' => true
        ];
    }
}