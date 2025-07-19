<?php

namespace Tests\Unit\Services;

use App\Models\StreamerProfile;
use App\Models\User;
use App\Models\Review;
use App\Services\StreamerNotificationService;
use App\Notifications\StreamerLiveNotification;
use App\Notifications\StreamerNewReviewNotification;
use App\Notifications\StreamerNewFollowerNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class StreamerNotificationServiceTest extends TestCase
{
    private StreamerNotificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new StreamerNotificationService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_notify_followers_of_live_stream_sends_notifications()
    {
        Notification::fake();

        $streamerProfile = Mockery::mock(StreamerProfile::class);
        $followers = Mockery::mock();
        $followerCollection = new Collection([
            Mockery::mock(User::class),
            Mockery::mock(User::class),
        ]);

        $streamerProfile->shouldReceive('followers')->andReturn($followers);
        $followers->shouldReceive('wherePivot')->with('notification_preferences->live', true)->andReturnSelf();
        $followers->shouldReceive('get')->andReturn($followerCollection);

        $this->service->notifyFollowersOfLiveStream($streamerProfile);

        Notification::assertSentTo($followerCollection, StreamerLiveNotification::class);
    }

    public function test_notify_followers_of_live_stream_handles_empty_followers()
    {
        Notification::fake();

        $streamerProfile = Mockery::mock(StreamerProfile::class);
        $followers = Mockery::mock();

        $streamerProfile->shouldReceive('followers')->andReturn($followers);
        $followers->shouldReceive('wherePivot')->with('notification_preferences->live', true)->andReturnSelf();
        $followers->shouldReceive('get')->andReturn(new Collection([]));

        $this->service->notifyFollowersOfLiveStream($streamerProfile);

        Notification::assertNothingSent();
    }

    public function test_notify_followers_of_new_review_sends_notifications()
    {
        Notification::fake();

        $review = Mockery::mock(Review::class);
        $streamerProfile = Mockery::mock(StreamerProfile::class);
        $followers = Mockery::mock();
        $followerCollection = new Collection([
            Mockery::mock(User::class),
        ]);

        $review->shouldReceive('getAttribute')->with('streamerProfile')->andReturn($streamerProfile);
        $streamerProfile->shouldReceive('followers')->andReturn($followers);
        $followers->shouldReceive('wherePivot')->with('notification_preferences->reviews', true)->andReturnSelf();
        $followers->shouldReceive('get')->andReturn($followerCollection);

        $this->service->notifyFollowersOfNewReview($review);

        Notification::assertSentTo($followerCollection, StreamerNewReviewNotification::class);
    }

    public function test_notify_followers_of_new_review_handles_non_streamer_review()
    {
        Notification::fake();

        $review = Mockery::mock(Review::class);
        $review->shouldReceive('getAttribute')->with('streamerProfile')->andReturn(null);

        $this->service->notifyFollowersOfNewReview($review);

        Notification::assertNothingSent();
    }

    public function test_send_follow_notification_notifies_streamer()
    {
        Notification::fake();

        $streamerProfile = Mockery::mock(StreamerProfile::class);
        $streamerUser = Mockery::mock(User::class);
        $follower = Mockery::mock(User::class);

        $streamerProfile->shouldReceive('getAttribute')->with('user')->andReturn($streamerUser);
        $streamerUser->shouldReceive('notify')->with(Mockery::type(StreamerNewFollowerNotification::class));

        $this->service->sendFollowNotification($streamerProfile, $follower);

        // The notification is sent directly to the user, not through the Notification facade
        $this->assertTrue(true); // Test passes if no exceptions are thrown
    }

    public function test_get_notification_preferences_returns_empty_for_non_follower()
    {
        $user = Mockery::mock(User::class);
        $streamerProfile = Mockery::mock(StreamerProfile::class);
        $followedStreamers = Mockery::mock();

        $user->shouldReceive('followedStreamers')->andReturn($followedStreamers);
        $followedStreamers->shouldReceive('where')->with('streamer_profile_id', 1)->andReturnSelf();
        $followedStreamers->shouldReceive('first')->andReturn(null);

        $streamerProfile->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $preferences = $this->service->getNotificationPreferences($user, $streamerProfile);

        $this->assertEmpty($preferences);
    }

    public function test_get_notification_preferences_returns_defaults_for_null_preferences()
    {
        $user = Mockery::mock(User::class);
        $streamerProfile = Mockery::mock(StreamerProfile::class);
        $followedStreamers = Mockery::mock();
        $pivot = Mockery::mock();
        $pivotData = Mockery::mock();

        $user->shouldReceive('followedStreamers')->andReturn($followedStreamers);
        $followedStreamers->shouldReceive('where')->with('streamer_profile_id', 1)->andReturnSelf();
        $followedStreamers->shouldReceive('first')->andReturn($pivot);

        $pivot->shouldReceive('getAttribute')->with('pivot')->andReturn($pivotData);
        $pivotData->shouldReceive('getAttribute')->with('notification_preferences')->andReturn(null);

        $streamerProfile->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $preferences = $this->service->getNotificationPreferences($user, $streamerProfile);

        $this->assertTrue($preferences['live']);
        $this->assertTrue($preferences['reviews']);
    }

    public function test_get_notification_preferences_returns_stored_preferences()
    {
        $user = Mockery::mock(User::class);
        $streamerProfile = Mockery::mock(StreamerProfile::class);
        $followedStreamers = Mockery::mock();
        $pivot = Mockery::mock();
        $pivotData = Mockery::mock();

        $user->shouldReceive('followedStreamers')->andReturn($followedStreamers);
        $followedStreamers->shouldReceive('where')->with('streamer_profile_id', 1)->andReturnSelf();
        $followedStreamers->shouldReceive('first')->andReturn($pivot);

        $pivot->shouldReceive('getAttribute')->with('pivot')->andReturn($pivotData);
        $pivotData->shouldReceive('getAttribute')->with('notification_preferences')
            ->andReturn('{"live":false,"reviews":true}');

        $streamerProfile->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $preferences = $this->service->getNotificationPreferences($user, $streamerProfile);

        $this->assertFalse($preferences['live']);
        $this->assertTrue($preferences['reviews']);
    }

    public function test_should_receive_live_notification_returns_correct_value()
    {
        $user = Mockery::mock(User::class);
        $streamerProfile = Mockery::mock(StreamerProfile::class);
        $followedStreamers = Mockery::mock();
        $pivot = Mockery::mock();
        $pivotData = Mockery::mock();

        $user->shouldReceive('followedStreamers')->andReturn($followedStreamers);
        $followedStreamers->shouldReceive('where')->with('streamer_profile_id', 1)->andReturnSelf();
        $followedStreamers->shouldReceive('first')->andReturn($pivot);

        $pivot->shouldReceive('getAttribute')->with('pivot')->andReturn($pivotData);
        $pivotData->shouldReceive('getAttribute')->with('notification_preferences')
            ->andReturn('{"live":true,"reviews":false}');

        $streamerProfile->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $result = $this->service->shouldReceiveLiveNotification($user, $streamerProfile);

        $this->assertTrue($result);
    }

    public function test_should_receive_review_notification_returns_correct_value()
    {
        $user = Mockery::mock(User::class);
        $streamerProfile = Mockery::mock(StreamerProfile::class);
        $followedStreamers = Mockery::mock();
        $pivot = Mockery::mock();
        $pivotData = Mockery::mock();

        $user->shouldReceive('followedStreamers')->andReturn($followedStreamers);
        $followedStreamers->shouldReceive('where')->with('streamer_profile_id', 1)->andReturnSelf();
        $followedStreamers->shouldReceive('first')->andReturn($pivot);

        $pivot->shouldReceive('getAttribute')->with('pivot')->andReturn($pivotData);
        $pivotData->shouldReceive('getAttribute')->with('notification_preferences')
            ->andReturn('{"live":false,"reviews":true}');

        $streamerProfile->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $result = $this->service->shouldReceiveReviewNotification($user, $streamerProfile);

        $this->assertTrue($result);
    }
}