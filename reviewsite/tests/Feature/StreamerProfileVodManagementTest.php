<?php

namespace Tests\Feature;

use App\Models\StreamerProfile;
use App\Models\StreamerVod;
use App\Models\User;
use App\Services\PlatformApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Mockery;

class StreamerProfileVodManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private StreamerProfile $streamerProfile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $this->user->id,
            'platform' => 'twitch',
            'is_approved' => true,
            'oauth_token' => 'test_token',
        ]);
    }

    /** @test */
    public function authenticated_streamer_can_view_vod_management_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('streamer.profile.manage-vods', $this->streamerProfile));

        $response->assertStatus(200);
        $response->assertViewIs('streamer.profiles.manage-vods');
        $response->assertViewHas('streamerProfile', $this->streamerProfile);
    }

    /** @test */
    public function unauthenticated_user_cannot_view_vod_management_page()
    {
        $response = $this->get(route('streamer.profile.manage-vods', $this->streamerProfile));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function user_cannot_manage_vods_for_other_users_profile()
    {
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)
            ->get(route('streamer.profile.manage-vods', $this->streamerProfile));

        $response->assertStatus(403);
    }

    /** @test */
    public function streamer_can_add_manual_vod()
    {
        $vodData = [
            'title' => 'My Manual VOD',
            'vod_url' => 'https://example.com/my-vod',
            'description' => 'This is a manually added VOD',
            'thumbnail_url' => 'https://example.com/thumbnail.jpg',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('streamer.profile.add-vod', $this->streamerProfile), $vodData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'VOD added successfully!');

        $this->assertDatabaseHas('streamer_vods', [
            'streamer_profile_id' => $this->streamerProfile->id,
            'title' => 'My Manual VOD',
            'vod_url' => 'https://example.com/my-vod',
            'description' => 'This is a manually added VOD',
            'thumbnail_url' => 'https://example.com/thumbnail.jpg',
            'is_manual' => true,
        ]);
    }

    /** @test */
    public function adding_manual_vod_requires_title_and_url()
    {
        $response = $this->actingAs($this->user)
            ->post(route('streamer.profile.add-vod', $this->streamerProfile), []);

        $response->assertSessionHasErrors(['title', 'vod_url']);
    }

    /** @test */
    public function adding_manual_vod_validates_url_format()
    {
        $vodData = [
            'title' => 'My Manual VOD',
            'vod_url' => 'not-a-valid-url',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('streamer.profile.add-vod', $this->streamerProfile), $vodData);

        $response->assertSessionHasErrors(['vod_url']);
    }

    /** @test */
    public function cannot_add_duplicate_vod_url()
    {
        // Create existing VOD
        $this->streamerProfile->vods()->create([
            'platform_vod_id' => 'existing_vod',
            'title' => 'Existing VOD',
            'vod_url' => 'https://example.com/existing-vod',
            'is_manual' => true,
        ]);

        $vodData = [
            'title' => 'Duplicate VOD',
            'vod_url' => 'https://example.com/existing-vod', // Same URL
        ];

        $response = $this->actingAs($this->user)
            ->post(route('streamer.profile.add-vod', $this->streamerProfile), $vodData);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'A VOD with this URL already exists.');
    }

    /** @test */
    public function streamer_can_import_vods_from_platform()
    {
        // Mock the PlatformApiService
        $mockService = Mockery::mock(PlatformApiService::class);
        $mockService->shouldReceive('importVods')
            ->once()
            ->with(Mockery::type(StreamerProfile::class), 20)
            ->andReturn(3);

        $this->app->instance(PlatformApiService::class, $mockService);

        $response = $this->actingAs($this->user)
            ->post(route('streamer.profile.import-vods', $this->streamerProfile));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Successfully imported 3 new VODs!');
    }

    /** @test */
    public function import_vods_shows_info_when_no_new_vods_found()
    {
        // Mock the PlatformApiService to return 0 imported VODs
        $mockService = Mockery::mock(PlatformApiService::class);
        $mockService->shouldReceive('importVods')
            ->once()
            ->with(Mockery::type(StreamerProfile::class), 20)
            ->andReturn(0);

        $this->app->instance(PlatformApiService::class, $mockService);

        $response = $this->actingAs($this->user)
            ->post(route('streamer.profile.import-vods', $this->streamerProfile));

        $response->assertRedirect();
        $response->assertSessionHas('info', 'No new VODs found to import.');
    }

    /** @test */
    public function import_vods_handles_api_errors_gracefully()
    {
        // Mock the PlatformApiService to throw an exception
        $mockService = Mockery::mock(PlatformApiService::class);
        $mockService->shouldReceive('importVods')
            ->once()
            ->andThrow(new \Exception('API connection failed'));

        $this->app->instance(PlatformApiService::class, $mockService);

        $response = $this->actingAs($this->user)
            ->post(route('streamer.profile.import-vods', $this->streamerProfile));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Failed to import VODs: API connection failed');
    }

    /** @test */
    public function streamer_can_delete_their_vod()
    {
        $vod = $this->streamerProfile->vods()->create([
            'platform_vod_id' => 'vod_to_delete',
            'title' => 'VOD to Delete',
            'vod_url' => 'https://example.com/vod-to-delete',
            'is_manual' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('streamer.profile.delete-vod', [$this->streamerProfile, $vod]));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'VOD deleted successfully!');

        $this->assertDatabaseMissing('streamer_vods', [
            'id' => $vod->id,
        ]);
    }

    /** @test */
    public function streamer_cannot_delete_vod_from_other_profile()
    {
        $otherUser = User::factory()->create();
        $otherProfile = StreamerProfile::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $vod = $otherProfile->vods()->create([
            'platform_vod_id' => 'other_vod',
            'title' => 'Other VOD',
            'vod_url' => 'https://example.com/other-vod',
            'is_manual' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('streamer.profile.delete-vod', [$this->streamerProfile, $vod]));

        $response->assertStatus(403);

        // VOD should still exist
        $this->assertDatabaseHas('streamer_vods', [
            'id' => $vod->id,
        ]);
    }

    /** @test */
    public function vod_management_page_displays_vods_correctly()
    {
        // Create some test VODs
        $manualVod = $this->streamerProfile->vods()->create([
            'platform_vod_id' => 'manual_vod',
            'title' => 'Manual VOD',
            'vod_url' => 'https://example.com/manual-vod',
            'description' => 'Manually added VOD',
            'is_manual' => true,
            'published_at' => now()->subDays(1),
        ]);

        $importedVod = $this->streamerProfile->vods()->create([
            'platform_vod_id' => 'imported_vod',
            'title' => 'Imported VOD',
            'vod_url' => 'https://twitch.tv/videos/imported_vod',
            'description' => 'Imported from Twitch',
            'thumbnail_url' => 'https://example.com/thumb.jpg',
            'duration_seconds' => 3600,
            'is_manual' => false,
            'published_at' => now()->subDays(2),
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('streamer.profile.manage-vods', $this->streamerProfile));

        $response->assertStatus(200);
        $response->assertSee('Manual VOD');
        $response->assertSee('Imported VOD');
        $response->assertSee('Manual'); // Badge for manual VOD
        $response->assertSee('Imported'); // Badge for imported VOD
        $response->assertSee('1:00:00'); // Formatted duration
    }

    /** @test */
    public function vod_management_page_shows_empty_state_when_no_vods()
    {
        $response = $this->actingAs($this->user)
            ->get(route('streamer.profile.manage-vods', $this->streamerProfile));

        $response->assertStatus(200);
        $response->assertSee('No VODs Found');
        $response->assertSee('Add your first VOD manually');
    }

    /** @test */
    public function streamer_can_trigger_vod_health_check()
    {
        // Create some VODs for the profile
        $this->streamerProfile->vods()->create([
            'platform_vod_id' => 'vod_1',
            'title' => 'Test VOD 1',
            'vod_url' => 'https://example.com/vod1',
            'is_manual' => true,
            'health_status' => 'unchecked',
        ]);

        $this->streamerProfile->vods()->create([
            'platform_vod_id' => 'vod_2',
            'title' => 'Test VOD 2',
            'vod_url' => 'https://example.com/vod2',
            'is_manual' => false,
            'health_status' => 'healthy',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('streamer.profile.check-vod-health', $this->streamerProfile));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Health check started for 2 VODs. Results will be updated shortly.');
    }

    /** @test */
    public function vod_health_check_shows_info_when_no_vods()
    {
        $response = $this->actingAs($this->user)
            ->post(route('streamer.profile.check-vod-health', $this->streamerProfile));

        $response->assertRedirect();
        $response->assertSessionHas('info', 'No VODs found to check.');
    }

    /** @test */
    public function user_cannot_trigger_health_check_for_other_users_profile()
    {
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)
            ->post(route('streamer.profile.check-vod-health', $this->streamerProfile));

        $response->assertStatus(403);
    }

    /** @test */
    public function vod_management_page_displays_health_status_badges()
    {
        // Create VODs with different health statuses
        $healthyVod = $this->streamerProfile->vods()->create([
            'platform_vod_id' => 'healthy_vod',
            'title' => 'Healthy VOD',
            'vod_url' => 'https://example.com/healthy-vod',
            'health_status' => 'healthy',
            'is_manual' => true,
        ]);

        $unhealthyVod = $this->streamerProfile->vods()->create([
            'platform_vod_id' => 'unhealthy_vod',
            'title' => 'Unhealthy VOD',
            'vod_url' => 'https://example.com/unhealthy-vod',
            'health_status' => 'unhealthy',
            'health_check_error' => 'Connection timeout',
            'is_manual' => false,
        ]);

        $uncheckedVod = $this->streamerProfile->vods()->create([
            'platform_vod_id' => 'unchecked_vod',
            'title' => 'Unchecked VOD',
            'vod_url' => 'https://example.com/unchecked-vod',
            'health_status' => 'unchecked',
            'is_manual' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('streamer.profile.manage-vods', $this->streamerProfile));

        $response->assertStatus(200);
        $response->assertSee('Healthy VOD');
        $response->assertSee('Unhealthy VOD');
        $response->assertSee('Unchecked VOD');
        
        // Check for health status badges
        $response->assertSee('Healthy'); // Health status badge
        $response->assertSee('Broken'); // Unhealthy status badge
        $response->assertSee('Unchecked'); // Unchecked status badge
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
