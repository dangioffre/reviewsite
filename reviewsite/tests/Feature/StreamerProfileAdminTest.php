<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\StreamerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StreamerProfileAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an admin user
        $this->admin = User::factory()->create([
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);
    }

    public function test_admin_can_access_streamer_profiles_index()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/streamer-profiles');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_streamer_profile_create_page()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/streamer-profiles/create');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_streamer_profile_edit_page()
    {
        $user = User::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/admin/streamer-profiles/{$streamerProfile->id}/edit");

        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_streamer_profiles()
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)
            ->get('/admin/streamer-profiles');

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_streamer_profiles()
    {
        $response = $this->get('/admin/streamer-profiles');

        $response->assertRedirect('/admin/login');
    }
}