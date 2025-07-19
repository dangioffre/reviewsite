<?php

namespace App\Exceptions;

/**
 * Exception for platform API related errors
 */
class PlatformApiException extends StreamerProfileException
{
    public const RATE_LIMITED = 2001;
    public const API_UNAVAILABLE = 2002;
    public const INVALID_RESPONSE = 2003;
    public const AUTHENTICATION_FAILED = 2004;
    public const RESOURCE_NOT_FOUND = 2005;
    public const QUOTA_EXCEEDED = 2006;
    public const NETWORK_ERROR = 2007;

    public static function rateLimited(string $platform, int $retryAfter = null, array $context = []): self
    {
        $message = "Rate limit exceeded for {$platform} API";
        if ($retryAfter) {
            $message .= ". Please try again in {$retryAfter} seconds.";
        }

        return new self(
            $message,
            self::RATE_LIMITED,
            null,
            array_merge($context, ['platform' => $platform, 'retry_after' => $retryAfter])
        );
    }

    public static function apiUnavailable(string $platform, array $context = []): self
    {
        return new self(
            "{$platform} API is currently unavailable. Please try again later.",
            self::API_UNAVAILABLE,
            null,
            array_merge($context, ['platform' => $platform])
        );
    }

    public static function invalidResponse(string $platform, string $details = '', array $context = []): self
    {
        $message = "Invalid response from {$platform} API";
        if ($details) {
            $message .= ": {$details}";
        }

        return new self(
            $message,
            self::INVALID_RESPONSE,
            null,
            array_merge($context, ['platform' => $platform, 'details' => $details])
        );
    }

    public static function authenticationFailed(string $platform, array $context = []): self
    {
        return new self(
            "Authentication failed with {$platform} API. Please reconnect your account.",
            self::AUTHENTICATION_FAILED,
            null,
            array_merge($context, ['platform' => $platform])
        );
    }

    public static function resourceNotFound(string $platform, string $resource, array $context = []): self
    {
        return new self(
            "{$resource} not found on {$platform}.",
            self::RESOURCE_NOT_FOUND,
            null,
            array_merge($context, ['platform' => $platform, 'resource' => $resource])
        );
    }

    public static function quotaExceeded(string $platform, array $context = []): self
    {
        return new self(
            "API quota exceeded for {$platform}. Please try again later.",
            self::QUOTA_EXCEEDED,
            null,
            array_merge($context, ['platform' => $platform])
        );
    }

    public static function networkError(string $platform, string $error, array $context = []): self
    {
        return new self(
            "Network error connecting to {$platform} API: {$error}",
            self::NETWORK_ERROR,
            null,
            array_merge($context, ['platform' => $platform, 'network_error' => $error])
        );
    }
}