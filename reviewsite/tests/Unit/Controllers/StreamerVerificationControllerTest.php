<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\StreamerVerificationController;
use App\Models\StreamerProfile;
use App\Models\User;
use App\Services\PlatformApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class StreamerVerificationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $controller;
    protected $mockPlatformApiService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockPlatformApiService = Mockery::mock(PlatformApiService::class);
        $this->controller = new StreamerVerificationController($this->mockPlatformApiService);
    }

    /** @test */
    public function show_method_returns_correct_view_for_profile_owner()
    {
        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create(['user_id' => $user->id]);

        Auth::shouldReceive('id')->andReturn($user->id);

        $response = $this->controller->show($profile);

        $this->assertEquals('streamer.verification.show', $response->getName());
        $this->assertEquals($profile, $response->getData()['profile']);
    }

    /** @test */
    public function show_method_aborts_for_non_owner()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $profile = StreamerProfile::factory()->create(['user_id' => $otherUser->id]);

        Auth::shouldReceive('id')->andReturn($user->id);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('You can only view your own verification status.');

        $this->controller->show($profile);
    }

    /** @test */
    public function request_method_successfully_requests_verification()
    {
        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_approved' => true,
            'verification_status' => 'pending',
        ]);

        Auth::shouldReceive('id')->andReturn($user->id);
        $this->mockPlatformApiService
            ->shouldReceive('validateChannelOwnership')
            ->with($profile)
            ->andReturn(true);

        $response = $this->controller->request($profile);

        $this->assertTrue($response->isRedirect());
        
        $profile->refresh();
        $this->assertEquals('requested', $profile->verification_status);
        $this->assertNotNull($profile->verification_token);
        $this->assertNotNull($profile->verification_requested_at);
    }

    /** @test */
    public function request_method_fails_when_verification_cannot_be_requested()
    {
        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_approved' => false, // Not approved
            'verification_status' => 'pending',
        ]);

        Auth::shouldReceive('id')->andReturn($user->id);

        $response = $this->controller->request($profile);

        $this->assertTrue($response->isRedirect());
        
        $profile->refresh();
        $this->assertEquals('pending', $profile->verification_status);
    }

    /** @test */
    public function request_method_fails_when_channel_ownership_cannot_be_validated()
    {
        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_approved' => true,
            'verification_status' => 'pending',
        ]);

        Auth::shouldReceive('id')->andReturn($user->id);
        $this->mockPlatformApiService
            ->shouldReceive('validateChannelOwnership')
            ->with($profile)
            ->andReturn(false);

        $response = $this->controller->request($profile);

        $this->assertTrue($response->isRedirect());
        
        $profile->refresh();
        $this->assertEquals('pending', $profile->verification_status);
    }

    /** @test */
    public function request_method_handles_exceptions_gracefully()
    {
        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'is_approved' => true,
            'verification_status' => 'pending',
        ]);

        Auth::shouldReceive('id')->andReturn($user->id);
        $this->mockPlatformApiService
            ->shouldReceive('validateChannelOwnership')
            ->with($profile)
            ->andThrow(new \Exception('API Error'));

        $response = $this->controller->request($profile);

        $this->assertTrue($response->isRedirect());
        
        $profile->refresh();
        $this->assertEquals('pending', $profile->verification_status);
    }

    /** @test */
    public function request_method_aborts_for_non_owner()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $profile = StreamerProfile::factory()->create(['user_id' => $otherUser->id]);

        Auth::shouldReceive('id')->andReturn($user->id);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('You can only request verification for your own profile.');

        $this->controller->request($profile);
    }

    /** @test */
    public function cancel_method_successfully_cancels_verification_request()
    {
        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'verification_status' => 'requested',
            'verification_token' => 'test-token',
            'verification_requested_at' => now(),
        ]);

        Auth::shouldReceive('id')->andReturn($user->id);

        $response = $this->controller->cancel($profile);

        $this->assertTrue($response->isRedirect());
        
        $profile->refresh();
        $this->assertEquals('pending', $profile->verification_status);
        $this->assertNull($profile->verification_token);
        $this->assertNull($profile->verification_requested_at);
    }

    /** @test */
    public function cancel_method_fails_for_non_requested_status()
    {
        $user = User::factory()->create();
        $profile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'verification_status' => 'in_review', // Cannot cancel when in review
        ]);

        Auth::shouldReceive('id')->andReturn($user->id);

        $response = $this->controller->cancel($profile);

        $this->assertTrue($response->isRedirect());
        
        $profile->refresh();
        $this->assertEquals('in_review', $profile->verification_status);
    }

    /** @test */
    public function cancel_method_aborts_for_non_owner()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $profile = StreamerProfile::factory()->create(['user_id' => $otherUser->id]);

        Auth::shouldReceive('id')->andReturn($user->id);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('You can only cancel your own verification request.');

        $this->controller->cancel($profile);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}