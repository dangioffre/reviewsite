<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\StreamerProfile;
use App\Models\Review;
use App\Models\Product;
use App\Services\StreamerNotificationService;
use App\Notifications\StreamerLiveNotification;
use App\Notifications\StreamerNewReviewNotification;
use App\Notifications\StreamerNewFollowerNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StreamerFollowerSystemTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $streamerUser;
    private StreamerProfile $streamerProfile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->streamerUser = User::factory()->create();
        $this->streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $this->streamerUser->id,
            'is_approved' => true,
            'is_verified' => true,
        ]);
    }

    public function test_user_can_follow_streamer()
    {
        Notification::fake();

        $response = $this->actingAs($this->user)
            ->post(route('streamer.follow', $this->streamerProfile));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertTrue($this->user->followedStreamers()->where('streamer_profile_id', $this->streamerProfile->id)->exists());

        // Check that notification was sent to streamer
        Notification::assertSentTo(
            $this->streamerUser,
            StreamerNewFollowerNotification::class,
            function ($notification) {
                return $notification->follower->id === $this->user->id &&
                       $notification->streamerProfile->id === $this->streamerProfile->id;
            }
        );
    }

    public function test_user_cannot_follow_same_streamer_twice()
    {
        $this->user->followedStreamers()->attach($this->streamerProfile->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => true])
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('streamer.follow', $this->streamerProfile));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertEquals(1, $this->user->followedStreamers()->count());
    }

    public function test_user_can_unfollow_streamer()
    {
        $this->user->followedStreamers()->attach($this->streamerProfile->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => true])
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('streamer.unfollow', $this->streamerProfile));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertFalse($this->user->followedStreamers()->where('streamer_profile_id', $this->streamerProfile->id)->exists());
    }

    public function test_user_cannot_unfollow_streamer_they_dont_follow()
    {
        $response = $this->actingAs($this->user)
            ->delete(route('streamer.unfollow', $this->streamerProfile));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_user_can_view_followed_streamers()
    {
        $this->user->followedStreamers()->attach($this->streamerProfile->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => true])
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('streamer.followers.index'));

        $response->assertOk();
        $response->assertViewIs('streamer.followers.index');
        $response->assertViewHas('followedStreamers');
        $response->assertSee($this->streamerProfile->getDisplayName());
    }

    public function test_user_can_update_notification_preferences()
    {
        $this->user->followedStreamers()->attach($this->streamerProfile->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => true])
        ]);

        $response = $this->actingAs($this->user)
            ->patch(route('streamer.notification-preferences', $this->streamerProfile), [
                'live' => false,
                'reviews' => true,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $pivot = $this->user->followedStreamers()
            ->where('streamer_profile_id', $this->streamerProfile->id)
            ->first()->pivot;

        $preferences = json_decode($pivot->notification_preferences, true);
        $this->assertFalse($preferences['live']);
        $this->assertTrue($preferences['reviews']);
    }

    public function test_followers_receive_live_notifications()
    {
        Notification::fake();

        // Create multiple followers with different preferences
        $follower1 = User::factory()->create();
        $follower2 = User::factory()->create();
        $follower3 = User::factory()->create();

        $follower1->followedStreamers()->attach($this->streamerProfile->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => true])
        ]);

        $follower2->followedStreamers()->attach($this->streamerProfile->id, [
            'notification_preferences' => json_encode(['live' => false, 'reviews' => true])
        ]);

        $follower3->followedStreamers()->attach($this->streamerProfile->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => false])
        ]);

        $notificationService = app(StreamerNotificationService::class);
        $notificationService->notifyFollowersOfLiveStream($this->streamerProfile);

        // Only followers with live notifications enabled should receive notification
        Notification::assertSentTo($follower1, StreamerLiveNotification::class);
        Notification::assertNotSentTo($follower2, StreamerLiveNotification::class);
        Notification::assertSentTo($follower3, StreamerLiveNotification::class);
    }

    public function test_followers_receive_review_notifications()
    {
        Notification::fake();

        $game = Product::factory()->create(['type' => 'game']);
        $review = Review::factory()->create([
            'user_id' => $this->streamerUser->id,
            'product_id' => $game->id,
            'streamer_profile_id' => $this->streamerProfile->id,
        ]);

        // Create followers with different preferences
        $follower1 = User::factory()->create();
        $follower2 = User::factory()->create();

        $follower1->followedStreamers()->attach($this->streamerProfile->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => true])
        ]);

        $follower2->followedStreamers()->attach($this->streamerProfile->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => false])
        ]);

        $notificationService = app(StreamerNotificationService::class);
        $notificationService->notifyFollowersOfNewReview($review);

        // Only followers with review notifications enabled should receive notification
        Notification::assertSentTo($follower1, StreamerNewReviewNotification::class);
        Notification::assertNotSentTo($follower2, StreamerNewReviewNotification::class);
    }

    public function test_notification_service_handles_non_streamer_reviews()
    {
        Notification::fake();

        $game = Product::factory()->create(['type' => 'game']);
        $review = Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $game->id,
            'streamer_profile_id' => null, // Regular user review
        ]);

        $notificationService = app(StreamerNotificationService::class);
        $notificationService->notifyFollowersOfNewReview($review);

        // No notifications should be sent for non-streamer reviews
        Notification::assertNothingSent();
    }

    public function test_notification_preferences_default_values()
    {
        $notificationService = app(StreamerNotificationService::class);

        // Test with no existing relationship
        $preferences = $notificationService->getNotificationPreferences($this->user, $this->streamerProfile);
        $this->assertEmpty($preferences);

        // Test with relationship but no preferences
        $this->user->followedStreamers()->attach($this->streamerProfile->id, [
            'notification_preferences' => null
        ]);

        $preferences = $notificationService->getNotificationPreferences($this->user, $this->streamerProfile);
        $this->assertTrue($preferences['live']);
        $this->assertTrue($preferences['reviews']);
    }

    public function test_should_receive_notification_methods()
    {
        $notificationService = app(StreamerNotificationService::class);

        $this->user->followedStreamers()->attach($this->streamerProfile->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => false])
        ]);

        $this->assertTrue($notificationService->shouldReceiveLiveNotification($this->user, $this->streamerProfile));
        $this->assertFalse($notificationService->shouldReceiveReviewNotification($this->user, $this->streamerProfile));
    }

    public function test_guest_cannot_follow_streamers()
    {
        $response = $this->post(route('streamer.follow', $this->streamerProfile));
        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_view_followed_streamers()
    {
        $response = $this->get(route('streamer.followers.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_followers_page_shows_empty_state()
    {
        $response = $this->actingAs($this->user)
            ->get(route('streamer.followers.index'));

        $response->assertOk();
        $response->assertSee('No followed streamers');
        $response->assertSee('Discover Streamers');
    }

    public function test_followers_page_shows_streamer_information()
    {
        $this->user->followedStreamers()->attach($this->streamerProfile->id, [
            'notification_preferences' => json_encode(['live' => true, 'reviews' => false])
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('streamer.followers.index'));

        $response->assertOk();
        $response->assertSee($this->streamerProfile->getDisplayName());
        $response->assertSee($this->streamerProfile->platform);
        $response->assertSee('View Profile');
        $response->assertSee('Watch Live');
        $response->assertSee('Unfollow');
    }
}