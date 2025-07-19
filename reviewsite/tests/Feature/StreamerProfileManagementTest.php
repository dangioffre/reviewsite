<?php

namespace Tests\Feature;

use App\Models\StreamerProfile;
use App\Models\StreamerSchedule;
use App\Models\StreamerSocialLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StreamerProfileManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_authenticated_user_can_view_profile_creation_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('streamer.profiles.create'));

        $response->assertStatus(200);
        $response->assertViewIs('streamer.profiles.create');
    }

    public function test_guest_cannot_view_profile_creation_page()
    {
        $response = $this->get(route('streamer.profiles.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_complete_profile_setup_after_oauth()
    {
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'bio' => null,
        ]);
        
        // Ensure the user relationship is properly loaded
        $user->refresh();

        $profileData = [
            'bio' => 'This is my streaming bio',
            'schedules' => [
                [
                    'day_of_week' => 1, // Monday
                    'start_time' => '19:00',
                    'end_time' => '22:00',
                    'timezone' => 'America/New_York',
                    'notes' => 'Regular Monday stream',
                ],
                [
                    'day_of_week' => 3, // Wednesday
                    'start_time' => '20:00',
                    'end_time' => '23:00',
                    'timezone' => 'America/New_York',
                    'notes' => 'Midweek gaming',
                ],
            ],
            'social_links' => [
                [
                    'platform' => 'twitter',
                    'url' => 'https://twitter.com/teststreamer',
                    'display_name' => '@teststreamer',
                ],
                [
                    'platform' => 'discord',
                    'url' => 'https://discord.gg/testserver',
                    'display_name' => 'Test Server',
                ],
            ],
        ];

        $response = $this->actingAs($user)->post(route('streamer.profiles.store'), $profileData);

        $response->assertRedirect(route('streamer.profile.show', $streamerProfile));

        // Verify profile was updated
        $streamerProfile->refresh();
        $this->assertEquals('This is my streaming bio', $streamerProfile->bio);

        // Verify schedules were created
        $this->assertCount(2, $streamerProfile->schedules);
        $mondaySchedule = $streamerProfile->schedules->where('day_of_week', 1)->first();
        $this->assertEquals('19:00:00', $mondaySchedule->start_time->format('H:i:s'));
        $this->assertEquals('22:00:00', $mondaySchedule->end_time->format('H:i:s'));
        $this->assertEquals('America/New_York', $mondaySchedule->timezone);

        // Verify social links were created
        $this->assertCount(2, $streamerProfile->socialLinks);
        $twitterLink = $streamerProfile->socialLinks->where('platform', 'twitter')->first();
        $this->assertEquals('https://twitter.com/teststreamer', $twitterLink->url);
    }

    public function test_user_without_oauth_connection_cannot_complete_setup()
    {
        $user = User::factory()->create();

        $profileData = [
            'bio' => 'This is my streaming bio',
        ];

        $response = $this->actingAs($user)->post(route('streamer.profiles.store'), $profileData);

        $response->assertRedirect(route('streamer.profiles.create'));
        $response->assertSessionHas('error', 'Please connect your streaming platform first.');
    }

    public function test_user_can_view_their_profile_edit_page()
    {
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('streamer.profile.edit', $streamerProfile));

        $response->assertStatus(200);
        $response->assertViewIs('streamer.profiles.edit');
        $response->assertViewHas('streamerProfile', $streamerProfile);
    }

    public function test_user_cannot_edit_other_users_profile()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get(route('streamer.profile.edit', $streamerProfile));

        $response->assertStatus(403);
    }

    public function test_user_can_update_their_profile()
    {
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'bio' => 'Old bio',
        ]);

        $schedule = StreamerSchedule::factory()->create([
            'streamer_profile_id' => $streamerProfile->id,
            'day_of_week' => 1,
            'start_time' => '19:00:00',
            'end_time' => '22:00:00',
        ]);

        $socialLink = StreamerSocialLink::factory()->create([
            'streamer_profile_id' => $streamerProfile->id,
            'platform' => 'twitter',
            'url' => 'https://twitter.com/old',
        ]);

        $updateData = [
            'bio' => 'Updated bio content',
            'schedules' => [
                [
                    'id' => $schedule->id,
                    'day_of_week' => 1,
                    'start_time' => '20:00',
                    'end_time' => '23:00',
                    'timezone' => 'America/Los_Angeles',
                    'notes' => 'Updated schedule',
                    'is_active' => true,
                ],
                [
                    'day_of_week' => 5, // New Friday schedule
                    'start_time' => '18:00',
                    'end_time' => '21:00',
                    'timezone' => 'America/Los_Angeles',
                    'notes' => 'Friday fun',
                ],
            ],
            'social_links' => [
                [
                    'id' => $socialLink->id,
                    'platform' => 'twitter',
                    'url' => 'https://twitter.com/updated',
                    'display_name' => '@updated',
                ],
                [
                    'platform' => 'youtube',
                    'url' => 'https://youtube.com/newchannel',
                    'display_name' => 'New Channel',
                ],
            ],
        ];

        $response = $this->actingAs($user)->put(route('streamer.profile.update', $streamerProfile), $updateData);

        $response->assertRedirect(route('streamer.profile.show', $streamerProfile));
        $response->assertSessionHas('success', 'Profile updated successfully!');

        // Verify profile was updated
        $streamerProfile->refresh();
        $this->assertEquals('Updated bio content', $streamerProfile->bio);

        // Verify schedules were updated
        $this->assertCount(2, $streamerProfile->schedules);
        $updatedSchedule = $streamerProfile->schedules->where('id', $schedule->id)->first();
        $this->assertEquals('20:00:00', $updatedSchedule->start_time->format('H:i:s'));
        $this->assertEquals('America/Los_Angeles', $updatedSchedule->timezone);

        $newSchedule = $streamerProfile->schedules->where('day_of_week', 5)->first();
        $this->assertNotNull($newSchedule);
        $this->assertEquals('18:00:00', $newSchedule->start_time->format('H:i:s'));

        // Verify social links were updated
        $this->assertCount(2, $streamerProfile->socialLinks);
        $updatedLink = $streamerProfile->socialLinks->where('id', $socialLink->id)->first();
        $this->assertEquals('https://twitter.com/updated', $updatedLink->url);

        $newLink = $streamerProfile->socialLinks->where('platform', 'youtube')->first();
        $this->assertNotNull($newLink);
        $this->assertEquals('https://youtube.com/newchannel', $newLink->url);
    }

    public function test_validation_fails_for_invalid_schedule_times()
    {
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create(['user_id' => $user->id]);

        $invalidData = [
            'schedules' => [
                [
                    'day_of_week' => 1,
                    'start_time' => '22:00',
                    'end_time' => '19:00', // End time before start time
                    'timezone' => 'America/New_York',
                ],
            ],
        ];

        $response = $this->actingAs($user)->put(route('streamer.profile.update', $streamerProfile), $invalidData);

        $response->assertSessionHasErrors(['schedules.0.end_time']);
    }

    public function test_validation_fails_for_invalid_timezone()
    {
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create(['user_id' => $user->id]);

        $invalidData = [
            'schedules' => [
                [
                    'day_of_week' => 1,
                    'start_time' => '19:00',
                    'end_time' => '22:00',
                    'timezone' => 'Invalid/Timezone',
                ],
            ],
        ];

        $response = $this->actingAs($user)->put(route('streamer.profile.update', $streamerProfile), $invalidData);

        $response->assertSessionHasErrors(['schedules.0.timezone']);
    }

    public function test_validation_fails_for_overlapping_schedules()
    {
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create(['user_id' => $user->id]);

        $overlappingData = [
            'schedules' => [
                [
                    'day_of_week' => 1,
                    'start_time' => '19:00',
                    'end_time' => '22:00',
                    'timezone' => 'America/New_York',
                ],
                [
                    'day_of_week' => 1, // Same day
                    'start_time' => '21:00', // Overlaps with first schedule
                    'end_time' => '23:00',
                    'timezone' => 'America/New_York',
                ],
            ],
        ];

        $response = $this->actingAs($user)->put(route('streamer.profile.update', $streamerProfile), $overlappingData);

        $response->assertSessionHasErrors(['schedules.1.start_time']);
    }

    public function test_validation_limits_schedule_and_social_link_counts()
    {
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create(['user_id' => $user->id]);

        // Test too many schedules (more than 7)
        $tooManySchedules = [];
        for ($i = 0; $i < 8; $i++) {
            $tooManySchedules[] = [
                'day_of_week' => $i % 7,
                'start_time' => '19:00',
                'end_time' => '22:00',
                'timezone' => 'America/New_York',
            ];
        }

        $response = $this->actingAs($user)->put(route('streamer.profile.update', $streamerProfile), [
            'schedules' => $tooManySchedules,
        ]);

        $response->assertSessionHasErrors(['schedules']);

        // Test too many social links (more than 10)
        $tooManySocialLinks = [];
        for ($i = 0; $i < 11; $i++) {
            $tooManySocialLinks[] = [
                'platform' => "platform{$i}",
                'url' => "https://example{$i}.com",
            ];
        }

        $response = $this->actingAs($user)->put(route('streamer.profile.update', $streamerProfile), [
            'social_links' => $tooManySocialLinks,
        ]);

        $response->assertSessionHasErrors(['social_links']);
    }

    public function test_public_can_view_approved_streamer_profiles()
    {
        $approvedProfile = StreamerProfile::factory()->create([
            'is_approved' => true,
        ]);

        $response = $this->get(route('streamer.profile.show', $approvedProfile));

        $response->assertStatus(200);
        $response->assertViewIs('streamer.profiles.show');
        $response->assertViewHas('streamerProfile', $approvedProfile);
    }

    public function test_public_can_view_streamer_profiles_index()
    {
        StreamerProfile::factory()->count(5)->create(['is_approved' => true]);
        StreamerProfile::factory()->count(3)->create(['is_approved' => false]);

        $response = $this->get(route('streamer.profiles.index'));

        $response->assertStatus(200);
        $response->assertViewIs('streamer.profiles.index');
        
        // Should only show approved profiles
        $profiles = $response->viewData('profiles');
        $this->assertCount(5, $profiles);
        $this->assertTrue($profiles->every(fn($profile) => $profile->is_approved));
    }

    public function test_owner_can_view_unapproved_profile()
    {
        $user = User::factory()->create();
        $unapprovedProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_approved' => false,
        ]);

        $response = $this->actingAs($user)->get(route('streamer.profile.show', $unapprovedProfile));

        $response->assertStatus(200);
    }

    public function test_non_owner_cannot_view_unapproved_profile()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $unapprovedProfile = StreamerProfile::factory()->create([
            'user_id' => $otherUser->id,
            'is_approved' => false,
        ]);

        $response = $this->actingAs($user)->get(route('streamer.profile.show', $unapprovedProfile));

        $response->assertStatus(403);
    }
}
