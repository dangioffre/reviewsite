<?php

namespace Tests\Feature;

use App\Models\StreamerProfile;
use App\Models\User;
use App\Services\PlatformApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class StreamerVerificationSystemTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock the PlatformApiService
        $this->mock(PlatformApiService::class, function ($mock) {
            $mock->shouldReceive('validateChannelOwnership')
                 ->andReturn(true)
                 ->byDefault();
        });
    }

    /** @test */
    public function user_can_view_verification_status_page()
    {
        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'verification_status' => 'pending',
        ]);

        $response = $this->actingAs($user)
            ->get(route('streamer.verification.show', $profile));

        $response->assertOk()
            ->assertViewIs('streamer.verification.show')
            ->assertViewHas('profile', $profile)
            ->assertSee('Verification Status')
            ->assertSee('Verification Not Requested');
    }

    /** @test */
    public function user_cannot_view_other_users_verification_status()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)
            ->get(route('streamer.verification.show', $profile));

        $response->assertForbidden();
    }

    /** @test */
    public function user_can_request_verification_for_approved_profile()
    {
        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_approved' => true,
            'verification_status' => 'pending',
        ]);

        $response = $this->actingAs($user)
            ->post(route('streamer.verification.request', $profile));

        $response->assertRedirect()
            ->assertSessionHas('success', 'Verification request submitted successfully. Our team will review your profile.');

        $profile->refresh();
        $this->assertEquals('requested', $profile->verification_status);
        $this->assertNotNull($profile->verification_token);
        $this->assertNotNull($profile->verification_requested_at);
    }

    /** @test */
    public function user_cannot_request_verification_for_unapproved_profile()
    {
        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_approved' => false,
            'verification_status' => 'pending',
        ]);

        $response = $this->actingAs($user)
            ->post(route('streamer.verification.request', $profile));

        $response->assertRedirect()
            ->assertSessionHas('error', 'Verification cannot be requested at this time.');

        $profile->refresh();
        $this->assertEquals('pending', $profile->verification_status);
    }

    /** @test */
    public function user_cannot_request_verification_for_already_verified_profile()
    {
        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_approved' => true,
            'verification_status' => 'verified',
            'is_verified' => true,
        ]);

        $response = $this->actingAs($user)
            ->post(route('streamer.verification.request', $profile));

        $response->assertRedirect()
            ->assertSessionHas('error', 'Verification cannot be requested at this time.');
    }

    /** @test */
    public function user_can_cancel_verification_request()
    {
        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'verification_status' => 'requested',
            'verification_token' => 'test-token',
            'verification_requested_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->delete(route('streamer.verification.cancel', $profile));

        $response->assertRedirect()
            ->assertSessionHas('success', 'Verification request cancelled.');

        $profile->refresh();
        $this->assertEquals('pending', $profile->verification_status);
        $this->assertNull($profile->verification_token);
        $this->assertNull($profile->verification_requested_at);
    }

    /** @test */
    public function user_cannot_cancel_verification_in_review()
    {
        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'verification_status' => 'in_review',
        ]);

        $response = $this->actingAs($user)
            ->delete(route('streamer.verification.cancel', $profile));

        $response->assertRedirect()
            ->assertSessionHas('error', 'Cannot cancel verification at this stage.');
    }

    /** @test */
    public function verification_request_fails_when_channel_ownership_cannot_be_validated()
    {
        // Mock the service to return false for ownership validation
        $this->mock(PlatformApiService::class, function ($mock) {
            $mock->shouldReceive('validateChannelOwnership')
                 ->andReturn(false);
        });

        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_approved' => true,
            'verification_status' => 'pending',
        ]);

        $response = $this->actingAs($user)
            ->post(route('streamer.verification.request', $profile));

        $response->assertRedirect()
            ->assertSessionHas('error', 'Unable to verify channel ownership. Please ensure your OAuth connection is valid.');

        $profile->refresh();
        $this->assertEquals('pending', $profile->verification_status);
    }

    /** @test */
    public function verification_request_handles_api_exceptions_gracefully()
    {
        // Mock the service to throw an exception
        $this->mock(PlatformApiService::class, function ($mock) {
            $mock->shouldReceive('validateChannelOwnership')
                 ->andThrow(new \Exception('API Error'));
        });

        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_approved' => true,
            'verification_status' => 'pending',
        ]);

        $response = $this->actingAs($user)
            ->post(route('streamer.verification.request', $profile));

        $response->assertRedirect()
            ->assertSessionHas('error', 'An error occurred while requesting verification. Please try again later.');

        $profile->refresh();
        $this->assertEquals('pending', $profile->verification_status);
    }

    /** @test */
    public function user_cannot_request_verification_for_other_users_profile()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $otherUser->id,
            'is_approved' => true,
            'verification_status' => 'pending',
        ]);

        $response = $this->actingAs($user)
            ->post(route('streamer.verification.request', $profile));

        $response->assertForbidden();
    }

    /** @test */
    public function user_can_request_verification_again_after_rejection()
    {
        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_approved' => true,
            'verification_status' => 'rejected',
            'verification_notes' => 'Previous rejection reason',
        ]);

        $response = $this->actingAs($user)
            ->post(route('streamer.verification.request', $profile));

        $response->assertRedirect()
            ->assertSessionHas('success', 'Verification request submitted successfully. Our team will review your profile.');

        $profile->refresh();
        $this->assertEquals('requested', $profile->verification_status);
        $this->assertNotNull($profile->verification_token);
        $this->assertNotNull($profile->verification_requested_at);
    }

    /** @test */
    public function verification_status_displays_correctly_on_profile_page()
    {
        $user = User::factory()->create();
        
        // Test different verification statuses
        $statuses = [
            'pending' => 'Not Requested',
            'requested' => 'Verification Requested',
            'in_review' => 'Under Review',
            'verified' => 'Verified Channel',
            'rejected' => 'Verification Rejected',
        ];

        foreach ($statuses as $status => $expectedText) {
            $profile = StreamerProfile::factory()->create([
                'user_id' => $user->id,
                'verification_status' => $status,
                'is_verified' => $status === 'verified',
            ]);

            $response = $this->actingAs($user)
                ->get(route('streamer.verification.show', $profile));

            $response->assertOk()
                ->assertSee($expectedText);
        }
    }

    /** @test */
    public function verification_badge_displays_correctly_on_public_profile()
    {
        $profile = StreamerProfile::factory()->create([
            'verification_status' => 'verified',
            'is_verified' => true,
            'is_approved' => true,
        ]);

        $response = $this->get(route('streamer.profile.show', $profile));

        $response->assertOk()
            ->assertSee('Verified');
    }

    /** @test */
    public function verification_link_appears_for_profile_owner()
    {
        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_approved' => true,
        ]);

        $response = $this->actingAs($user)
            ->get(route('streamer.profile.show', $profile));

        $response->assertOk()
            ->assertSee('Verification')
            ->assertSee(route('streamer.verification.show', $profile));
    }

    /** @test */
    public function verification_link_does_not_appear_for_other_users()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $otherUser->id,
            'is_approved' => true,
        ]);

        $response = $this->actingAs($user)
            ->get(route('streamer.profile.show', $profile));

        $response->assertOk()
            ->assertDontSee('Verification');
    }
}