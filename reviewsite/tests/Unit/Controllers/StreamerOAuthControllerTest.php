<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Http\Controllers\StreamerOAuthController;
use App\Services\StreamerOAuthService;
use App\Models\User;
use App\Models\StreamerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Exception;

class StreamerOAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    private StreamerOAuthService $mockService;
    private StreamerOAuthController $controller;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockService = Mockery::mock(StreamerOAuthService::class);
        $this->controller = new StreamerOAuthController($this->mockService);
        $this->user = User::factory()->create();
        Auth::login($this->user);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_redirects_to_oauth_provider()
    {
        $redirectUrl = 'https://twitch.tv/oauth/authorize?client_id=123';
        
        $this->mockService
            ->shouldReceive('initiateConnection')
            ->with('twitch', $this->user)
            ->andReturn($redirectUrl);

        $response = $this->controller->redirect('twitch');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals($redirectUrl, $response->getTargetUrl());
    }

    /** @test */
    public function it_handles_oauth_redirect_exception()
    {
        $this->mockService
            ->shouldReceive('initiateConnection')
            ->with('twitch', $this->user)
            ->andThrow(new Exception('Connection failed'));

        $response = $this->controller->redirect('twitch');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('dashboard', $response->getTargetUrl());
    }

    /** @test */
    public function it_handles_oauth_callback_successfully()
    {
        $streamerProfile = StreamerProfile::factory()->make([
            'id' => 1,
            'platform' => 'twitch'
        ]);

        session(['oauth_user_id' => $this->user->id, 'oauth_platform' => 'twitch']);

        $this->mockService
            ->shouldReceive('handleCallback')
            ->with('twitch', 'auth_code_123', $this->user)
            ->andReturn($streamerProfile);

        $request = Request::create('/auth/twitch/callback', 'GET', ['code' => 'auth_code_123']);
        $response = $this->controller->callback('twitch', $request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertNull(session('oauth_user_id'));
        $this->assertNull(session('oauth_platform'));
    }

    /** @test */
    public function it_handles_callback_without_code()
    {
        $request = Request::create('/auth/twitch/callback', 'GET');
        $response = $this->controller->callback('twitch', $request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('dashboard', $response->getTargetUrl());
    }

    /** @test */
    public function it_handles_callback_with_invalid_session()
    {
        $request = Request::create('/auth/twitch/callback', 'GET', ['code' => 'auth_code_123']);
        $response = $this->controller->callback('twitch', $request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('dashboard', $response->getTargetUrl());
    }

    /** @test */
    public function it_handles_callback_with_platform_mismatch()
    {
        session(['oauth_user_id' => $this->user->id, 'oauth_platform' => 'youtube']);

        $request = Request::create('/auth/twitch/callback', 'GET', ['code' => 'auth_code_123']);
        $response = $this->controller->callback('twitch', $request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('dashboard', $response->getTargetUrl());
    }

    /** @test */
    public function it_handles_callback_exception()
    {
        session(['oauth_user_id' => $this->user->id, 'oauth_platform' => 'twitch']);

        $this->mockService
            ->shouldReceive('handleCallback')
            ->with('twitch', 'auth_code_123', $this->user)
            ->andThrow(new Exception('Callback failed'));

        $request = Request::create('/auth/twitch/callback', 'GET', ['code' => 'auth_code_123']);
        $response = $this->controller->callback('twitch', $request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('dashboard', $response->getTargetUrl());
        $this->assertNull(session('oauth_user_id'));
        $this->assertNull(session('oauth_platform'));
    }

    /** @test */
    public function it_disconnects_oauth_connection()
    {
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $this->user->id,
            'platform' => 'twitch'
        ]);

        $this->mockService
            ->shouldReceive('revokeConnection')
            ->with(Mockery::on(function ($profile) use ($streamerProfile) {
                return $profile->id === $streamerProfile->id;
            }));

        $response = $this->controller->disconnect('twitch');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('dashboard', $response->getTargetUrl());
    }

    /** @test */
    public function it_handles_disconnect_when_profile_not_found()
    {
        $response = $this->controller->disconnect('twitch');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('dashboard', $response->getTargetUrl());
    }

    /** @test */
    public function it_handles_disconnect_exception()
    {
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $this->user->id,
            'platform' => 'twitch'
        ]);

        $this->mockService
            ->shouldReceive('revokeConnection')
            ->andThrow(new Exception('Disconnect failed'));

        $response = $this->controller->disconnect('twitch');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('dashboard', $response->getTargetUrl());
    }
}