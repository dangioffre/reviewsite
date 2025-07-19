<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\StreamerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserStreamerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_one_streamer_profile()
    {
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(StreamerProfile::class, $user->streamerProfile);
        $this->assertEquals($streamerProfile->id, $user->streamerProfile->id);
    }

    public function test_user_can_follow_many_streamers()
    {
        $user = User::factory()->create();
        $streamers = StreamerProfile::factory()->count(3)->create();
        
        foreach ($streamers as $streamer) {
            $user->followedStreamers()->attach($streamer->id, [
                'notification_preferences' => json_encode(['live' => true, 'reviews' => false])
            ]);
        }

        $this->assertCount(3, $user->followedStreamers);
        $this->assertInstanceOf(StreamerProfile::class, $user->followedStreamers->first());
        
        // Test pivot data
        $firstFollowed = $user->followedStreamers->first();
        $this->assertNotNull($firstFollowed->pivot->notification_preferences);
    }

    public function test_is_streamer_returns_true_when_user_has_streamer_profile()
    {
        $user = User::factory()->create();
        $this->assertFalse($user->isStreamer());

        StreamerProfile::factory()->create(['user_id' => $user->id]);
        $user->refresh();
        
        $this->assertTrue($user->isStreamer());
    }

    public function test_can_create_streamer_profile_returns_true_when_no_profile_exists()
    {
        $user = User::factory()->create();
        $this->assertTrue($user->canCreateStreamerProfile());

        StreamerProfile::factory()->create(['user_id' => $user->id]);
        $user->refresh();
        
        $this->assertFalse($user->canCreateStreamerProfile());
    }
}