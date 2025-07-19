<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\StreamerFollowController;
use App\Models\User;
use App\Models\StreamerProfile;
use App\Services\StreamerNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class StreamerFollowControllerTest extends TestCase
{
    private StreamerFollowController $controller;
    private StreamerNotificationService $mockNotificationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockNotificationService = Mockery::mock(StreamerNotificationService::class);
        $this->controller = new StreamerFollowController($this->mockNotificationService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_constructor_initializes_service()
    {
        $this->assertInstanceOf(StreamerFollowController::class, $this->controller);
    }

    public function test_follow_method_calls_notification_service()
    {
        $user = Mockery::mock(User::class);
        $streamerProfile = Mockery::mock(StreamerProfile::class);
        $followedStreamers = Mockery::mock();

        $user->shouldReceive('followedStreamers')->andReturn($followedStreamers);
        $followedStreamers->shouldReceive('where')->with('streamer_profile_id', 1)->andReturnSelf();
        $followedStreamers->shouldReceive('exists')->andReturn(false);
        $followedStreamers->shouldReceive('attach')->with(1, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => true])
        ]);

        $streamerProfile->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $streamerProfile->shouldReceive('getDisplayName')->andReturn('TestStreamer');

        Auth::shouldReceive('user')->andReturn($user);

        $this->mockNotificationService
            ->shouldReceive('sendFollowNotification')
            ->once()
            ->with($streamerProfile, $user);

        $response = $this->controller->follow($streamerProfile);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function test_unfollow_method_detaches_relationship()
    {
        $user = Mockery::mock(User::class);
        $streamerProfile = Mockery::mock(StreamerProfile::class);
        $followedStreamers = Mockery::mock();

        $user->shouldReceive('followedStreamers')->andReturn($followedStreamers);
        $followedStreamers->shouldReceive('where')->with('streamer_profile_id', 1)->andReturnSelf();
        $followedStreamers->shouldReceive('exists')->andReturn(true);
        $followedStreamers->shouldReceive('detach')->with(1);

        $streamerProfile->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $streamerProfile->shouldReceive('getDisplayName')->andReturn('TestStreamer');

        Auth::shouldReceive('user')->andReturn($user);

        $response = $this->controller->unfollow($streamerProfile);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function test_update_notification_preferences_validates_input()
    {
        $request = Request::create('/test', 'PATCH', [
            'live' => true,
            'reviews' => false,
        ]);

        $user = Mockery::mock(User::class);
        $streamerProfile = Mockery::mock(StreamerProfile::class);
        $followedStreamers = Mockery::mock();

        $user->shouldReceive('followedStreamers')->andReturn($followedStreamers);
        $followedStreamers->shouldReceive('where')->with('streamer_profile_id', 1)->andReturnSelf();
        $followedStreamers->shouldReceive('exists')->andReturn(true);
        $followedStreamers->shouldReceive('updateExistingPivot')->with(1, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => false])
        ]);

        $streamerProfile->shouldReceive('getAttribute')->with('id')->andReturn(1);

        Auth::shouldReceive('user')->andReturn($user);

        $response = $this->controller->updateNotificationPreferences($request, $streamerProfile);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function test_followers_method_returns_view_with_data()
    {
        $user = Mockery::mock(User::class);
        $followedStreamers = Mockery::mock();
        $query = Mockery::mock();

        $user->shouldReceive('followedStreamers')->andReturn($followedStreamers);
        $followedStreamers->shouldReceive('approved')->andReturn($query);
        $query->shouldReceive('with')->with(Mockery::any())->andReturnSelf();
        $query->shouldReceive('get')->andReturn(collect([]));

        Auth::shouldReceive('user')->andReturn($user);

        $response = $this->controller->followers();

        $this->assertEquals('streamer.followers.index', $response->name());
        $this->assertArrayHasKey('followedStreamers', $response->getData());
    }
}