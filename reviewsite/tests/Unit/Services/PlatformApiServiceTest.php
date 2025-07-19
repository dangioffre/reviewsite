<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\PlatformApiService;
use App\Models\StreamerProfile;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Exception;
use Mockery;

class PlatformApiServiceTest extends TestCase
{
    use RefreshDatabase;
    private PlatformApiService $service;
    private StreamerProfile $twitchProfile;
    private StreamerProfile $youtubeProfile;
    private StreamerProfile $kickProfile;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = new PlatformApiService();
        
        // Create test user
        $user = User::factory()->create();
        
        // Create test profiles for each platform
        $this->twitchProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'platform' => 'twitch',
            'platform_user_id' => '123456789',
            'channel_name' => 'TestStreamer',
            'channel_url' => 'https://twitch.tv/teststreamer',
            'oauth_token' => 'test_token_123',
        ]);

        $this->youtubeProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'platform' => 'youtube',
            'platform_user_id' => 'UC123456789',
            'channel_name' => 'Test YouTube Channel',
            'channel_url' => 'https://youtube.com/channel/UC123456789',
            'oauth_token' => 'youtube_token_123',
        ]);

        $this->kickProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'platform' => 'kick',
            'platform_user_id' => '987654321',
            'channel_name' => 'TestKickStreamer',
            'channel_url' => 'https://kick.com/testkickstreamer',
            'oauth_token' => 'kick_token_123',
        ]);

        // Set up config values
        Config::set('services.twitch.client_id', 'test_twitch_client_id');
        Config::set('services.youtube.api_key', 'test_youtube_api_key');
    }

    protected function tearDown(): void
    {
        Http::clearResolvedInstances();
        Cache::flush();
        parent::tearDown();
    }

    /** @test */
    public function it_fetches_twitch_channel_data_successfully()
    {
        // Mock successful Twitch API response
        Http::fake([
            'api.twitch.tv/helix/users*' => Http::response([
                'data' => [
                    [
                        'id' => '123456789',
                        'login' => 'teststreamer',
                        'display_name' => 'TestStreamer',
                        'profile_image_url' => 'https://example.com/avatar.jpg',
                        'description' => 'Test streamer bio',
                    ]
                ]
            ], 200)
        ]);

        $result = $this->service->fetchChannelData($this->twitchProfile);

        $this->assertEquals('TestStreamer', $result['channel_name']);
        $this->assertEquals('https://twitch.tv/teststreamer', $result['channel_url']);
        $this->assertEquals('https://example.com/avatar.jpg', $result['profile_photo_url']);
        $this->assertEquals('Test streamer bio', $result['bio']);
        $this->assertEquals('123456789', $result['platform_user_id']);

        // Verify the API was called with correct headers
        Http::assertSent(function ($request) {
            return $request->hasHeader('Client-ID', 'test_twitch_client_id') &&
                   $request->hasHeader('Authorization', 'Bearer test_token_123') &&
                   $request->url() === 'https://api.twitch.tv/helix/users?id=123456789';
        });
    }

    /** @test */
    public function it_fetches_youtube_channel_data_successfully()
    {
        // Mock successful YouTube API response
        Http::fake([
            'www.googleapis.com/youtube/v3/channels*' => Http::response([
                'items' => [
                    [
                        'id' => 'UC123456789',
                        'snippet' => [
                            'title' => 'Test YouTube Channel',
                            'description' => 'YouTube channel description',
                            'thumbnails' => [
                                'high' => [
                                    'url' => 'https://example.com/youtube-avatar.jpg'
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $result = $this->service->fetchChannelData($this->youtubeProfile);

        $this->assertEquals('Test YouTube Channel', $result['channel_name']);
        $this->assertEquals('https://youtube.com/channel/UC123456789', $result['channel_url']);
        $this->assertEquals('https://example.com/youtube-avatar.jpg', $result['profile_photo_url']);
        $this->assertEquals('YouTube channel description', $result['bio']);
        $this->assertEquals('UC123456789', $result['platform_user_id']);

        // Verify the API was called with correct parameters
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'www.googleapis.com/youtube/v3/channels') &&
                   str_contains($request->url(), 'part=snippet%2Cstatistics') &&
                   str_contains($request->url(), 'id=UC123456789') &&
                   str_contains($request->url(), 'key=test_youtube_api_key');
        });
    }

    /** @test */
    public function it_fetches_kick_channel_data_successfully()
    {
        // Mock successful Kick API response
        Http::fake([
            'kick.com/api/v2/channels/testkickstreamer' => Http::response([
                'id' => 987654321,
                'slug' => 'testkickstreamer',
                'user' => [
                    'username' => 'TestKickStreamer',
                    'profile_pic' => 'https://example.com/kick-avatar.jpg',
                    'bio' => 'Kick streamer bio',
                ]
            ], 200)
        ]);

        $result = $this->service->fetchChannelData($this->kickProfile);

        $this->assertEquals('TestKickStreamer', $result['channel_name']);
        $this->assertEquals('https://kick.com/testkickstreamer', $result['channel_url']);
        $this->assertEquals('https://example.com/kick-avatar.jpg', $result['profile_photo_url']);
        $this->assertEquals('Kick streamer bio', $result['bio']);
        $this->assertEquals('987654321', $result['platform_user_id']);
    }

    /** @test */
    public function it_throws_exception_for_invalid_profile()
    {
        $invalidProfile = StreamerProfile::factory()->create([
            'oauth_token' => null,
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No OAuth token available for profile');

        $this->service->fetchChannelData($invalidProfile);
    }

    /** @test */
    public function it_throws_exception_for_unsupported_platform()
    {
        // Create a mock profile with unsupported platform
        $invalidProfile = new StreamerProfile([
            'platform' => 'unsupported',
            'oauth_token' => 'test_token',
            'platform_user_id' => '123456',
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unsupported platform: unsupported');

        $this->service->fetchChannelData($invalidProfile);
    }

    /** @test */
    public function it_fetches_twitch_vods_successfully()
    {
        // Mock successful Twitch VODs API response
        Http::fake([
            'api.twitch.tv/helix/videos*' => Http::response([
                'data' => [
                    [
                        'id' => 'vod123',
                        'title' => 'Test Stream VOD',
                        'description' => 'Test stream description',
                        'thumbnail_url' => 'https://example.com/thumbnail.jpg',
                        'url' => 'https://twitch.tv/videos/vod123',
                        'duration' => '1h30m45s',
                        'created_at' => '2024-01-01T12:00:00Z',
                    ],
                    [
                        'id' => 'vod456',
                        'title' => 'Another Stream VOD',
                        'description' => 'Another description',
                        'thumbnail_url' => 'https://example.com/thumbnail2.jpg',
                        'url' => 'https://twitch.tv/videos/vod456',
                        'duration' => '2h15m30s',
                        'created_at' => '2024-01-02T15:30:00Z',
                    ]
                ]
            ], 200)
        ]);

        $result = $this->service->fetchVods($this->twitchProfile, 5);

        $this->assertCount(2, $result);
        
        $firstVod = $result[0];
        $this->assertEquals('vod123', $firstVod['platform_vod_id']);
        $this->assertEquals('Test Stream VOD', $firstVod['title']);
        $this->assertEquals('Test stream description', $firstVod['description']);
        $this->assertEquals('https://example.com/thumbnail.jpg', $firstVod['thumbnail_url']);
        $this->assertEquals('https://twitch.tv/videos/vod123', $firstVod['vod_url']);
        $this->assertEquals(5445, $firstVod['duration_seconds']); // 1h30m45s = 5445 seconds
        $this->assertEquals('2024-01-01T12:00:00Z', $firstVod['published_at']);

        // Verify the API was called with correct parameters
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'user_id=123456789') &&
                   str_contains($request->url(), 'type=archive') &&
                   str_contains($request->url(), 'first=5');
        });
    }

    /** @test */
    public function it_fetches_youtube_vods_successfully()
    {
        // Mock successful YouTube search API response
        Http::fake([
            'www.googleapis.com/youtube/v3/search*' => Http::response([
                'items' => [
                    [
                        'id' => [
                            'videoId' => 'youtube_video_123'
                        ],
                        'snippet' => [
                            'title' => 'YouTube Video Title',
                            'description' => 'YouTube video description',
                            'publishedAt' => '2024-01-01T12:00:00Z',
                            'thumbnails' => [
                                'high' => [
                                    'url' => 'https://example.com/yt-thumb.jpg'
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $result = $this->service->fetchVods($this->youtubeProfile, 3);

        $this->assertCount(1, $result);
        
        $firstVod = $result[0];
        $this->assertEquals('youtube_video_123', $firstVod['platform_vod_id']);
        $this->assertEquals('YouTube Video Title', $firstVod['title']);
        $this->assertEquals('YouTube video description', $firstVod['description']);
        $this->assertEquals('https://example.com/yt-thumb.jpg', $firstVod['thumbnail_url']);
        $this->assertEquals('https://youtube.com/watch?v=youtube_video_123', $firstVod['vod_url']);
        $this->assertEquals('2024-01-01T12:00:00Z', $firstVod['published_at']);
    }

    /** @test */
    public function it_checks_twitch_live_status_when_live()
    {
        // Mock Twitch streams API response indicating live
        Http::fake([
            'api.twitch.tv/helix/streams*' => Http::response([
                'data' => [
                    [
                        'id' => 'stream123',
                        'user_id' => '123456789',
                        'game_name' => 'Test Game',
                        'type' => 'live',
                    ]
                ]
            ], 200)
        ]);

        $result = $this->service->checkLiveStatus($this->twitchProfile);

        $this->assertTrue($result);

        // Verify the API was called correctly
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'user_id=123456789') &&
                   $request->hasHeader('Client-ID', 'test_twitch_client_id') &&
                   $request->hasHeader('Authorization', 'Bearer test_token_123');
        });
    }

    /** @test */
    public function it_checks_twitch_live_status_when_offline()
    {
        // Mock Twitch streams API response indicating offline
        Http::fake([
            'api.twitch.tv/helix/streams*' => Http::response([
                'data' => []
            ], 200)
        ]);

        $result = $this->service->checkLiveStatus($this->twitchProfile);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_caches_live_status_results()
    {
        // Mock successful API response
        Http::fake([
            'api.twitch.tv/helix/streams*' => Http::response([
                'data' => [['id' => 'stream123']]
            ], 200)
        ]);

        // First call should hit the API
        $result1 = $this->service->checkLiveStatus($this->twitchProfile);
        $this->assertTrue($result1);

        // Second call should use cache
        $result2 = $this->service->checkLiveStatus($this->twitchProfile);
        $this->assertTrue($result2);

        // Verify API was only called once
        Http::assertSentCount(1);
    }

    /** @test */
    public function it_handles_rate_limiting_gracefully()
    {
        // Set rate limit in cache
        Cache::put('platform_api_rate_limit_twitch', true, 300);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Rate limit exceeded for twitch API');

        $this->service->fetchChannelData($this->twitchProfile);
    }

    /** @test */
    public function it_handles_api_errors_and_sets_rate_limits()
    {
        // Mock API response with rate limit error
        Http::fake([
            'api.twitch.tv/helix/users*' => Http::response([
                'error' => 'Too Many Requests',
                'message' => 'Rate limit exceeded'
            ], 429)
        ]);

        try {
            $this->service->fetchChannelData($this->twitchProfile);
        } catch (Exception $e) {
            // Exception should be thrown
            $this->assertStringContainsString('Twitch API request failed', $e->getMessage());
        }

        // Rate limit should be set in cache
        $this->assertTrue(Cache::has('platform_api_rate_limit_twitch'));
    }

    /** @test */
    public function it_handles_api_downtime_gracefully()
    {
        // Mock API response with service unavailable error
        Http::fake([
            'api.twitch.tv/helix/users*' => Http::response([
                'error' => 'Service Unavailable'
            ], 503)
        ]);

        try {
            $this->service->fetchChannelData($this->twitchProfile);
        } catch (Exception $e) {
            $this->assertStringContainsString('Twitch API request failed', $e->getMessage());
        }

        // Rate limit should be set for API downtime
        $this->assertTrue(Cache::has('platform_api_rate_limit_twitch'));
    }

    /** @test */
    public function it_returns_false_for_live_status_on_error_instead_of_throwing()
    {
        // Mock API error
        Http::fake([
            'api.twitch.tv/helix/streams*' => Http::response([], 500)
        ]);

        // Should return false instead of throwing exception
        $result = $this->service->checkLiveStatus($this->twitchProfile);
        $this->assertFalse($result);
    }

    /** @test */
    public function it_parses_twitch_duration_correctly()
    {
        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('parseTwitchDuration');
        $method->setAccessible(true);

        $this->assertEquals(3661, $method->invoke($this->service, '1h1m1s')); // 1 hour, 1 minute, 1 second
        $this->assertEquals(3600, $method->invoke($this->service, '1h')); // 1 hour
        $this->assertEquals(60, $method->invoke($this->service, '1m')); // 1 minute
        $this->assertEquals(1, $method->invoke($this->service, '1s')); // 1 second
        $this->assertEquals(5445, $method->invoke($this->service, '1h30m45s')); // 1h30m45s
        $this->assertNull($method->invoke($this->service, '')); // Empty string
    }

    /** @test */
    public function it_extracts_username_from_urls_correctly()
    {
        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('extractUsernameFromUrl');
        $method->setAccessible(true);

        $this->assertEquals('testuser', $method->invoke($this->service, 'https://kick.com/testuser'));
        $this->assertEquals('testuser', $method->invoke($this->service, 'https://kick.com/testuser?param=value'));
        $this->assertEquals('testuser', $method->invoke($this->service, 'https://twitch.tv/testuser'));
        $this->assertNull($method->invoke($this->service, 'https://youtube.com/channel/UC123'));
    }

    /** @test */
    public function it_imports_vods_successfully()
    {
        // Mock successful Twitch VODs API response
        Http::fake([
            'api.twitch.tv/helix/videos*' => Http::response([
                'data' => [
                    [
                        'id' => 'vod123',
                        'title' => 'Test Stream VOD',
                        'description' => 'Test stream description',
                        'thumbnail_url' => 'https://example.com/thumbnail.jpg',
                        'url' => 'https://twitch.tv/videos/vod123',
                        'duration' => '1h30m45s',
                        'created_at' => '2024-01-01T12:00:00Z',
                    ],
                    [
                        'id' => 'vod456',
                        'title' => 'Another Stream VOD',
                        'description' => 'Another description',
                        'thumbnail_url' => 'https://example.com/thumbnail2.jpg',
                        'url' => 'https://twitch.tv/videos/vod456',
                        'duration' => '2h15m30s',
                        'created_at' => '2024-01-02T15:30:00Z',
                    ]
                ]
            ], 200)
        ]);

        $importedCount = $this->service->importVods($this->twitchProfile, 10);

        $this->assertEquals(2, $importedCount);

        // Verify VODs were created in database
        $this->assertDatabaseHas('streamer_vods', [
            'streamer_profile_id' => $this->twitchProfile->id,
            'platform_vod_id' => 'vod123',
            'title' => 'Test Stream VOD',
            'is_manual' => false,
        ]);

        $this->assertDatabaseHas('streamer_vods', [
            'streamer_profile_id' => $this->twitchProfile->id,
            'platform_vod_id' => 'vod456',
            'title' => 'Another Stream VOD',
            'is_manual' => false,
        ]);
    }

    /** @test */
    public function it_skips_duplicate_vods_during_import()
    {
        // Create existing VOD
        $this->twitchProfile->vods()->create([
            'platform_vod_id' => 'vod123',
            'title' => 'Existing VOD',
            'vod_url' => 'https://twitch.tv/videos/vod123',
            'is_manual' => false,
        ]);

        // Mock API response with one existing and one new VOD
        Http::fake([
            'api.twitch.tv/helix/videos*' => Http::response([
                'data' => [
                    [
                        'id' => 'vod123', // This already exists
                        'title' => 'Test Stream VOD',
                        'description' => 'Test stream description',
                        'thumbnail_url' => 'https://example.com/thumbnail.jpg',
                        'url' => 'https://twitch.tv/videos/vod123',
                        'duration' => '1h30m45s',
                        'created_at' => '2024-01-01T12:00:00Z',
                    ],
                    [
                        'id' => 'vod456', // This is new
                        'title' => 'New Stream VOD',
                        'description' => 'New description',
                        'thumbnail_url' => 'https://example.com/thumbnail2.jpg',
                        'url' => 'https://twitch.tv/videos/vod456',
                        'duration' => '2h15m30s',
                        'created_at' => '2024-01-02T15:30:00Z',
                    ]
                ]
            ], 200)
        ]);

        $importedCount = $this->service->importVods($this->twitchProfile, 10);

        // Should only import 1 new VOD (skip the duplicate)
        $this->assertEquals(1, $importedCount);

        // Verify only the new VOD was added
        $this->assertDatabaseHas('streamer_vods', [
            'streamer_profile_id' => $this->twitchProfile->id,
            'platform_vod_id' => 'vod456',
            'title' => 'New Stream VOD',
        ]);

        // Verify we still have 2 total VODs (1 existing + 1 new)
        $this->assertEquals(2, $this->twitchProfile->vods()->count());
    }

    /** @test */
    public function it_checks_vod_health_successfully()
    {
        $vod = $this->twitchProfile->vods()->create([
            'platform_vod_id' => 'vod123',
            'title' => 'Test VOD',
            'vod_url' => 'https://twitch.tv/videos/vod123',
            'is_manual' => false,
        ]);

        // Mock successful HEAD request
        Http::fake([
            'twitch.tv/videos/vod123' => Http::response('', 200)
        ]);

        $isHealthy = $this->service->checkVodHealth($vod);

        $this->assertTrue($isHealthy);

        Http::assertSent(function ($request) {
            return $request->method() === 'HEAD' &&
                   $request->url() === 'https://twitch.tv/videos/vod123';
        });
    }

    /** @test */
    public function it_detects_unhealthy_vods()
    {
        $vod = $this->twitchProfile->vods()->create([
            'platform_vod_id' => 'vod123',
            'title' => 'Test VOD',
            'vod_url' => 'https://twitch.tv/videos/vod123',
            'is_manual' => false,
        ]);

        // Mock failed HEAD request (404 Not Found)
        Http::fake([
            'twitch.tv/videos/vod123' => Http::response('', 404)
        ]);

        $isHealthy = $this->service->checkVodHealth($vod);

        $this->assertFalse($isHealthy);
    }

    /** @test */
    public function it_handles_vod_health_check_exceptions_gracefully()
    {
        $vod = $this->twitchProfile->vods()->create([
            'platform_vod_id' => 'vod123',
            'title' => 'Test VOD',
            'vod_url' => 'https://invalid-url',
            'is_manual' => false,
        ]);

        // Mock exception during HTTP request
        Http::fake(function () {
            throw new \Exception('Connection timeout');
        });

        $isHealthy = $this->service->checkVodHealth($vod);

        // Should return false instead of throwing exception
        $this->assertFalse($isHealthy);
    }
}