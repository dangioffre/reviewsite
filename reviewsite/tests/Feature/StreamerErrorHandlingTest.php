<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\StreamerProfile;
use App\Services\StreamerOAuthService;
use App\Services\PlatformApiService;
use App\Exceptions\OAuthException;
use App\Exceptions\PlatformApiException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Mockery;

class StreamerErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Log::spy();
        Cache::spy();
    }

    public function test_oauth_controller_handles_invalid_state_gracefully()
    {
        $user = User::factory()->create();
        
        // Mock Socialite to throw InvalidStateException
        Socialite::shouldReceive('driver')
            ->with('twitch')
            ->andReturnSelf();
        Socialite::shouldReceive('user')
            ->andThrow(new InvalidStateException());

        // Set up session as if user initiated OAuth
        session(['oauth_user_id' => $user->id, 'oauth_platform' => 'twitch']);

        $response = $this->actingAs($user)
            ->get(route('streamer.oauth.callback', ['platform' => 'twitch', 'code' => 'test_code']));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
        
        $errorMessage = session('error');
        $this->assertStringContainsString('security check failed', $errorMessage);
    }

    public function test_oauth_controller_handles_account_already_connected_error()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        // Create existing profile for user1
        StreamerProfile::factory()->create([
            'user_id' => $user1->id,
            'platform' => 'twitch',
            'platform_user_id' => '12345'
        ]);

        // Mock Socialite to return same platform user ID
        $socialiteUser = Mockery::mock();
        $socialiteUser->shouldReceive('getId')->andReturn('12345');
        $socialiteUser->shouldReceive('getName')->andReturn('TestStreamer');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');
        $socialiteUser->token = 'test_token';
        $socialiteUser->refreshToken = 'refresh_token';
        $socialiteUser->expiresIn = 3600;

        Socialite::shouldReceive('driver')
            ->with('twitch')
            ->andReturnSelf();
        Socialite::shouldReceive('user')
            ->andReturn($socialiteUser);

        // Set up session for user2
        session(['oauth_user_id' => $user2->id, 'oauth_platform' => 'twitch']);

        $response = $this->actingAs($user2)
            ->get(route('streamer.oauth.callback', ['platform' => 'twitch', 'code' => 'test_code']));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
        
        $errorMessage = session('error');
        $this->assertStringContainsString('already connected to another user', $errorMessage);
    }

    public function test_platform_api_handles_rate_limiting_gracefully()
    {
        $profile = StreamerProfile::factory()->create([
            'platform' => 'twitch',
            'oauth_token' => 'valid_token'
        ]);

        // Mock HTTP response with 429 rate limit
        Http::fake([
            'api.twitch.tv/*' => Http::response(
                ['error' => 'Too Many Requests'],
                429,
                ['Retry-After' => '300']
            )
        ]);

        $platformApiService = app(PlatformApiService::class);

        $this->expectException(PlatformApiException::class);
        $this->expectExceptionCode(PlatformApiException::RATE_LIMITED);

        try {
            $platformApiService->fetchChannelData($profile);
        } catch (PlatformApiException $e) {
            $this->assertEquals(['platform' => 'twitch', 'retry_after' => 300], $e->getContext());
            throw $e;
        }
    }

    public function test_platform_api_uses_fallback_data_when_api_unavailable()
    {
        $profile = StreamerProfile::factory()->create([
            'platform' => 'youtube',
            'oauth_token' => 'valid_token',
            'channel_name' => 'TestChannel',
            'channel_url' => 'https://youtube.com/channel/test',
            'bio' => 'Test bio'
        ]);

        // Cache some fallback data
        Cache::put("fallback_channel_data_{$profile->id}", [
            'channel_name' => 'TestChannel',
            'channel_url' => 'https://youtube.com/channel/test',
            'bio' => 'Test bio',
            'is_fallback' => true
        ], 3600);

        // Mock HTTP response with 503 service unavailable
        Http::fake([
            'www.googleapis.com/*' => Http::response(
                ['error' => 'Service Unavailable'],
                503
            )
        ]);

        $platformApiService = app(PlatformApiService::class);
        $result = $platformApiService->fetchChannelData($profile);

        $this->assertTrue($result['is_fallback']);
        $this->assertEquals('TestChannel', $result['channel_name']);
    }

    public function test_live_status_check_degrades_gracefully_on_error()
    {
        $profile = StreamerProfile::factory()->create([
            'platform' => 'kick',
            'oauth_token' => 'valid_token'
        ]);

        // Mock HTTP failure
        Http::fake([
            'kick.com/*' => Http::response('', 500)
        ]);

        $platformApiService = app(PlatformApiService::class);
        $isLive = $platformApiService->checkLiveStatus($profile);

        // Should return false instead of throwing exception
        $this->assertFalse($isLive);
    }

    public function test_oauth_service_retries_on_network_errors()
    {
        $user = User::factory()->create();

        // Mock Socialite to fail first time, succeed second time
        $callCount = 0;
        Socialite::shouldReceive('driver')
            ->with('twitch')
            ->andReturnUsing(function () use (&$callCount) {
                $callCount++;
                if ($callCount === 1) {
                    throw new \Exception('Connection timeout');
                }
                return Socialite::getFacadeRoot();
            });

        Socialite::shouldReceive('scopes')
            ->andReturnSelf();
        Socialite::shouldReceive('redirect')
            ->andReturnSelf();
        Socialite::shouldReceive('getTargetUrl')
            ->andReturn('https://id.twitch.tv/oauth2/authorize?...');

        $oauthService = app(StreamerOAuthService::class);
        $redirectUrl = $oauthService->initiateConnection('twitch', $user);

        $this->assertStringContainsString('twitch.tv', $redirectUrl);
        $this->assertEquals(2, $callCount); // Should have retried once
    }

    public function test_platform_api_caches_successful_responses_for_fallback()
    {
        $profile = StreamerProfile::factory()->create([
            'platform' => 'twitch',
            'oauth_token' => 'valid_token'
        ]);

        // Mock successful HTTP response
        Http::fake([
            'api.twitch.tv/*' => Http::response([
                'data' => [[
                    'id' => '12345',
                    'display_name' => 'TestStreamer',
                    'login' => 'teststreamer',
                    'profile_image_url' => 'https://example.com/avatar.jpg',
                    'description' => 'Test description'
                ]]
            ])
        ]);

        $platformApiService = app(PlatformApiService::class);
        $result = $platformApiService->fetchChannelData($profile);

        // Verify data was cached for fallback
        Cache::shouldHaveReceived('put')
            ->once()
            ->withArgs(function ($key, $data, $ttl) use ($profile) {
                return $key === "fallback_channel_data_{$profile->id}" &&
                       isset($data['channel_name']) &&
                       $ttl === 3600;
            });
    }

    public function test_error_frequency_tracking_logs_warnings()
    {
        $profile = StreamerProfile::factory()->create([
            'platform' => 'youtube',
            'oauth_token' => 'valid_token'
        ]);

        // Mock high error count in cache
        Cache::shouldReceive('get')
            ->with('streamer_error_youtube_api_error_count', 0)
            ->andReturn(15); // Above threshold

        // Mock HTTP failure
        Http::fake([
            'www.googleapis.com/*' => Http::response('', 500)
        ]);

        $platformApiService = app(PlatformApiService::class);

        try {
            $platformApiService->fetchChannelData($profile);
        } catch (PlatformApiException $e) {
            // Expected
        }

        Log::shouldHaveReceived('warning')
            ->once()
            ->withArgs(function ($message, $context) {
                return str_contains($message, 'High error frequency detected') &&
                       $context['platform'] === 'youtube';
            });
    }

    public function test_oauth_callback_handles_provider_error_responses()
    {
        $user = User::factory()->create();
        session(['oauth_user_id' => $user->id, 'oauth_platform' => 'twitch']);

        // Simulate OAuth provider error response
        $response = $this->actingAs($user)
            ->get(route('streamer.oauth.callback', [
                'platform' => 'twitch',
                'error' => 'access_denied',
                'error_description' => 'The user denied the request'
            ]));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
        
        $errorMessage = session('error');
        $this->assertStringContainsString('user denied', $errorMessage);
    }

    public function test_vod_import_continues_on_partial_failures()
    {
        $profile = StreamerProfile::factory()->create([
            'platform' => 'twitch',
            'oauth_token' => 'valid_token'
        ]);

        // Mock API response with some valid and some invalid VODs
        Http::fake([
            'api.twitch.tv/helix/videos*' => Http::response([
                'data' => [
                    [
                        'id' => 'valid_vod_1',
                        'title' => 'Valid VOD 1',
                        'description' => 'Description 1',
                        'thumbnail_url' => 'https://example.com/thumb1.jpg',
                        'url' => 'https://twitch.tv/videos/valid_vod_1',
                        'duration' => '1h30m45s',
                        'created_at' => '2023-01-01T00:00:00Z'
                    ],
                    [
                        'id' => 'valid_vod_2',
                        'title' => 'Valid VOD 2',
                        'description' => 'Description 2',
                        'thumbnail_url' => 'https://example.com/thumb2.jpg',
                        'url' => 'https://twitch.tv/videos/valid_vod_2',
                        'duration' => '2h15m30s',
                        'created_at' => '2023-01-02T00:00:00Z'
                    ]
                ]
            ])
        ]);

        $platformApiService = app(PlatformApiService::class);
        $importedCount = $platformApiService->importVods($profile, 10);

        $this->assertEquals(2, $importedCount);
        $this->assertDatabaseCount('streamer_vods', 2);
    }
}