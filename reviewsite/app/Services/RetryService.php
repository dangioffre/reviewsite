<?php

namespace App\Services;

use Closure;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Service for handling retry logic with exponential backoff
 */
class RetryService
{
    /**
     * Default retry configuration
     */
    private const DEFAULT_MAX_ATTEMPTS = 3;
    private const DEFAULT_BASE_DELAY = 1; // seconds
    private const DEFAULT_MAX_DELAY = 60; // seconds
    private const DEFAULT_MULTIPLIER = 2;

    /**
     * Execute a callback with retry logic and exponential backoff
     *
     * @param Closure $callback The function to execute
     * @param array $config Retry configuration
     * @param array $retryableExceptions List of exception classes that should trigger a retry
     * @return mixed
     * @throws Exception
     */
    public function execute(
        Closure $callback,
        array $config = [],
        array $retryableExceptions = [Exception::class]
    ) {
        $maxAttempts = $config['max_attempts'] ?? self::DEFAULT_MAX_ATTEMPTS;
        $baseDelay = $config['base_delay'] ?? self::DEFAULT_BASE_DELAY;
        $maxDelay = $config['max_delay'] ?? self::DEFAULT_MAX_DELAY;
        $multiplier = $config['multiplier'] ?? self::DEFAULT_MULTIPLIER;
        $jitter = $config['jitter'] ?? true;

        $attempt = 1;
        $lastException = null;

        while ($attempt <= $maxAttempts) {
            try {
                return $callback();
            } catch (Exception $e) {
                $lastException = $e;

                // Check if this exception should trigger a retry
                if (!$this->shouldRetry($e, $retryableExceptions, $attempt, $maxAttempts)) {
                    throw $e;
                }

                // Calculate delay for next attempt
                $delay = $this->calculateDelay($attempt, $baseDelay, $maxDelay, $multiplier, $jitter);

                Log::warning("Retry attempt {$attempt}/{$maxAttempts} failed, retrying in {$delay}s", [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'attempt' => $attempt,
                    'delay' => $delay
                ]);

                // Wait before next attempt
                if ($delay > 0) {
                    sleep($delay);
                }

                $attempt++;
            }
        }

        // All attempts failed, throw the last exception
        Log::error("All retry attempts failed", [
            'max_attempts' => $maxAttempts,
            'final_exception' => get_class($lastException),
            'message' => $lastException->getMessage()
        ]);

        throw $lastException;
    }

    /**
     * Execute with platform-specific retry configuration
     *
     * @param Closure $callback
     * @param string $platform
     * @return mixed
     * @throws Exception
     */
    public function executeForPlatform(Closure $callback, string $platform)
    {
        $config = $this->getPlatformRetryConfig($platform);
        $retryableExceptions = $this->getPlatformRetryableExceptions($platform);

        return $this->execute($callback, $config, $retryableExceptions);
    }

    /**
     * Determine if an exception should trigger a retry
     *
     * @param Exception $exception
     * @param array $retryableExceptions
     * @param int $attempt
     * @param int $maxAttempts
     * @return bool
     */
    private function shouldRetry(Exception $exception, array $retryableExceptions, int $attempt, int $maxAttempts): bool
    {
        // Don't retry if we've reached max attempts
        if ($attempt >= $maxAttempts) {
            return false;
        }

        // Check if exception type is retryable
        foreach ($retryableExceptions as $retryableClass) {
            if ($exception instanceof $retryableClass) {
                return $this->isRetryableError($exception);
            }
        }

        return false;
    }

    /**
     * Check if specific error conditions are retryable
     *
     * @param Exception $exception
     * @return bool
     */
    private function isRetryableError(Exception $exception): bool
    {
        $message = strtolower($exception->getMessage());

        // Network/connection errors are retryable
        $networkErrors = [
            'connection timeout',
            'connection refused',
            'network error',
            'dns resolution failed',
            'ssl handshake failed',
            'curl error'
        ];

        foreach ($networkErrors as $error) {
            if (str_contains($message, $error)) {
                return true;
            }
        }

        // HTTP status codes that are retryable
        $retryableHttpCodes = ['502', '503', '504', '408', '429'];
        foreach ($retryableHttpCodes as $code) {
            if (str_contains($message, $code)) {
                return true;
            }
        }

        // Platform-specific temporary errors
        $temporaryErrors = [
            'service unavailable',
            'temporarily unavailable',
            'internal server error',
            'rate limit',
            'quota exceeded',
            'too many requests'
        ];

        foreach ($temporaryErrors as $error) {
            if (str_contains($message, $error)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calculate delay with exponential backoff and optional jitter
     *
     * @param int $attempt
     * @param int $baseDelay
     * @param int $maxDelay
     * @param float $multiplier
     * @param bool $jitter
     * @return int
     */
    private function calculateDelay(int $attempt, int $baseDelay, int $maxDelay, float $multiplier, bool $jitter): int
    {
        // Calculate exponential backoff
        $delay = $baseDelay * pow($multiplier, $attempt - 1);

        // Apply maximum delay limit
        $delay = min($delay, $maxDelay);

        // Add jitter to prevent thundering herd
        if ($jitter) {
            $jitterAmount = $delay * 0.1; // 10% jitter
            $delay += mt_rand(-$jitterAmount, $jitterAmount);
        }

        return max(0, (int) $delay);
    }

    /**
     * Get platform-specific retry configuration
     *
     * @param string $platform
     * @return array
     */
    private function getPlatformRetryConfig(string $platform): array
    {
        return match ($platform) {
            'twitch' => [
                'max_attempts' => 4,
                'base_delay' => 2,
                'max_delay' => 120,
                'multiplier' => 2.5,
                'jitter' => true
            ],
            'youtube' => [
                'max_attempts' => 3,
                'base_delay' => 5,
                'max_delay' => 300,
                'multiplier' => 3,
                'jitter' => true
            ],
            'kick' => [
                'max_attempts' => 5,
                'base_delay' => 1,
                'max_delay' => 60,
                'multiplier' => 2,
                'jitter' => true
            ],
            default => [
                'max_attempts' => self::DEFAULT_MAX_ATTEMPTS,
                'base_delay' => self::DEFAULT_BASE_DELAY,
                'max_delay' => self::DEFAULT_MAX_DELAY,
                'multiplier' => self::DEFAULT_MULTIPLIER,
                'jitter' => true
            ]
        };
    }

    /**
     * Get platform-specific retryable exceptions
     *
     * @param string $platform
     * @return array
     */
    private function getPlatformRetryableExceptions(string $platform): array
    {
        return [
            \App\Exceptions\PlatformApiException::class,
            \Illuminate\Http\Client\ConnectionException::class,
            \Illuminate\Http\Client\RequestException::class,
            Exception::class
        ];
    }
}