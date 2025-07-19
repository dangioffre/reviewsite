<?php

namespace Tests\Unit\Services;

use App\Services\StreamerErrorHandlingService;
use App\Exceptions\OAuthException;
use App\Exceptions\PlatformApiException;
use App\Models\StreamerProfile;
use App\Models\StreamerVod;
use Exception;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Two\InvalidStateException;
use Mockery;

class StreamerErrorHandlingServiceTest extends TestCase
{
    use RefreshDatabase;
    
    private StreamerErrorHandlingService $errorHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->errorHandler = new StreamerErrorHandlingService();
        Log::spy();
        Cache::spy();
    }

    public function test_handles_oauth_invalid_state_exception()
    {
        $exception = new InvalidStateException();
        
        $result = $this->errorHandler->handleOAuthError($exception, 'twitch');
        
        $this->assertInstanceOf(OAuthException::class, $result);
        $this->assertEquals(OAuthException::INVALID_STATE, $result->getCode());
        $this->assertStringContainsString('state validation failed', $result->getMessage());
    }

    public function test_handles_oauth_token_expired_error()
    {
        $exception = new Exception('OAuth token has expired');
        
        $result = $this->errorHandler->handleOAuthError($exception, 'youtube');
        
        $this->assertInstanceOf(OAuthException::class, $result);
        $this->assertEquals(OAuthException::TOKEN_EXPIRED, $result->getCode());
    }

    public function test_handles_oauth_account_already_connected_error()
    {
        $exception = new Exception('This account is already connected to another user');
        
        $result = $this->errorHandler->handleOAuthError($exception, 'kick');
        
        $this->assertInstanceOf(OAuthException::class, $result);
        $this->assertEquals(OAuthException::ACCOUNT_ALREADY_CONNECTED, $result->getCode());
    }

    public function test_handles_platform_api_connection_exception()
    {
        $exception = new ConnectionException('Connection timeout');
        
        $result = $this->errorHandler->handlePlatformApiError($exception, 'twitch');
        
        $this->assertInstanceOf(PlatformApiException::class, $result);
        $this->assertEquals(PlatformApiException::NETWORK_ERROR, $result->getCode());
    }

    public function test_handles_platform_api_request_exception_with_401()
    {
        $response = Mockery::mock(Response::class);
        $response->shouldReceive('status')->andReturn(401);
        $response->shouldReceive('headers')->andReturn([]);
        
        $exception = Mockery::mock(RequestException::class);
        $exception->shouldReceive('getMessage')->andReturn('Unauthorized');
        $exception->response = $response;
        
        $result = $this->errorHandler->handlePlatformApiError($exception, 'youtube');
        
        $this->assertInstanceOf(PlatformApiException::class, $result);
        $this->assertEquals(PlatformApiException::AUTHENTICATION_FAILED, $result->getCode());
    }

    public function test_handles_platform_api_request_exception_with_429()
    {
        $response = Mockery::mock(Response::class);
        $response->shouldReceive('status')->andReturn(429);
        $response->shouldReceive('headers')->andReturn(['Retry-After' => ['300']]);
        
        $exception = Mockery::mock(RequestException::class);
        $exception->shouldReceive('getMessage')->andReturn('Too Many Requests');
        $exception->response = $response;
        
        $result = $this->errorHandler->handlePlatformApiError($exception, 'kick');
        
        $this->assertInstanceOf(PlatformApiException::class, $result);
        $this->assertEquals(PlatformApiException::RATE_LIMITED, $result->getCode());
        $this->assertEquals(['platform' => 'kick', 'retry_after' => 300], $result->getContext());
    }

    public function test_handles_platform_api_request_exception_with_503()
    {
        $response = Mockery::mock(Response::class);
        $response->shouldReceive('status')->andReturn(503);
        $response->shouldReceive('headers')->andReturn([]);
        
        $exception = Mockery::mock(RequestException::class);
        $exception->shouldReceive('getMessage')->andReturn('Service Unavailable');
        $exception->response = $response;
        
        $result = $this->errorHandler->handlePlatformApiError($exception, 'twitch');
        
        $this->assertInstanceOf(PlatformApiException::class, $result);
        $this->assertEquals(PlatformApiException::API_UNAVAILABLE, $result->getCode());
    }

    public function test_extracts_retry_after_from_message()
    {
        $exception = new Exception('Rate limit exceeded. Please retry in 120 seconds.');
        
        $result = $this->errorHandler->handlePlatformApiError($exception, 'youtube');
        
        $this->assertInstanceOf(PlatformApiException::class, $result);
        $this->assertEquals(PlatformApiException::RATE_LIMITED, $result->getCode());
    }

    public function test_handles_quota_exceeded_error()
    {
        $exception = new Exception('API quota exceeded for today');
        
        $result = $this->errorHandler->handlePlatformApiError($exception, 'youtube');
        
        $this->assertInstanceOf(PlatformApiException::class, $result);
        $this->assertEquals(PlatformApiException::QUOTA_EXCEEDED, $result->getCode());
    }

    public function test_provides_user_friendly_oauth_messages()
    {
        $testCases = [
            [OAuthException::INVALID_STATE, 'Connection security check failed'],
            [OAuthException::TOKEN_EXPIRED, 'connection has expired'],
            [OAuthException::ACCOUNT_ALREADY_CONNECTED, 'already connected to another user'],
            [OAuthException::UNSUPPORTED_PLATFORM, 'not currently supported']
        ];

        foreach ($testCases as [$code, $expectedText]) {
            $exception = new OAuthException('Test message', $code);
            $message = $this->errorHandler->getUserFriendlyMessage($exception);
            
            $this->assertStringContainsString($expectedText, $message);
        }
    }

    public function test_provides_user_friendly_platform_api_messages()
    {
        $testCases = [
            [PlatformApiException::RATE_LIMITED, 'making too many requests'],
            [PlatformApiException::API_UNAVAILABLE, 'experiencing issues'],
            [PlatformApiException::AUTHENTICATION_FAILED, 'connection has expired'],
            [PlatformApiException::QUOTA_EXCEEDED, 'daily limit']
        ];

        foreach ($testCases as [$code, $expectedText]) {
            $exception = new PlatformApiException('Test message', $code, null, ['platform' => 'twitch']);
            $message = $this->errorHandler->getUserFriendlyMessage($exception);
            
            $this->assertStringContainsString($expectedText, $message);
        }
    }

    public function test_determines_fallback_usage()
    {
        $fallbackCodes = [
            PlatformApiException::API_UNAVAILABLE,
            PlatformApiException::RATE_LIMITED,
            PlatformApiException::QUOTA_EXCEEDED,
            PlatformApiException::NETWORK_ERROR
        ];

        foreach ($fallbackCodes as $code) {
            $exception = new PlatformApiException('Test', $code);
            $this->assertTrue($this->errorHandler->shouldUseFallback($exception));
        }

        $nonFallbackException = new PlatformApiException('Test', PlatformApiException::AUTHENTICATION_FAILED);
        $this->assertFalse($this->errorHandler->shouldUseFallback($nonFallbackException));
    }

    public function test_gets_channel_data_fallback()
    {
        $profile = StreamerProfile::factory()->create([
            'channel_name' => 'TestStreamer',
            'channel_url' => 'https://twitch.tv/teststreamer',
            'profile_photo_url' => 'https://example.com/photo.jpg',
            'bio' => 'Test bio',
            'platform_user_id' => '12345'
        ]);

        // Mock cache to return null so the closure executes
        Cache::shouldReceive('get')
            ->with("fallback_channel_data_{$profile->id}", \Mockery::type('Closure'))
            ->andReturnUsing(function ($key, $closure) {
                return $closure();
            });

        $fallbackData = $this->errorHandler->getFallbackData($profile, 'channel_data');

        $this->assertEquals([
            'channel_name' => 'TestStreamer',
            'channel_url' => 'https://twitch.tv/teststreamer',
            'profile_photo_url' => 'https://example.com/photo.jpg',
            'bio' => 'Test bio',
            'platform_user_id' => '12345',
            'is_fallback' => true
        ], $fallbackData);
    }

    public function test_gets_live_status_fallback()
    {
        $profile = StreamerProfile::factory()->create();

        // Mock cache to return null so the closure executes
        Cache::shouldReceive('get')
            ->with("fallback_live_status_{$profile->id}", \Mockery::type('Closure'))
            ->andReturnUsing(function ($key, $closure) {
                return $closure();
            });

        $fallbackData = $this->errorHandler->getFallbackData($profile, 'live_status');

        $this->assertEquals([
            'is_live' => false,
            'is_fallback' => true,
            'message' => 'Live status unavailable'
        ], $fallbackData);
    }

    public function test_caches_fallback_data()
    {
        $profile = StreamerProfile::factory()->create();
        $data = ['test' => 'data'];

        $this->errorHandler->cacheFallbackData($profile, 'test_operation', $data);

        Cache::shouldHaveReceived('put')
            ->once()
            ->with("fallback_test_operation_{$profile->id}", $data, 3600);
    }

    public function test_tracks_error_frequency()
    {
        $this->errorHandler->trackErrorFrequency('twitch', 'rate_limited');

        Cache::shouldHaveReceived('get')
            ->once()
            ->with('streamer_error_twitch_rate_limited_count', 0);

        Cache::shouldHaveReceived('put')
            ->once()
            ->with('streamer_error_twitch_rate_limited_count', 1, 300);
    }

    public function test_detects_platform_experiencing_issues()
    {
        // Mock high error counts
        Cache::shouldReceive('get')
            ->with('streamer_error_twitch_api_unavailable_count', 0)
            ->andReturn(10);
        Cache::shouldReceive('get')
            ->with('streamer_error_twitch_rate_limited_count', 0)
            ->andReturn(8);
        Cache::shouldReceive('get')
            ->with('streamer_error_twitch_network_error_count', 0)
            ->andReturn(5);

        $isExperiencingIssues = $this->errorHandler->isPlatformExperiencingIssues('twitch');

        $this->assertTrue($isExperiencingIssues);
    }

    public function test_logs_errors_with_context()
    {
        $exception = new Exception('Test error');
        $context = ['user_id' => 123];

        $this->errorHandler->handleOAuthError($exception, 'twitch', $context);

        Log::shouldHaveReceived('error')
            ->once()
            ->withArgs(function ($message, $logContext) use ($context) {
                return str_contains($message, 'oauth error') &&
                       $logContext['context'] === array_merge($context, ['platform' => 'twitch']);
            });
    }
}