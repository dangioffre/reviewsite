<?php

namespace App\Exceptions;

/**
 * Exception for OAuth related errors
 */
class OAuthException extends StreamerProfileException
{
    public const INVALID_STATE = 1001;
    public const TOKEN_EXPIRED = 1002;
    public const TOKEN_REFRESH_FAILED = 1003;
    public const PROVIDER_ERROR = 1004;
    public const ACCOUNT_ALREADY_CONNECTED = 1005;
    public const UNSUPPORTED_PLATFORM = 1006;

    public static function invalidState(string $platform, array $context = []): self
    {
        return new self(
            "OAuth state validation failed for {$platform}. Please try connecting again.",
            self::INVALID_STATE,
            null,
            array_merge($context, ['platform' => $platform])
        );
    }

    public static function tokenExpired(string $platform, array $context = []): self
    {
        return new self(
            "OAuth token has expired for {$platform}. Please reconnect your account.",
            self::TOKEN_EXPIRED,
            null,
            array_merge($context, ['platform' => $platform])
        );
    }

    public static function tokenRefreshFailed(string $platform, array $context = []): self
    {
        return new self(
            "Failed to refresh OAuth token for {$platform}. Please reconnect your account.",
            self::TOKEN_REFRESH_FAILED,
            null,
            array_merge($context, ['platform' => $platform])
        );
    }

    public static function providerError(string $platform, string $error, array $context = []): self
    {
        return new self(
            "OAuth provider error for {$platform}: {$error}",
            self::PROVIDER_ERROR,
            null,
            array_merge($context, ['platform' => $platform, 'provider_error' => $error])
        );
    }

    public static function accountAlreadyConnected(string $platform, array $context = []): self
    {
        return new self(
            "This {$platform} account is already connected to another user.",
            self::ACCOUNT_ALREADY_CONNECTED,
            null,
            array_merge($context, ['platform' => $platform])
        );
    }

    public static function unsupportedPlatform(string $platform, array $context = []): self
    {
        return new self(
            "Platform '{$platform}' is not supported.",
            self::UNSUPPORTED_PLATFORM,
            null,
            array_merge($context, ['platform' => $platform])
        );
    }
}