<?php

namespace Tests\Unit\Models;

use App\Models\StreamerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StreamerProfileVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_request_verification_for_approved_pending_profile()
    {
        $profile = StreamerProfile::factory()->create([
            'is_approved' => true,
            'verification_status' => 'pending',
        ]);

        $this->assertTrue($profile->canRequestVerification());
    }

    /** @test */
    public function can_request_verification_for_approved_rejected_profile()
    {
        $profile = StreamerProfile::factory()->create([
            'is_approved' => true,
            'verification_status' => 'rejected',
        ]);

        $this->assertTrue($profile->canRequestVerification());
    }

    /** @test */
    public function cannot_request_verification_for_unapproved_profile()
    {
        $profile = StreamerProfile::factory()->create([
            'is_approved' => false,
            'verification_status' => 'pending',
        ]);

        $this->assertFalse($profile->canRequestVerification());
    }

    /** @test */
    public function cannot_request_verification_for_already_requested_profile()
    {
        $profile = StreamerProfile::factory()->create([
            'is_approved' => true,
            'verification_status' => 'requested',
        ]);

        $this->assertFalse($profile->canRequestVerification());
    }

    /** @test */
    public function cannot_request_verification_for_verified_profile()
    {
        $profile = StreamerProfile::factory()->create([
            'is_approved' => true,
            'verification_status' => 'verified',
            'is_verified' => true,
        ]);

        $this->assertFalse($profile->canRequestVerification());
    }

    /** @test */
    public function request_verification_updates_status_and_token()
    {
        $profile = StreamerProfile::factory()->create([
            'verification_status' => 'pending',
        ]);

        $profile->requestVerification();

        $this->assertEquals('requested', $profile->verification_status);
        $this->assertNotNull($profile->verification_token);
        $this->assertNotNull($profile->verification_requested_at);
        $this->assertEquals(32, strlen($profile->verification_token));
    }

    /** @test */
    public function is_verified_returns_true_for_verified_profile()
    {
        $profile = StreamerProfile::factory()->create([
            'verification_status' => 'verified',
            'is_verified' => true,
        ]);

        $this->assertTrue($profile->isVerified());
    }

    /** @test */
    public function is_verified_returns_false_for_non_verified_profile()
    {
        $profile = StreamerProfile::factory()->create([
            'verification_status' => 'requested',
            'is_verified' => false,
        ]);

        $this->assertFalse($profile->isVerified());
    }

    /** @test */
    public function verify_method_updates_profile_correctly()
    {
        $verifier = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'verification_status' => 'requested',
            'is_verified' => false,
        ]);

        $notes = 'Channel ownership confirmed via API';
        $profile->verify($verifier, $notes);

        $this->assertEquals('verified', $profile->verification_status);
        $this->assertTrue($profile->is_verified);
        $this->assertEquals($notes, $profile->verification_notes);
        $this->assertEquals($verifier->id, $profile->verified_by);
        $this->assertNotNull($profile->verification_completed_at);
    }

    /** @test */
    public function reject_verification_method_updates_profile_correctly()
    {
        $verifier = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'verification_status' => 'in_review',
            'is_verified' => false,
        ]);

        $notes = 'Unable to confirm channel ownership';
        $profile->rejectVerification($verifier, $notes);

        $this->assertEquals('rejected', $profile->verification_status);
        $this->assertFalse($profile->is_verified);
        $this->assertEquals($notes, $profile->verification_notes);
        $this->assertEquals($verifier->id, $profile->verified_by);
        $this->assertNotNull($profile->verification_completed_at);
    }

    /** @test */
    public function set_in_review_updates_status()
    {
        $profile = StreamerProfile::factory()->create([
            'verification_status' => 'requested',
        ]);

        $profile->setInReview();

        $this->assertEquals('in_review', $profile->verification_status);
    }

    /** @test */
    public function get_verification_badge_color_returns_correct_colors()
    {
        $testCases = [
            'pending' => 'gray',
            'requested' => 'yellow',
            'in_review' => 'blue',
            'verified' => 'green',
            'rejected' => 'red',
            'unknown' => 'gray',
        ];

        foreach ($testCases as $status => $expectedColor) {
            if ($status === 'unknown') {
                // Test with a valid status but check the method with invalid input
                $profile = StreamerProfile::factory()->create([
                    'verification_status' => 'pending',
                ]);
                // Manually set invalid status to test the default case
                $profile->verification_status = 'invalid_status';
            } else {
                $profile = StreamerProfile::factory()->create([
                    'verification_status' => $status,
                ]);
            }

            $this->assertEquals($expectedColor, $profile->getVerificationBadgeColor());
        }
    }

    /** @test */
    public function get_verification_status_text_returns_correct_text()
    {
        $testCases = [
            'pending' => 'Not Requested',
            'requested' => 'Verification Requested',
            'in_review' => 'Under Review',
            'verified' => 'Verified',
            'rejected' => 'Verification Rejected',
            'unknown' => 'Unknown',
        ];

        foreach ($testCases as $status => $expectedText) {
            if ($status === 'unknown') {
                // Test with a valid status but check the method with invalid input
                $profile = StreamerProfile::factory()->create([
                    'verification_status' => 'pending',
                ]);
                // Manually set invalid status to test the default case
                $profile->verification_status = 'invalid_status';
            } else {
                $profile = StreamerProfile::factory()->create([
                    'verification_status' => $status,
                ]);
            }

            $this->assertEquals($expectedText, $profile->getVerificationStatusText());
        }
    }

    /** @test */
    public function can_post_reviews_requires_both_approval_and_verification()
    {
        // Not approved, not verified
        $profile1 = StreamerProfile::factory()->create([
            'is_approved' => false,
            'is_verified' => false,
        ]);
        $this->assertFalse($profile1->canPostReviews());

        // Approved but not verified
        $profile2 = StreamerProfile::factory()->create([
            'is_approved' => true,
            'is_verified' => false,
        ]);
        $this->assertFalse($profile2->canPostReviews());

        // Not approved but verified
        $profile3 = StreamerProfile::factory()->create([
            'is_approved' => false,
            'is_verified' => true,
        ]);
        $this->assertFalse($profile3->canPostReviews());

        // Both approved and verified
        $profile4 = StreamerProfile::factory()->create([
            'is_approved' => true,
            'is_verified' => true,
        ]);
        $this->assertTrue($profile4->canPostReviews());
    }

    /** @test */
    public function verifier_relationship_works_correctly()
    {
        $verifier = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'verified_by' => $verifier->id,
        ]);

        $this->assertInstanceOf(User::class, $profile->verifier);
        $this->assertEquals($verifier->id, $profile->verifier->id);
    }

    /** @test */
    public function verified_scope_filters_correctly()
    {
        StreamerProfile::factory()->create([
            'verification_status' => 'verified',
            'is_verified' => true,
        ]);
        
        StreamerProfile::factory()->create([
            'verification_status' => 'pending',
            'is_verified' => false,
        ]);

        $verifiedProfiles = StreamerProfile::verified()->get();
        
        $this->assertCount(1, $verifiedProfiles);
        $this->assertTrue($verifiedProfiles->first()->is_verified);
    }
}