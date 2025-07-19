<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\StreamerOAuthService;
use App\Models\User;
use App\Models\StreamerProfile;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Laravel\Socialite\Two\InvalidStateException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery;
use Exception;

class StreamerOAuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private StreamerOAuthService $service;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new StreamerOAuthService();
        $this->user = User::factory()->create();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_initiate_connection_for_supported_platforms()
    {
        $platforms = ['twitch', 'youtube', 'kick'];

        foreach ($platforms as $platform) {
            $mockDriver = Mockery::mock();
            $mockDriver->shouldReceive('scopes')->andReturnSelf();
            $mockDriver->shouldReceive('redirect')->andReturnSelf();
            $mockDriver->shouldReceive('getTargetUrl')->andReturn("https://{$platform}.com/oauth/authorize");

            Socialite::shouldReceive('driver')
                ->with($platform)
                ->andReturn($mockDriver);

            $redirectUrl = $this->service->initiateConnection($platform, $this->user);

            $this->assertEquals("https://{$platform}.com/oauth/authorize", $redirectUrl);
            $this->assertEquals($this->user->id, session('oauth_user_id'));
            $this->assertEquals($platform, session('oauth_platform'));
        }
    }

    /** @test */
    public function it_throws_exception_for_unsupported_platform()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unsupported platform: unsupported');

        $this->service->initiateConnection('unsupported', $this->user);
    }

    /** @test */
    public function it_throws_exception_when_user_already_has_platform_profile()
    {
        StreamerProfile::factory()->create([
            'user_id' => $this->user->id,
            'platform' => 'twitch'
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('User already has a twitch profile connected.');

        $this->service->initiateConnection('twitch', $this->user);
    }

    /** @test */
    public function it_can_handle_oauth_callback_and_create_profile()
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('12345');
        $socialiteUser->shouldReceive('getName')->andReturn('TestStreamer');
        $socialiteUser->shouldReceive('getNickname')->andReturn('teststreamer');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');
        $socialiteUser->token = 'access_token_123';
        $socialiteUser->refreshToken = 'refresh_token_123';
        $socialiteUser->expiresIn = 3600;
        $socialiteUser->user = ['description' => 'Test bio'];

        $mockDriver = Mockery::mock();
        $mockDriver->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->with('twitch')
            ->andReturn($mockDriver);

        $profile = $this->service->handleCallback('twitch', 'auth_code_123', $this->user);

        $this->assertInstanceOf(StreamerProfile::class, $profile);
        $this->assertEquals($this->user->id, $profile->user_id);
        $this->assertEquals('twitch', $profile->platform);
        $this->assertEquals('12345', $profile->platform_user_id);
        $this->assertEquals('TestStreamer', $profile->channel_name);
        $this->assertEquals('https://twitch.tv/teststreamer', $profile->channel_url);
        $this->assertEquals('https://example.com/avatar.jpg', $profile->profile_photo_url);
        $this->assertEquals('Test bio', $profile->bio);
        $this->assertEquals('access_token_123', $profile->oauth_token);
        $this->assertEquals('refresh_token_123', $profile->oauth_refresh_token);
        $this->assertNotNull($profile->oauth_expires_at);
    }

    /** @test */
    public function it_handles_youtube_callback_correctly()
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('UC123456789');
        $socialiteUser->shouldReceive('getName')->andReturn('YouTube Channel');
        $socialiteUser->shouldReceive('getNickname')->andReturn(null);
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://yt3.ggpht.com/avatar.jpg');
        $socialiteUser->token = 'yt_access_token';
        $socialiteUser->refreshToken = 'yt_refresh_token';
        $socialiteUser->expiresIn = 3600;
        $socialiteUser->user = ['snippet' => ['description' => 'YouTube bio']];

        $mockDriver = Mockery::mock();
        $mockDriver->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->with('youtube')
            ->andReturn($mockDriver);

        $profile = $this->service->handleCallback('youtube', 'auth_code_123', $this->user);

        $this->assertEquals('youtube', $profile->platform);
        $this->assertEquals('UC123456789', $profile->platform_user_id);
        $this->assertEquals('YouTube Channel', $profile->channel_name);
        $this->assertEquals('https://youtube.com/channel/UC123456789', $profile->channel_url);
        $this->assertEquals('YouTube bio', $profile->bio);
    }

    /** @test */
    public function it_handles_kick_callback_correctly()
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('kick123');
        $socialiteUser->shouldReceive('getName')->andReturn('KickStreamer');
        $socialiteUser->shouldReceive('getNickname')->andReturn('kickstreamer');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://kick.com/avatar.jpg');
        $socialiteUser->token = 'kick_access_token';
        $socialiteUser->refreshToken = null;
        $socialiteUser->expiresIn = null;
        $socialiteUser->user = ['bio' => 'Kick bio'];

        $mockDriver = Mockery::mock();
        $mockDriver->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->with('kick')
            ->andReturn($mockDriver);

        $profile = $this->service->handleCallback('kick', 'auth_code_123', $this->user);

        $this->assertEquals('kick', $profile->platform);
        $this->assertEquals('kick123', $profile->platform_user_id);
        $this->assertEquals('KickStreamer', $profile->channel_name);
        $this->assertEquals('https://kick.com/kickstreamer', $profile->channel_url);
        $this->assertEquals('Kick bio', $profile->bio);
        $this->assertNull($profile->oauth_expires_at);
    }

    /** @test */
    public function it_throws_exception_when_platform_account_already_connected_to_another_user()
    {
        $otherUser = User::factory()->create();
        StreamerProfile::factory()->create([
            'user_id' => $otherUser->id,
            'platform' => 'twitch',
            'platform_user_id' => '12345'
        ]);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('12345');

        $mockDriver = Mockery::mock();
        $mockDriver->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->with('twitch')
            ->andReturn($mockDriver);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('This twitch account is already connected to another user.');

        $this->service->handleCallback('twitch', 'auth_code_123', $this->user);
    }

    /** @test */
    public function it_handles_invalid_state_exception()
    {
        $mockDriver = Mockery::mock();
        $mockDriver->shouldReceive('user')->andThrow(new InvalidStateException());

        Socialite::shouldReceive('driver')
            ->with('twitch')
            ->andReturn($mockDriver);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('OAuth state validation failed. Please try connecting again.');

        $this->service->handleCallback('twitch', 'auth_code_123', $this->user);
    }

    /** @test */
    public function it_can_refresh_oauth_token()
    {
        $profile = StreamerProfile::factory()->create([
            'user_id' => $this->user->id,
            'platform' => 'twitch',
            'oauth_refresh_token' => 'refresh_token_123',
            'oauth_expires_at' => now()->addHour()
        ]);

        $refreshResponse = Mockery::mock();
        $refreshResponse->token = 'new_access_token';
        $refreshResponse->refreshToken = 'new_refresh_token';
        $refreshResponse->expiresIn = 3600;

        $mockDriver = Mockery::mock();
        $mockDriver->shouldReceive('refreshToken')
            ->with('refresh_token_123')
            ->andReturn($refreshResponse);

        Socialite::shouldReceive('driver')
            ->with('twitch')
            ->andReturn($mockDriver);

        $result = $this->service->refreshToken($profile);

        $this->assertTrue($result);
        $profile->refresh();
        $this->assertEquals('new_access_token', $profile->oauth_token);
        $this->assertEquals('new_refresh_token', $profile->oauth_refresh_token);
    }

    /** @test */
    public function it_returns_false_when_no_refresh_token_available()
    {
        $profile = StreamerProfile::factory()->create([
            'user_id' => $this->user->id,
            'platform' => 'twitch',
            'oauth_refresh_token' => null
        ]);

        $result = $this->service->refreshToken($profile);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_revoke_oauth_connection()
    {
        $profile = StreamerProfile::factory()->create([
            'user_id' => $this->user->id,
            'platform' => 'twitch',
            'oauth_token' => 'access_token_123',
            'oauth_refresh_token' => 'refresh_token_123',
            'oauth_expires_at' => now()->addHour()
        ]);

        $result = $this->service->revokeConnection($profile);

        $this->assertTrue($result);
        $profile->refresh();
        $this->assertNull($profile->oauth_token);
        $this->assertNull($profile->oauth_refresh_token);
        $this->assertNull($profile->oauth_expires_at);
    }

    /** @test */
    public function it_can_check_if_token_needs_refresh()
    {
        // Token expires in 10 minutes - should need refresh
        $profileNeedsRefresh = StreamerProfile::factory()->create([
            'oauth_expires_at' => now()->addMinutes(3)
        ]);

        // Token expires in 1 hour - should not need refresh
        $profileDoesNotNeedRefresh = StreamerProfile::factory()->create([
            'oauth_expires_at' => now()->addHour()
        ]);

        // No expiration time - should not need refresh
        $profileNoExpiration = StreamerProfile::factory()->create([
            'oauth_expires_at' => null
        ]);

        $this->assertTrue($this->service->tokenNeedsRefresh($profileNeedsRefresh));
        $this->assertFalse($this->service->tokenNeedsRefresh($profileDoesNotNeedRefresh));
        $this->assertFalse($this->service->tokenNeedsRefresh($profileNoExpiration));
    }

    /** @test */
    public function it_updates_existing_profile_on_callback()
    {
        $existingProfile = StreamerProfile::factory()->create([
            'user_id' => $this->user->id,
            'platform' => 'twitch',
            'channel_name' => 'OldName',
            'bio' => 'Old bio'
        ]);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn($existingProfile->platform_user_id);
        $socialiteUser->shouldReceive('getName')->andReturn('NewName');
        $socialiteUser->shouldReceive('getNickname')->andReturn('newname');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://example.com/new-avatar.jpg');
        $socialiteUser->token = 'new_access_token';
        $socialiteUser->refreshToken = 'new_refresh_token';
        $socialiteUser->expiresIn = 3600;
        $socialiteUser->user = ['description' => 'New bio'];

        $mockDriver = Mockery::mock();
        $mockDriver->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->with('twitch')
            ->andReturn($mockDriver);

        $profile = $this->service->handleCallback('twitch', 'auth_code_123', $this->user);

        $this->assertEquals($existingProfile->id, $profile->id);
        $this->assertEquals('NewName', $profile->channel_name);
        $this->assertEquals('New bio', $profile->bio);
        $this->assertEquals('new_access_token', $profile->oauth_token);
    }
}