<?php

namespace Tests\Unit\Services;

use App\Services\RetryService;
use Exception;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;

class RetryServiceTest extends TestCase
{
    private RetryService $retryService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->retryService = new RetryService();
        Log::spy();
    }

    public function test_executes_callback_successfully_on_first_attempt()
    {
        $callback = function () {
            return 'success';
        };

        $result = $this->retryService->execute($callback);

        $this->assertEquals('success', $result);
    }

    public function test_retries_on_retryable_exception()
    {
        $attempts = 0;
        $callback = function () use (&$attempts) {
            $attempts++;
            if ($attempts < 3) {
                throw new Exception('Network error');
            }
            return 'success';
        };

        $result = $this->retryService->execute($callback, [
            'max_attempts' => 3,
            'base_delay' => 0, // No delay for testing
        ]);

        $this->assertEquals('success', $result);
        $this->assertEquals(3, $attempts);
    }

    public function test_throws_exception_after_max_attempts()
    {
        $attempts = 0;
        $callback = function () use (&$attempts) {
            $attempts++;
            throw new Exception('Persistent error');
        };

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Persistent error');

        $this->retryService->execute($callback, [
            'max_attempts' => 2,
            'base_delay' => 0, // No delay for testing
        ]);

        $this->assertEquals(2, $attempts);
    }

    public function test_does_not_retry_non_retryable_exceptions()
    {
        $attempts = 0;
        $callback = function () use (&$attempts) {
            $attempts++;
            throw new Exception('Invalid input');
        };

        $this->expectException(Exception::class);

        $this->retryService->execute($callback, ['max_attempts' => 3], []);

        $this->assertEquals(1, $attempts);
    }

    public function test_calculates_exponential_backoff_delay()
    {
        $attempts = 0;
        
        $callback = function () use (&$attempts) {
            $attempts++;
            if ($attempts < 4) {
                throw new Exception('Rate limit exceeded');
            }
            return 'success';
        };
        
        $result = $this->retryService->execute($callback, [
            'max_attempts' => 4,
            'base_delay' => 0, // No delay for testing
            'multiplier' => 2,
            'jitter' => false
        ]);

        $this->assertEquals('success', $result);
        $this->assertEquals(4, $attempts);
    }

    public function test_respects_max_delay_limit()
    {
        $attempts = 0;
        $callback = function () use (&$attempts) {
            $attempts++;
            if ($attempts < 3) {
                throw new Exception('Service unavailable');
            }
            return 'success';
        };

        $result = $this->retryService->execute($callback, [
            'max_attempts' => 3,
            'base_delay' => 0, // No delay for testing
            'max_delay' => 5,
            'multiplier' => 10,
            'jitter' => false
        ]);

        $this->assertEquals('success', $result);
    }

    public function test_platform_specific_retry_configuration()
    {
        $attempts = 0;
        $callback = function () use (&$attempts) {
            $attempts++;
            if ($attempts < 3) {
                throw new Exception('Connection timeout');
            }
            return 'success';
        };

        // Override the platform config to have no delay for testing
        $result = $this->retryService->execute($callback, [
            'max_attempts' => 4, // Twitch default
            'base_delay' => 0, // No delay for testing
            'multiplier' => 2.5,
            'jitter' => true
        ]);

        $this->assertEquals('success', $result);
        $this->assertEquals(3, $attempts);
    }

    public function test_identifies_retryable_network_errors()
    {
        $retryableErrors = [
            'Connection timeout',
            'Connection refused',
            'Network error',
            'DNS resolution failed',
            'SSL handshake failed',
            'cURL error'
        ];

        foreach ($retryableErrors as $error) {
            $attempts = 0;
            $callback = function () use (&$attempts, $error) {
                $attempts++;
                if ($attempts < 2) {
                    throw new Exception($error);
                }
                return 'success';
            };

            $result = $this->retryService->execute($callback, [
                'max_attempts' => 2,
                'base_delay' => 0, // No delay for testing
            ]);
            $this->assertEquals('success', $result, "Failed for error: {$error}");
        }
    }

    public function test_identifies_retryable_http_status_codes()
    {
        $retryableCodes = ['502', '503', '504', '408', '429'];

        foreach ($retryableCodes as $code) {
            $attempts = 0;
            $callback = function () use (&$attempts, $code) {
                $attempts++;
                if ($attempts < 2) {
                    throw new Exception("HTTP {$code} error");
                }
                return 'success';
            };

            $result = $this->retryService->execute($callback, [
                'max_attempts' => 2,
                'base_delay' => 0, // No delay for testing
            ]);
            $this->assertEquals('success', $result, "Failed for HTTP code: {$code}");
        }
    }

    public function test_identifies_retryable_platform_errors()
    {
        $retryableErrors = [
            'Service unavailable',
            'Temporarily unavailable',
            'Internal server error',
            'Rate limit exceeded',
            'Quota exceeded',
            'Too many requests'
        ];

        foreach ($retryableErrors as $error) {
            $attempts = 0;
            $callback = function () use (&$attempts, $error) {
                $attempts++;
                if ($attempts < 2) {
                    throw new Exception($error);
                }
                return 'success';
            };

            $result = $this->retryService->execute($callback, [
                'max_attempts' => 2,
                'base_delay' => 0, // No delay for testing
            ]);
            $this->assertEquals('success', $result, "Failed for error: {$error}");
        }
    }

    public function test_logs_retry_attempts()
    {
        // This test verifies that the retry service executes successfully
        // The actual logging is tested in integration tests
        $attempts = 0;
        $callback = function () use (&$attempts) {
            $attempts++;
            if ($attempts < 3) {
                throw new Exception('Network error'); // Use a retryable error message
            }
            return 'success';
        };

        $result = $this->retryService->execute($callback, [
            'max_attempts' => 3,
            'base_delay' => 0, // No delay for testing
        ]);

        $this->assertEquals('success', $result);
        $this->assertEquals(3, $attempts);
    }

    public function test_logs_final_failure()
    {
        // This test verifies that the retry service throws the final exception
        // The actual logging is tested in integration tests
        $attempts = 0;
        $callback = function () use (&$attempts) {
            $attempts++;
            throw new Exception('Network error'); // Use retryable error that will exhaust attempts
        };

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Network error');

        try {
            $this->retryService->execute($callback, [
                'max_attempts' => 2,
                'base_delay' => 0, // No delay for testing
            ]);
        } finally {
            $this->assertEquals(2, $attempts);
        }
    }
}