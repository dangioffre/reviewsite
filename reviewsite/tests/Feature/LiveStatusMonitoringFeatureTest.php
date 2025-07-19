<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\StreamerProfile;
use App\Services\LiveStatusMonitoringService;
use App\Notifications\StreamerLiveNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class LiveStatusMonitoringFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_live_status_change_triggers_notifications_to_followers()
    {
        // Arrange
        Notification::fake();

        $streamer = User::factory()->create();
        $follower1 = User::factory()->create();
        $follower2 = User::factory()->create();
        $follower3 = User::factory()->create();

        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $streamer->id,
            'is_live' => false,
            'is_approved' => true,
            'channel_name' => 'TestStreamer'
        ]);

        // Add followers with different notification preferences
        $streamerProfile->followers()->attach($follower1->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => true])
        ]);
        
        $streamerProfile->followers()->attach($follower2->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => false])
        ]);
        
        // Follower3 has live notifications disabled
        $streamerProfile->followers()->attach($follower3->id, [
            'notification_preferences' => json_encode(['live' => false, 'reviews' => true])
        ]);

        $service = app(LiveStatusMonitoringService::class);

        // Act
        $service->updateLiveStatusWithNotifications($streamerProfile, true);

        // Assert
        $streamerProfile->refresh();
        $this->assertTrue($streamerProfile->is_live);

        // Should send notifications to follower1 and follower2 (who have live notifications enabled)
        Notification::assertSentTo(
            [$follower1, $follower2],
            StreamerLiveNotification::class,
            function ($notification, $channels, $notifiable) use ($streamerProfile) {
                $data = $notification->toDatabase($notifiable);
                return $data['streamer_profile_id'] === $streamerProfile->id;
            }
        );

        // Should NOT send notification to follower3 (live notifications disabled)
        Notification::assertNotSentTo($follower3, StreamerLiveNotification::class);
    }

    public function test_manual_live_override_triggers_notifications()
    {
        // Arrange
        Notification::fake();

        $streamer = User::factory()->create();
        $follower = User::factory()->create();

        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $streamer->id,
            'is_live' => false,
            'manual_live_override' => null,
            'is_approved' => true
        ]);

        $streamerProfile->followers()->attach($follower->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => true])
        ]);

        $service = app(LiveStatusMonitoringService::class);

        // Act
        $service->setManualLiveOverrideWithNotifications($streamerProfile, true);

        // Assert
        $streamerProfile->refresh();
        $this->assertTrue($streamerProfile->manual_live_override);
        $this->assertTrue($streamerProfile->isLive());

        Notification::assertSentTo($follower, StreamerLiveNotification::class);
    }

    public function test_no_notifications_sent_when_streamer_stays_live()
    {
        // Arrange
        Notification::fake();

        $streamer = User::factory()->create();
        $follower = User::factory()->create();

        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $streamer->id,
            'is_live' => true,
            'is_approved' => true
        ]);

        $streamerProfile->followers()->attach($follower->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => true])
        ]);

        $service = app(LiveStatusMonitoringService::class);

        // Act
        $service->updateLiveStatusWithNotifications($streamerProfile, true);

        // Assert
        Notification::assertNothingSent();
    }

    public function test_no_notifications_sent_when_streamer_goes_offline()
    {
        // Arrange
        Notification::fake();

        $streamer = User::factory()->create();
        $follower = User::factory()->create();

        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $streamer->id,
            'is_live' => true,
            'is_approved' => true
        ]);

        $streamerProfile->followers()->attach($follower->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => true])
        ]);

        $service = app(LiveStatusMonitoringService::class);

        // Act
        $service->updateLiveStatusWithNotifications($streamerProfile, false);

        // Assert
        $streamerProfile->refresh();
        $this->assertFalse($streamerProfile->is_live);
        
        Notification::assertNothingSent();
    }

    public function test_no_notifications_sent_for_unapproved_streamers()
    {
        // Arrange
        Notification::fake();

        $streamer = User::factory()->create();
        $follower = User::factory()->create();

        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $streamer->id,
            'is_live' => false,
            'is_approved' => false // Not approved
        ]);

        $streamerProfile->followers()->attach($follower->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => true])
        ]);

        $service = app(LiveStatusMonitoringService::class);

        // Act
        $service->updateLiveStatusWithNotifications($streamerProfile, true);

        // Assert
        $streamerProfile->refresh();
        $this->assertTrue($streamerProfile->is_live);
        
        // No notifications should be sent for unapproved streamers
        Notification::assertNothingSent();
    }

    public function test_bulk_live_status_update_with_notifications()
    {
        // Arrange
        Notification::fake();

        $streamer1 = User::factory()->create();
        $streamer2 = User::factory()->create();
        $follower = User::factory()->create();

        $streamerProfile1 = StreamerProfile::factory()->create([
            'user_id' => $streamer1->id,
            'is_live' => false,
            'is_approved' => true
        ]);

        $streamerProfile2 = StreamerProfile::factory()->create([
            'user_id' => $streamer2->id,
            'is_live' => false,
            'is_approved' => true
        ]);

        // Follower follows both streamers
        $streamerProfile1->followers()->attach($follower->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => true])
        ]);
        
        $streamerProfile2->followers()->attach($follower->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => true])
        ]);

        $service = app(LiveStatusMonitoringService::class);

        $updates = [
            ['streamer' => $streamerProfile1, 'is_live' => true],
            ['streamer' => $streamerProfile2, 'is_live' => true]
        ];

        // Act
        $service->bulkUpdateLiveStatusWithNotifications($updates);

        // Assert
        $streamerProfile1->refresh();
        $streamerProfile2->refresh();
        
        $this->assertTrue($streamerProfile1->is_live);
        $this->assertTrue($streamerProfile2->is_live);

        // Should receive 2 notifications (one for each streamer)
        Notification::assertSentToTimes($follower, StreamerLiveNotification::class, 2);
    }

    public function test_should_trigger_live_notifications_logic()
    {
        // Arrange
        $streamer = User::factory()->create();
        $follower1 = User::factory()->create();
        $follower2 = User::factory()->create();

        $approvedStreamerProfile = StreamerProfile::factory()->create([
            'user_id' => $streamer->id,
            'is_approved' => true
        ]);

        $unapprovedStreamerProfile = StreamerProfile::factory()->create([
            'user_id' => User::factory()->create()->id,
            'is_approved' => false
        ]);

        // Add followers to approved streamer
        $approvedStreamerProfile->followers()->attach($follower1->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => true])
        ]);
        
        $approvedStreamerProfile->followers()->attach($follower2->id, [
            'notification_preferences' => json_encode(['live' => false, 'reviews' => true])
        ]);

        $service = app(LiveStatusMonitoringService::class);

        // Act & Assert
        $this->assertTrue($service->shouldTriggerLiveNotifications($approvedStreamerProfile));
        $this->assertFalse($service->shouldTriggerLiveNotifications($unapprovedStreamerProfile));
    }

    public function test_live_status_change_stats()
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();
        $user4 = User::factory()->create();

        // Create various streamer profiles
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

        // Unapproved streamer (should not be counted)
        StreamerProfile::factory()->create([
            'user_id' => $user4->id,
            'is_live' => true,
            'is_approved' => false,
            'manual_live_override' => null
        ]);

        $service = app(LiveStatusMonitoringService::class);

        // Act
        $stats = $service->getLiveStatusChangeStats();

        // Assert
        $this->assertEquals(3, $stats['total_approved_streamers']);
        $this->assertEquals(2, $stats['currently_live']); // One is_live=true, one manual_override=true
        $this->assertEquals(1, $stats['manual_overrides_active']);
        $this->assertEquals(66.67, $stats['live_percentage']);
    }
}