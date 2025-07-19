<?php

namespace Tests\Unit\Models;

use App\Models\Review;
use App\Models\StreamerProfile;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewStreamerTest extends TestCase
{
    use RefreshDatabase;

    public function test_review_belongs_to_streamer_profile()
    {
        $streamerProfile = StreamerProfile::factory()->create();
        $review = Review::factory()->create(['streamer_profile_id' => $streamerProfile->id]);

        $this->assertInstanceOf(StreamerProfile::class, $review->streamerProfile);
        $this->assertEquals($streamerProfile->id, $review->streamerProfile->id);
    }

    public function test_is_streamer_review_returns_true_when_streamer_profile_id_exists()
    {
        $streamerProfile = StreamerProfile::factory()->create();
        $streamerReview = Review::factory()->create(['streamer_profile_id' => $streamerProfile->id]);
        $regularReview = Review::factory()->create(['streamer_profile_id' => null]);

        $this->assertTrue($streamerReview->isStreamerReview());
        $this->assertFalse($regularReview->isStreamerReview());
    }

    public function test_streamer_scope_filters_streamer_reviews()
    {
        $streamerProfile = StreamerProfile::factory()->create();
        Review::factory()->create(['streamer_profile_id' => $streamerProfile->id]);
        Review::factory()->create(['streamer_profile_id' => $streamerProfile->id]);
        Review::factory()->create(['streamer_profile_id' => null]);

        $streamerReviews = Review::streamer()->get();

        $this->assertCount(2, $streamerReviews);
        $this->assertTrue($streamerReviews->every(fn($review) => $review->streamer_profile_id !== null));
    }

    public function test_not_streamer_scope_filters_non_streamer_reviews()
    {
        $streamerProfile = StreamerProfile::factory()->create();
        Review::factory()->create(['streamer_profile_id' => $streamerProfile->id]);
        Review::factory()->create(['streamer_profile_id' => null]);
        Review::factory()->create(['streamer_profile_id' => null]);

        $nonStreamerReviews = Review::notStreamer()->get();

        $this->assertCount(2, $nonStreamerReviews);
        $this->assertTrue($nonStreamerReviews->every(fn($review) => $review->streamer_profile_id === null));
    }

    public function test_get_author_display_name_shows_dual_identity_for_streamer_reviews()
    {
        $user = User::factory()->create(['name' => 'John Doe']);
        $streamerProfile = StreamerProfile::factory()->create([
            'user_id' => $user->id,
            'channel_name' => 'JohnGaming'
        ]);
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'streamer_profile_id' => $streamerProfile->id
        ]);

        $this->assertEquals('John Doe (JohnGaming)', $review->author_display_name);
    }

    public function test_get_author_display_name_shows_regular_name_for_non_streamer_reviews()
    {
        $user = User::factory()->create(['name' => 'Jane Smith']);
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'streamer_profile_id' => null
        ]);

        $this->assertEquals('Jane Smith', $review->author_display_name);
    }

    public function test_get_review_type_returns_streamer_for_streamer_reviews()
    {
        $streamerProfile = StreamerProfile::factory()->create();
        $streamerReview = Review::factory()->create(['streamer_profile_id' => $streamerProfile->id]);
        $regularReview = Review::factory()->create(['streamer_profile_id' => null]);

        $this->assertEquals('streamer', $streamerReview->review_type);
        $this->assertEquals('user', $regularReview->review_type);
    }

    public function test_get_review_context_includes_streamer_profile_for_streamer_reviews()
    {
        $streamerProfile = StreamerProfile::factory()->create();
        $streamerReview = Review::factory()->create(['streamer_profile_id' => $streamerProfile->id]);
        $regularReview = Review::factory()->create(['streamer_profile_id' => null]);

        $streamerContext = $streamerReview->review_context;
        $regularContext = $regularReview->review_context;

        $this->assertEquals('streamer', $streamerContext['type']);
        $this->assertInstanceOf(StreamerProfile::class, $streamerContext['streamer_profile']);
        $this->assertEquals($streamerProfile->id, $streamerContext['streamer_profile']->id);

        $this->assertEquals('user', $regularContext['type']);
        $this->assertArrayNotHasKey('streamer_profile', $regularContext);
    }

    public function test_streamer_profile_id_is_fillable()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $streamerProfile = StreamerProfile::factory()->create();

        $review = Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'streamer_profile_id' => $streamerProfile->id,
            'title' => 'Test Review',
            'content' => 'This is a test review',
            'rating' => 5,
            'is_published' => true,
        ]);

        $this->assertEquals($streamerProfile->id, $review->streamer_profile_id);
    }
}