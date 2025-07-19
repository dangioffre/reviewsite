<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\StreamerProfile;
use App\Models\User;
use App\Services\LiveStatusMonitoringService;
use App\Services\LiveStatusService;
use App\Services\StreamerNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;

class LiveStatusMonitoringServiceTest extends TestCase
{
    use RefreshDatabase;

    private LiveStatusMonitoringService $service;
    private $mockLiveStatusService;
    private $mockNotificationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockLiveStatusService = Mockery::mock(LiveStatusService::class);
        $this->mockNotificationService = Mockery::mock(StreamerNotificationService::class);
        
        $this->service = new LiveStatusMonitoringService(
            $this->mockLiveStatusService,
            $this->mockNotificationService
        );
    }

    public function test_update_live_status_with_notifications_triggers_notifications_when_going_live()
    {
        // Arrange
        $user = User::factory()->create();
        $follower = User::factory()->create();
        
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_live' => false,
            'is_approved' => true
        ]);

        // Add follower with live notifications enabled
        $streamerProfile->followers()->attach($follower->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => true])
        ]);

        $this->mockLiveStatusService
            ->shouldReceive('clearCache')
            ->once()
            ->with($streamerProfile);

        $this->mockNotificationService
            ->shouldReceive('notifyFollowersOfLiveStream')
            ->once()
            ->with($streamerProfile);

        Log::shouldReceive('info')->twice();

        // Act
        $this->service->updateLiveStatusWithNotifications($streamerProfile, true);

        // Assert
        $streamerProfile->refresh();
        $this->assertTrue($streamerProfile->is_live);
        $this->assertNotNull($streamerProfile->live_status_checked_at);
    }

    public function test_update_live_status_with_notifications_does_not_trigger_notifications_when_staying_live()
    {
        // Arrange
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_live' => true,
            'is_approved' => true
        ]);

        $this->mockLiveStatusService
            ->shouldReceive('clearCache')
            ->once()
            ->with($streamerProfile);

        $this->mockNotificationService
            ->shouldNotReceive('notifyFollowersOfLiveStream');

        Log::shouldReceive('info')->once();

        // Act
        $this->service->updateLiveStatusWithNotifications($streamerProfile, true);

        // Assert
        $streamerProfile->refresh();
        $this->assertTrue($streamerProfile->is_live);
    }

    public function test_update_live_status_with_notifications_does_not_trigger_notifications_when_going_offline()
    {
        // Arrange
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_live' => true,
            'is_approved' => true
        ]);

        $this->mockLiveStatusService
            ->shouldReceive('clearCache')
            ->once()
            ->with($streamerProfile);

        $this->mockNotificationService
            ->shouldNotReceive('notifyFollowersOfLiveStream');

        Log::shouldReceive('info')->once();

        // Act
        $this->service->updateLiveStatusWithNotifications($streamerProfile, false);

        // Assert
        $streamerProfile->refresh();
        $this->assertFalse($streamerProfile->is_live);
    }

    public function test_set_manual_live_override_with_notifications_triggers_notifications_when_going_live()
    {
        // Arrange
        $user = User::factory()->create();
        $follower = User::factory()->create();
        
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_live' => false,
            'manual_live_override' => null,
            'is_approved' => true
        ]);

        // Add follower with live notifications enabled
        $streamerProfile->followers()->attach($follower->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => true])
        ]);

        $this->mockLiveStatusService
            ->shouldReceive('clearCache')
            ->once()
            ->with($streamerProfile);

        $this->mockNotificationService
            ->shouldReceive('notifyFollowersOfLiveStream')
            ->once()
            ->with($streamerProfile);

        Log::shouldReceive('info')->twice();

        // Act
        $this->service->setManualLiveOverrideWithNotifications($streamerProfile, true);

        // Assert
        $streamerProfile->refresh();
        $this->assertTrue($streamerProfile->manual_live_override);
        $this->assertTrue($streamerProfile->isLive());
    }

    public function test_set_manual_live_override_with_notifications_does_not_trigger_when_going_offline()
    {
        // Arrange
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_live' => true,
            'manual_live_override' => null,
            'is_approved' => true
        ]);

        $this->mockLiveStatusService
            ->shouldReceive('clearCache')
            ->once()
            ->with($streamerProfile);

        $this->mockNotificationService
            ->shouldNotReceive('notifyFollowersOfLiveStream');

        Log::shouldReceive('info')->once();

        // Act
        $this->service->setManualLiveOverrideWithNotifications($streamerProfile, false);

        // Assert
        $streamerProfile->refresh();
        $this->assertFalse($streamerProfile->manual_live_override);
        $this->assertFalse($streamerProfile->isLive());
    }

    public function test_trigger_live_notifications_calls_notification_service()
    {
        // Arrange
        $user = User::factory()->create();
        $follower = User::factory()->create();
        
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_approved' => true
        ]);

        // Add follower with live notifications enabled
        $streamerProfile->followers()->attach($follower->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => true])
        ]);

        $this->mockNotificationService
            ->shouldReceive('notifyFollowersOfLiveStream')
            ->once()
            ->with($streamerProfile);

        Log::shouldReceive('info')->once();

        // Act
        $this->service->triggerLiveNotifications($streamerProfile);

        // Assert
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_trigger_live_notifications_handles_exceptions_gracefully()
    {
        // Arrange
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_approved' => true
        ]);

        $this->mockNotificationService
            ->shouldReceive('notifyFollowersOfLiveStream')
            ->once()
            ->with($streamerProfile)
            ->andThrow(new \Exception('Notification failed'));

        Log::shouldReceive('error')->once();

        // Act & Assert - Should not throw exception
        $this->service->triggerLiveNotifications($streamerProfile);
        
        // Assert
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_should_trigger_live_notifications_returns_false_for_unapproved_streamer()
    {
        // Arrange
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_approved' => false
        ]);

        // Act
        $result = $this->service->shouldTriggerLiveNotifications($streamerProfile);

        // Assert
        $this->assertFalse($result);
    }

    public function test_should_trigger_live_notifications_returns_true_for_approved_streamer_with_followers()
    {
        // Arrange
        $user = User::factory()->create();
        $follower = User::factory()->create();
        
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_approved' => true
        ]);

        // Add follower with live notifications enabled
        $streamerProfile->followers()->attach($follower->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => true])
        ]);

        // Act
        $result = $this->service->shouldTriggerLiveNotifications($streamerProfile);

        // Assert
        $this->assertTrue($result);
    }

    public function test_should_trigger_live_notifications_returns_false_for_streamer_with_no_live_notification_followers()
    {
        // Arrange
        $user = User::factory()->create();
        $follower = User::factory()->create();
        
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_approved' => true
        ]);

        // Add follower with live notifications disabled
        $streamerProfile->followers()->attach($follower->id, [
            'notification_preferences' => json_encode(['live' => false, 'reviews' => true])
        ]);

        // Act
        $result = $this->service->shouldTriggerLiveNotifications($streamerProfile);

        // Assert
        $this->assertFalse($result);
    }

    public function test_bulk_update_live_status_with_notifications_processes_multiple_streamers()
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $streamer1 = StreamerProfile::factory()->create([
            'user_id' => $user1->id,
            'is_live' => false,
            'is_approved' => true
        ]);
        
        $streamer2 = StreamerProfile::factory()->create([
            'user_id' => $user2->id,
            'is_live' => false,
            'is_approved' => true
        ]);

        $updates = [
            ['streamer' => $streamer1, 'is_live' => true],
            ['streamer' => $streamer2, 'is_live' => true]
        ];

        $this->mockLiveStatusService
            ->shouldReceive('clearCache')
            ->twice();

        $this->mockNotificationService
            ->shouldReceive('notifyFollowersOfLiveStream')
            ->twice();

        Log::shouldReceive('info')->times(4);

        // Act
        $this->service->bulkUpdateLiveStatusWithNotifications($updates);

        // Assert
        $streamer1->refresh();
        $streamer2->refresh();
        $this->assertTrue($streamer1->is_live);
        $this->assertTrue($streamer2->is_live);
    }

    public function test_get_live_status_change_stats_returns_correct_statistics()
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();
        
        // Create approved streamers
        StreamerProfile::factory()->create([
            'user_id' => $user1->id,
            'is_live' => true,
            'is_approved' => true,
            'manual_live_override' => null
        ]);
        
        StreamerProfile::factory()->create([
            'user_id' => $user2->id,
            'is_live' => false,
            'is_approved' => true,
            'manual_live_override' => true
        ]);
        
        StreamerProfile::factory()->create([
            'user_id' => $user3->id,
            'is_live' => false,
            'is_approved' => true,
            'manual_live_override' => null
        ]);

        // Act
        $stats = $this->service->getLiveStatusChangeStats();

        // Assert
        $this->assertEquals(3, $stats['total_approved_streamers']);
        $this->assertEquals(2, $stats['currently_live']); // One is_live=true, one manual_override=true
        $this->assertEquals(1, $stats['manual_overrides_active']);
        $this->assertEquals(66.67, $stats['live_percentage']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}