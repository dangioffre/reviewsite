<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Review;
use App\Models\StreamerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StreamerReviewDualIdentityTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $product;
    protected $streamerProfile;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create(['type' => 'game']);
        $this->streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $this->user->id,
            'is_approved' => true,
            'is_verified' => true,
        ]);
    }

    /** @test */
    public function authenticated_streamer_can_create_review_with_streamer_identity()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('games.reviews.store', $this->product), [
            'title' => 'Great game as a streamer',
            'content' => 'This game is fantastic for streaming. Great viewer engagement.',
            'rating' => 9,
            'streamer_profile_id' => $this->streamerProfile->id,
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('reviews', [
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'streamer_profile_id' => $this->streamerProfile->id,
            'title' => 'Great game as a streamer',
            'rating' => 9,
        ]);
    }

    /** @test */
    public function streamer_review_displays_dual_identity_format()
    {
        $review = Review::factory()->create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'streamer_profile_id' => $this->streamerProfile->id,
            'title' => 'Streamer Review Test',
            'content' => 'Test content',
            'rating' => 8,
            'is_published' => true,
        ]);

        $this->assertEquals(
            $this->user->name . ' (' . $this->streamerProfile->channel_name . ')',
            $review->getAuthorDisplayNameAttribute()
        );
    }

    /** @test */
    public function streamer_reviews_appear_in_separate_section_on_game_page()
    {
        // Create a regular user review
        $regularReview = Review::factory()->create([
            'product_id' => $this->product->id,
            'user_id' => User::factory()->create()->id,
            'title' => 'Regular Review',
            'content' => 'Regular content',
            'rating' => 7,
            'is_published' => true,
        ]);

        // Create a streamer review
        $streamerReview = Review::factory()->create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'streamer_profile_id' => $this->streamerProfile->id,
            'title' => 'Streamer Review',
            'content' => 'Streamer content',
            'rating' => 9,
            'is_published' => true,
        ]);

        $response = $this->get(route('games.show', $this->product));

        $response->assertStatus(200);
        $response->assertSee('Reviews by Streamers');
        $response->assertSee('Community Reviews');
        $response->assertSee($streamerReview->title);
        $response->assertSee($regularReview->title);
    }

    /** @test */
    public function streamer_review_appears_on_streamer_profile_page()
    {
        $review = Review::factory()->create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'streamer_profile_id' => $this->streamerProfile->id,
            'title' => 'Profile Review Test',
            'content' => 'Profile content test',
            'rating' => 8,
            'is_published' => true,
        ]);

        $response = $this->get(route('streamer.profile.show', $this->streamerProfile));

        $response->assertStatus(200);
        $response->assertSee($review->title);
        $response->assertSee($this->product->name);
    }

    /** @test */
    public function user_cannot_post_review_as_unverified_streamer_profile()
    {
        $unverifiedProfile = StreamerProfile::factory()->create([
            'user_id' => $this->user->id,
            'is_approved' => false,
            'is_verified' => false,
        ]);

        $this->actingAs($this->user);

        $response = $this->post(route('games.reviews.store', $this->product), [
            'title' => 'Test Review',
            'content' => 'Test content',
            'rating' => 8,
            'streamer_profile_id' => $unverifiedProfile->id,
        ]);

        $response->assertSessionHasErrors('streamer_profile_id');
    }

    /** @test */
    public function user_cannot_post_review_as_another_users_streamer_profile()
    {
        $otherUser = User::factory()->create();
        $otherProfile = StreamerProfile::factory()->create([
            'user_id' => $otherUser->id,
            'is_approved' => true,
            'is_verified' => true,
        ]);

        $this->actingAs($this->user);

        $response = $this->post(route('games.reviews.store', $this->product), [
            'title' => 'Test Review',
            'content' => 'Test content',
            'rating' => 8,
            'streamer_profile_id' => $otherProfile->id,
        ]);

        $response->assertSessionHasErrors('streamer_profile_id');
    }

    /** @test */
    public function streamer_can_edit_review_and_change_identity()
    {
        $review = Review::factory()->create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'streamer_profile_id' => null, // Start as regular review
            'title' => 'Original Review',
            'content' => 'Original content',
            'rating' => 7,
            'is_published' => true,
        ]);

        $this->actingAs($this->user);

        $response = $this->put(route('games.reviews.update', [$this->product, $review]), [
            'title' => 'Updated Review',
            'content' => 'Updated content',
            'rating' => 9,
            'streamer_profile_id' => $this->streamerProfile->id, // Change to streamer identity
        ]);

        $response->assertRedirect();
        
        $review->refresh();
        $this->assertEquals($this->streamerProfile->id, $review->streamer_profile_id);
        $this->assertEquals('Updated Review', $review->title);
    }

    /** @test */
    public function review_model_correctly_identifies_streamer_reviews()
    {
        $regularReview = Review::factory()->create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'streamer_profile_id' => null,
        ]);

        $streamerReview = Review::factory()->create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'streamer_profile_id' => $this->streamerProfile->id,
        ]);

        $this->assertFalse($regularReview->isStreamerReview());
        $this->assertTrue($streamerReview->isStreamerReview());
        $this->assertEquals('user', $regularReview->getReviewTypeAttribute());
        $this->assertEquals('streamer', $streamerReview->getReviewTypeAttribute());
    }

    /** @test */
    public function game_controller_separates_streamer_and_user_reviews()
    {
        // Create different types of reviews
        $staffReview = Review::factory()->create([
            'product_id' => $this->product->id,
            'user_id' => User::factory()->create(['is_admin' => true])->id,
            'is_staff_review' => true,
            'is_published' => true,
        ]);

        $streamerReview = Review::factory()->create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'streamer_profile_id' => $this->streamerProfile->id,
            'is_staff_review' => false,
            'is_published' => true,
        ]);

        $userReview = Review::factory()->create([
            'product_id' => $this->product->id,
            'user_id' => User::factory()->create()->id,
            'streamer_profile_id' => null,
            'is_staff_review' => false,
            'is_published' => true,
        ]);

        $response = $this->get(route('games.show', $this->product));
        
        $response->assertStatus(200);
        
        // Check that the view receives the separated review collections
        $response->assertViewHas('staffReviews');
        $response->assertViewHas('streamerReviews');
        $response->assertViewHas('userReviews');
        
        $staffReviews = $response->viewData('staffReviews');
        $streamerReviews = $response->viewData('streamerReviews');
        $userReviews = $response->viewData('userReviews');
        
        $this->assertCount(1, $staffReviews);
        $this->assertCount(1, $streamerReviews);
        $this->assertCount(1, $userReviews);
        
        $this->assertEquals($staffReview->id, $staffReviews->first()->id);
        $this->assertEquals($streamerReview->id, $streamerReviews->first()->id);
        $this->assertEquals($userReview->id, $userReviews->first()->id);
    }
}