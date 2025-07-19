<?php

namespace Tests\Unit;

use App\Jobs\CheckLiveStatusJob;
use App\Models\StreamerProfile;
use App\Models\User;
use App\Services\PlatformApiService;
use App\Services\StreamerNotificationService;
use App\Notifications\StreamerLiveNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Mockery;

class CheckLiveStatusJobTest extends TestCase
{
    use RefreshDatabase;

    private StreamerProfile $streamerProfile;
    private User $follower;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->follower = User::factory()->create();
        
        $this->streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_live' => false,
            'is_approved' => true,
            'channel_name' => 'TestStreamer',
            'platform' => 'twitch',
            'manual_live_override' => null,
        ]);

        // Add a follower with live notifications enabled
        $this->streamerProfile->followers()->attach($this->follower->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => true])
        ]);
    }

    public function test_it_updates_live_status_and_sends_notifications_when_streamer_goes_live()
    {
        Notification::fake();
        
        $mockPlatformService = Mockery::mock(PlatformApiService::class);
        $mockPlatformService->shouldReceive('checkLiveStatus')
            ->once()
            ->with($this->streamerProfile)
            ->andReturn(true);

        $mockNotificationService = Mockery::mock(StreamerNotificationService::class);
        $mockNotificationService->shouldReceive('notifyFollowersOfLiveStream')
            ->once()
            ->with($this->streamerProfile);

        Log::shouldReceive('info')
            ->once()
            ->with('Streamer went live, notifications sent', [
                'profile_id' => $this->streamerProfile->id,
                'channel_name' => 'TestStreamer',
                'platform' => 'twitch'
            ]);

        Log::shouldReceive('info')
            ->once()
            ->with('Live status changed', [
                'profile_id' => $this->streamerProfile->id,
                'channel_name' => 'TestStreamer',
                'platform' => 'twitch',
                'was_live' => false,
                'is_live' => true
            ]);

        $job = new CheckLiveStatusJob($this->streamerProfile);
        $job->handle($mockPlatformService, $mockNotificationService);

        // Verify live status was updated
        $this->streamerProfile->refresh();
        $this->assertTrue($this->streamerProfile->is_live);
        $this->assertNotNull($this->streamerProfile->live_status_checked_at);
    }

    /** @test */
    public function it_updates_live_status_without_notifications_when_streamer_goes_offline()
    {
        // Set streamer as initially live
        $this->streamerProfile->update(['is_live' => true]);
        
        Notification::fake();
        
        $mockPlatformService = Mockery::mock(PlatformApiService::class);
        $mockPlatformService->shouldReceive('checkLiveStatus')
            ->once()
            ->with($this->streamerProfile)
            ->andReturn(false);

        $mockNotificationService = Mockery::mock(StreamerNotificationService::class);
        $mockNotificationService->shouldNotReceive('notifyFollowersOfLiveStream');

        $job = new CheckLiveStatusJob($this->streamerProfile);
        $job->handle($mockPlatformService, $mockNotificationService);

        // Verify live status was updated
        $this->streamerProfile->refresh();
        $this->assertFalse($this->streamerProfile->is_live);
        $this->assertNotNull($this->streamerProfile->live_status_checked_at);
    }

    /** @test */
    public function it_does_not_send_notifications_when_streamer_stays_live()
    {
        // Set streamer as initially live
        $this->streamerProfile->update(['is_live' => true]);
        
        Notification::fake();
        
        $mockPlatformService = Mockery::mock(PlatformApiService::class);
        $mockPlatformService->shouldReceive('checkLiveStatus')
            ->once()
            ->with($this->streamerProfile)
            ->andReturn(true);

        $mockNotificationService = Mockery::mock(StreamerNotificationService::class);
        $mockNotificationService->shouldNotReceive('notifyFollowersOfLiveStream');

        $job = new CheckLiveStatusJob($this->streamerProfile);
        $job->handle($mockPlatformService, $mockNotificationService);

        // Verify live status remains true
        $this->streamerProfile->refresh();
        $this->assertTrue($this->streamerProfile->is_live);
    }

    /** @test */
    public function it_does_not_send_notifications_when_streamer_stays_offline()
    {
        Notification::fake();
        
        $mockPlatformService = Mockery::mock(PlatformApiService::class);
        $mockPlatformService->shouldReceive('checkLiveStatus')
            ->once()
            ->with($this->streamerProfile)
            ->andReturn(false);

        $mockNotificationService = Mockery::mock(StreamerNotificationService::class);
        $mockNotificationService->shouldNotReceive('notifyFollowersOfLiveStream');

        $job = new CheckLiveStatusJob($this->streamerProfile);
        $job->handle($mockPlatformService, $mockNotificationService);

        // Verify live status remains false
        $this->streamerProfile->refresh();
        $this->assertFalse($this->streamerProfile->is_live);
    }

    /** @test */
    public function it_skips_live_status_check_when_manual_override_is_set()
    {
        $this->streamerProfile->update(['manual_live_override' => true]);
        
        $mockPlatformService = Mockery::mock(PlatformApiService::class);
        $mockPlatformService->shouldNotReceive('checkLiveStatus');

        $mockNotificationService = Mockery::mock(StreamerNotificationService::class);
        $mockNotificationService->shouldNotReceive('notifyFollowersOfLiveStream');

        Log::shouldReceive('info')
            ->once()
            ->with('Skipping live status check due to manual override', [
                'profile_id' => $this->streamerProfile->id,
                'manual_override' => true
            ]);

        $job = new CheckLiveStatusJob($this->streamerProfile);
        $job->handle($mockPlatformService, $mockNotificationService);

        // Verify live status was not changed
        $this->streamerProfile->refresh();
        $this->assertFalse($this->streamerProfile->is_live);
        $this->assertTrue($this->streamerProfile->manual_live_override);
    }

    /** @test */
    public function it_logs_status_changes()
    {
        $mockPlatformService = Mockery::mock(PlatformApiService::class);
        $mockPlatformService->shouldReceive('checkLiveStatus')
            ->once()
            ->with($this->streamerProfile)
            ->andReturn(true);

        $mockNotificationService = Mockery::mock(StreamerNotificationService::class);
        $mockNotificationService->shouldReceive('notifyFollowersOfLiveStream')
            ->once()
            ->with($this->streamerProfile);

        Log::shouldReceive('info')
            ->once()
            ->with('Streamer went live, notifications sent', Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('Live status changed', [
                'profile_id' => $this->streamerProfile->id,
                'channel_name' => 'TestStreamer',
                'platform' => 'twitch',
                'was_live' => false,
                'is_live' => true
            ]);

        $job = new CheckLiveStatusJob($this->streamerProfile);
        $job->handle($mockPlatformService, $mockNotificationService);
        
        // Verify the status was actually updated
        $this->streamerProfile->refresh();
        $this->assertTrue($this->streamerProfile->is_live);
    }

    /** @test */
    public function it_handles_platform_api_errors_gracefully()
    {
        $mockPlatformService = Mockery::mock(PlatformApiService::class);
        $mockPlatformService->shouldReceive('checkLiveStatus')
            ->once()
            ->with($this->streamerProfile)
            ->andThrow(new \Exception('API rate limit exceeded'));

        $mockNotificationService = Mockery::mock(StreamerNotificationService::class);
        $mockNotificationService->shouldNotReceive('notifyFollowersOfLiveStream');

        Log::shouldReceive('error')
            ->once()
            ->with('Failed to check live status', [
                'profile_id' => $this->streamerProfile->id,
                'channel_name' => 'TestStreamer',
                'platform' => 'twitch',
                'error' => 'API rate limit exceeded'
            ]);

        $job = new CheckLiveStatusJob($this->streamerProfile);
        
        // Should not throw exception - errors are handled gracefully
        $job->handle($mockPlatformService, $mockNotificationService);

        // Verify live status was not changed due to error
        $this->streamerProfile->refresh();
        $this->assertFalse($this->streamerProfile->is_live);
    }

    /** @test */
    public function it_handles_notification_service_errors_gracefully()
    {
        $mockPlatformService = Mockery::mock(PlatformApiService::class);
        $mockPlatformService->shouldReceive('checkLiveStatus')
            ->once()
            ->with($this->streamerProfile)
            ->andReturn(true);

        $mockNotificationService = Mockery::mock(StreamerNotificationService::class);
        $mockNotificationService->shouldReceive('notifyFollowersOfLiveStream')
            ->once()
            ->with($this->streamerProfile)
            ->andThrow(new \Exception('Notification service unavailable'));

        Log::shouldReceive('error')
            ->once()
            ->with('Failed to check live status', Mockery::any());

        $job = new CheckLiveStatusJob($this->streamerProfile);
        
        // Should not throw exception - errors are handled gracefully
        $job->handle($mockPlatformService, $mockNotificationService);

        // Verify live status was still updated despite notification error
        $this->streamerProfile->refresh();
        $this->assertTrue($this->streamerProfile->is_live);
    }

    public function test_it_has_correct_retry_configuration()
    {
        $job = new CheckLiveStatusJob($this->streamerProfile);
        
        $this->assertEquals(3, $job->tries);
        $this->assertEquals(60, $job->backoff);
    }

    public function test_it_works_with_twitch_platform()
    {
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'platform' => 'twitch',
            'is_live' => false,
            'is_approved' => true,
        ]);

        $mockPlatformService = Mockery::mock(PlatformApiService::class);
        $mockPlatformService->shouldReceive('checkLiveStatus')
            ->once()
            ->with($streamerProfile)
            ->andReturn(true);

        $mockNotificationService = Mockery::mock(StreamerNotificationService::class);
        $mockNotificationService->shouldReceive('notifyFollowersOfLiveStream')
            ->once()
            ->with($streamerProfile);

        Log::shouldReceive('info')->twice();

        $job = new CheckLiveStatusJob($streamerProfile);
        $job->handle($mockPlatformService, $mockNotificationService);

        $streamerProfile->refresh();
        $this->assertTrue($streamerProfile->is_live);
    }

    public function test_it_works_with_youtube_platform()
    {
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'platform' => 'youtube',
            'is_live' => false,
            'is_approved' => true,
        ]);

        $mockPlatformService = Mockery::mock(PlatformApiService::class);
        $mockPlatformService->shouldReceive('checkLiveStatus')
            ->once()
            ->with($streamerProfile)
            ->andReturn(true);

        $mockNotificationService = Mockery::mock(StreamerNotificationService::class);
        $mockNotificationService->shouldReceive('notifyFollowersOfLiveStream')
            ->once()
            ->with($streamerProfile);

        Log::shouldReceive('info')->twice();

        $job = new CheckLiveStatusJob($streamerProfile);
        $job->handle($mockPlatformService, $mockNotificationService);

        $streamerProfile->refresh();
        $this->assertTrue($streamerProfile->is_live);
    }

    public function test_it_works_with_kick_platform()
    {
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'platform' => 'kick',
            'is_live' => false,
            'is_approved' => true,
        ]);

        $mockPlatformService = Mockery::mock(PlatformApiService::class);
        $mockPlatformService->shouldReceive('checkLiveStatus')
            ->once()
            ->with($streamerProfile)
            ->andReturn(true);

        $mockNotificationService = Mockery::mock(StreamerNotificationService::class);
        $mockNotificationService->shouldReceive('notifyFollowersOfLiveStream')
            ->once()
            ->with($streamerProfile);

        Log::shouldReceive('info')->twice();

        $job = new CheckLiveStatusJob($streamerProfile);
        $job->handle($mockPlatformService, $mockNotificationService);

        $streamerProfile->refresh();
        $this->assertTrue($streamerProfile->is_live);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}