<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\StreamerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LiveStatusMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_streamer_profile_is_live_returns_manual_override_when_set()
    {
        // Arrange
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_live' => false,
            'manual_live_override' => true
        ]);

        // Act & Assert
        $this->assertTrue($streamerProfile->isLive());
    }

    public function test_streamer_profile_is_live_returns_false_when_manual_override_is_false()
    {
        // Arrange
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_live' => true,
            'manual_live_override' => false
        ]);

        // Act & Assert
        $this->assertFalse($streamerProfile->isLive());
    }

    public function test_streamer_profile_is_live_returns_database_value_when_no_manual_override()
    {
        // Arrange
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_live' => true,
            'manual_live_override' => null
        ]);

        // Act & Assert
        $this->assertTrue($streamerProfile->isLive());
    }

    public function test_streamer_profile_is_live_returns_false_when_no_manual_override_and_not_live()
    {
        // Arrange
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_live' => false,
            'manual_live_override' => null
        ]);

        // Act & Assert
        $this->assertFalse($streamerProfile->isLive());
    }

    public function test_live_scope_includes_manual_override_true()
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $liveStreamer = StreamerProfile::factory()->create([
            'user_id' => $user1->id,
            'is_live' => false,
            'manual_live_override' => true
        ]);

        $offlineStreamer = StreamerProfile::factory()->create([
            'user_id' => $user2->id,
            'is_live' => false,
            'manual_live_override' => false
        ]);

        // Act
        $liveStreamers = StreamerProfile::live()->get();

        // Assert
        $this->assertCount(1, $liveStreamers);
        $this->assertEquals($liveStreamer->id, $liveStreamers->first()->id);
    }

    public function test_live_scope_includes_database_live_when_no_manual_override()
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $liveStreamer = StreamerProfile::factory()->create([
            'user_id' => $user1->id,
            'is_live' => true,
            'manual_live_override' => null
        ]);

        $offlineStreamer = StreamerProfile::factory()->create([
            'user_id' => $user2->id,
            'is_live' => false,
            'manual_live_override' => null
        ]);

        // Act
        $liveStreamers = StreamerProfile::live()->get();

        // Assert
        $this->assertCount(1, $liveStreamers);
        $this->assertEquals($liveStreamer->id, $liveStreamers->first()->id);
    }

    public function test_live_scope_excludes_database_live_when_manual_override_is_false()
    {
        // Arrange
        $user = User::factory()->create();
        
        $streamer = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_live' => true,
            'manual_live_override' => false
        ]);

        // Act
        $liveStreamers = StreamerProfile::live()->get();

        // Assert
        $this->assertCount(0, $liveStreamers);
    }

    public function test_update_live_status_updates_database_fields()
    {
        // Arrange
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_live' => false,
            'live_status_checked_at' => null
        ]);

        // Act
        $streamerProfile->updateLiveStatus(true);

        // Assert
        $streamerProfile->refresh();
        $this->assertTrue($streamerProfile->is_live);
        $this->assertNotNull($streamerProfile->live_status_checked_at);
    }

    public function test_set_manual_live_override_updates_database_field()
    {
        // Arrange
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'manual_live_override' => null
        ]);

        // Act
        $streamerProfile->setManualLiveOverride(true);

        // Assert
        $streamerProfile->refresh();
        $this->assertTrue($streamerProfile->manual_live_override);
    }

    public function test_set_manual_live_override_can_be_set_to_null()
    {
        // Arrange
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'manual_live_override' => true
        ]);

        // Act
        $streamerProfile->setManualLiveOverride(null);

        // Assert
        $streamerProfile->refresh();
        $this->assertNull($streamerProfile->manual_live_override);
    }

    public function test_complex_live_scope_scenarios()
    {
        // Arrange
        $users = User::factory()->count(5)->create();
        
        // Scenario 1: Manual override true (should be included)
        $streamer1 = StreamerProfile::factory()->create([
            'user_id' => $users[0]->id,
            'is_live' => false,
            'manual_live_override' => true
        ]);

        // Scenario 2: Manual override false (should be excluded)
        $streamer2 = StreamerProfile::factory()->create([
            'user_id' => $users[1]->id,
            'is_live' => true,
            'manual_live_override' => false
        ]);

        // Scenario 3: No manual override, is_live true (should be included)
        $streamer3 = StreamerProfile::factory()->create([
            'user_id' => $users[2]->id,
            'is_live' => true,
            'manual_live_override' => null
        ]);

        // Scenario 4: No manual override, is_live false (should be excluded)
        $streamer4 = StreamerProfile::factory()->create([
            'user_id' => $users[3]->id,
            'is_live' => false,
            'manual_live_override' => null
        ]);

        // Scenario 5: Manual override null, is_live false (should be excluded)
        $streamer5 = StreamerProfile::factory()->create([
            'user_id' => $users[4]->id,
            'is_live' => false,
            'manual_live_override' => null
        ]);

        // Act
        $liveStreamers = StreamerProfile::live()->get();

        // Assert
        $this->assertCount(2, $liveStreamers);
        $liveStreamerIds = $liveStreamers->pluck('id')->toArray();
        $this->assertContains($streamer1->id, $liveStreamerIds);
        $this->assertContains($streamer3->id, $liveStreamerIds);
        $this->assertNotContains($streamer2->id, $liveStreamerIds);
        $this->assertNotContains($streamer4->id, $liveStreamerIds);
        $this->assertNotContains($streamer5->id, $liveStreamerIds);
    }
}