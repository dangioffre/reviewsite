<?php

namespace Tests\Unit\Models;

use App\Models\StreamerProfile;
use App\Models\StreamerSchedule;
use App\Models\StreamerVod;
use App\Models\StreamerSocialLink;
use App\Models\User;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StreamerProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_streamer_profile_belongs_to_user()
    {
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $streamerProfile->user);
        $this->assertEquals($user->id, $streamerProfile->user->id);
    }

    public function test_streamer_profile_has_many_schedules()
    {
        $streamerProfile = StreamerProfile::factory()->create();
        $schedules = StreamerSchedule::factory()->count(3)->create([
            'streamer_profile_id' => $streamerProfile->id
        ]);

        $this->assertCount(3, $streamerProfile->schedules);
        $this->assertInstanceOf(StreamerSchedule::class, $streamerProfile->schedules->first());
    }

    public function test_streamer_profile_has_many_vods()
    {
        $streamerProfile = StreamerProfile::factory()->create();
        $vods = StreamerVod::factory()->count(5)->create([
            'streamer_profile_id' => $streamerProfile->id
        ]);

        $this->assertCount(5, $streamerProfile->vods);
        $this->assertInstanceOf(StreamerVod::class, $streamerProfile->vods->first());
    }

    public function test_streamer_profile_has_many_social_links()
    {
        $streamerProfile = StreamerProfile::factory()->create();
        $socialLinks = StreamerSocialLink::factory()->count(4)->create([
            'streamer_profile_id' => $streamerProfile->id
        ]);

        $this->assertCount(4, $streamerProfile->socialLinks);
        $this->assertInstanceOf(StreamerSocialLink::class, $streamerProfile->socialLinks->first());
    }

    public function test_streamer_profile_has_many_followers()
    {
        $streamerProfile = StreamerProfile::factory()->create();
        $followers = User::factory()->count(3)->create();
        
        foreach ($followers as $follower) {
            $streamerProfile->followers()->attach($follower->id, [
                'notification_preferences' => json_encode(['live' => true, 'reviews' => true])
            ]);
        }

        $this->assertCount(3, $streamerProfile->followers);
        $this->assertInstanceOf(User::class, $streamerProfile->followers->first());
    }

    public function test_streamer_profile_has_many_reviews()
    {
        $streamerProfile = StreamerProfile::factory()->create();
        $reviews = Review::factory()->count(2)->create([
            'streamer_profile_id' => $streamerProfile->id
        ]);

        $this->assertCount(2, $streamerProfile->reviews);
        $this->assertInstanceOf(Review::class, $streamerProfile->reviews->first());
    }

    public function test_approved_scope_filters_approved_profiles()
    {
        StreamerProfile::factory()->create(['is_approved' => true]);
        StreamerProfile::factory()->create(['is_approved' => false]);
        StreamerProfile::factory()->create(['is_approved' => true]);

        $approvedProfiles = StreamerProfile::approved()->get();

        $this->assertCount(2, $approvedProfiles);
        $this->assertTrue($approvedProfiles->every(fn($profile) => $profile->is_approved));
    }

    public function test_verified_scope_filters_verified_profiles()
    {
        StreamerProfile::factory()->create(['is_verified' => true]);
        StreamerProfile::factory()->create(['is_verified' => false]);
        StreamerProfile::factory()->create(['is_verified' => true]);

        $verifiedProfiles = StreamerProfile::verified()->get();

        $this->assertCount(2, $verifiedProfiles);
        $this->assertTrue($verifiedProfiles->every(fn($profile) => $profile->is_verified));
    }

    public function test_platform_scope_filters_by_platform()
    {
        StreamerProfile::factory()->twitch()->create();
        StreamerProfile::factory()->youtube()->create();
        StreamerProfile::factory()->kick()->create();
        StreamerProfile::factory()->twitch()->create();

        $twitchProfiles = StreamerProfile::platform('twitch')->get();
        $youtubeProfiles = StreamerProfile::platform('youtube')->get();
        $kickProfiles = StreamerProfile::platform('kick')->get();

        $this->assertCount(2, $twitchProfiles);
        $this->assertCount(1, $youtubeProfiles);
        $this->assertCount(1, $kickProfiles);
        
        $this->assertTrue($twitchProfiles->every(fn($profile) => $profile->platform === 'twitch'));
        $this->assertTrue($youtubeProfiles->every(fn($profile) => $profile->platform === 'youtube'));
        $this->assertTrue($kickProfiles->every(fn($profile) => $profile->platform === 'kick'));
    }

    public function test_is_live_returns_false_by_default()
    {
        $streamerProfile = StreamerProfile::factory()->create();

        $this->assertFalse($streamerProfile->isLive());
    }

    public function test_get_display_name_returns_channel_name()
    {
        $streamerProfile = StreamerProfile::factory()->create(['channel_name' => 'TestStreamer']);

        $this->assertEquals('TestStreamer', $streamerProfile->getDisplayName());
    }

    public function test_can_post_reviews_requires_approved_and_verified()
    {
        $notApprovedNotVerified = StreamerProfile::factory()->create([
            'is_approved' => false,
            'is_verified' => false
        ]);
        $approvedNotVerified = StreamerProfile::factory()->create([
            'is_approved' => true,
            'is_verified' => false
        ]);
        $notApprovedVerified = StreamerProfile::factory()->create([
            'is_approved' => false,
            'is_verified' => true
        ]);
        $approvedAndVerified = StreamerProfile::factory()->create([
            'is_approved' => true,
            'is_verified' => true
        ]);

        $this->assertFalse($notApprovedNotVerified->canPostReviews());
        $this->assertFalse($approvedNotVerified->canPostReviews());
        $this->assertFalse($notApprovedVerified->canPostReviews());
        $this->assertTrue($approvedAndVerified->canPostReviews());
    }

    public function test_oauth_tokens_are_hidden()
    {
        $streamerProfile = StreamerProfile::factory()->create([
            'oauth_token' => 'secret_token',
            'oauth_refresh_token' => 'secret_refresh_token'
        ]);

        $array = $streamerProfile->toArray();

        $this->assertArrayNotHasKey('oauth_token', $array);
        $this->assertArrayNotHasKey('oauth_refresh_token', $array);
    }

    public function test_casts_work_correctly()
    {
        $streamerProfile = StreamerProfile::factory()->create([
            'is_verified' => 1,
            'is_approved' => 0,
            'oauth_expires_at' => '2024-12-31 23:59:59'
        ]);

        $this->assertIsBool($streamerProfile->is_verified);
        $this->assertIsBool($streamerProfile->is_approved);
        $this->assertInstanceOf(\Carbon\Carbon::class, $streamerProfile->oauth_expires_at);
        $this->assertTrue($streamerProfile->is_verified);
        $this->assertFalse($streamerProfile->is_approved);
    }
}