<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\StreamerProfile;
use App\Models\Product;
use App\Models\Review;
use App\Models\ListModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StreamerDiscoveryAndSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->user = User::factory()->create();
        $this->streamer = StreamerProfile::factory()->create([
            'channel_name' => 'TestStreamer',
            'platform' => 'twitch',
            'bio' => 'A test streamer for gaming content',
            'is_approved' => true,
            'is_verified' => true,
        ]);
        
        $this->game = Product::factory()->create([
            'name' => 'Test Game',
            'type' => 'game',
            'description' => 'A test game for reviews'
        ]);
        
        $this->review = Review::factory()->create([
            'user_id' => $this->streamer->user_id,
            'streamer_profile_id' => $this->streamer->id,
            'product_id' => $this->game->id,
            'title' => 'Great Game Review',
            'content' => 'This is a detailed review of the test game',
            'is_published' => true,
        ]);
        
        $this->list = ListModel::factory()->create([
            'name' => 'Test Gaming List',
            'description' => 'A curated list of great games',
            'is_public' => true,
        ]);
    }

    /** @test */
    public function it_displays_streamer_profiles_index_page()
    {
        $response = $this->get(route('streamer.profiles.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Discover Streamers');
        $response->assertSee($this->streamer->channel_name);
        $response->assertSee(ucfirst($this->streamer->platform));
    }

    /** @test */
    public function it_filters_streamers_by_platform()
    {
        // Create streamers on different platforms
        $twitchStreamer = StreamerProfile::factory()->create([
            'platform' => 'twitch',
            'channel_name' => 'TwitchStreamer',
            'is_approved' => true,
        ]);
        
        $youtubeStreamer = StreamerProfile::factory()->create([
            'platform' => 'youtube',
            'channel_name' => 'YouTubeStreamer',
            'is_approved' => true,
        ]);

        // Filter by Twitch
        $response = $this->get(route('streamer.profiles.index', ['platform' => 'twitch']));
        $response->assertStatus(200);
        $response->assertSee('TwitchStreamer');
        $response->assertDontSee('YouTubeStreamer');

        // Filter by YouTube
        $response = $this->get(route('streamer.profiles.index', ['platform' => 'youtube']));
        $response->assertStatus(200);
        $response->assertSee('YouTubeStreamer');
        $response->assertDontSee('TwitchStreamer');
    }

    /** @test */
    public function it_filters_streamers_by_live_status()
    {
        // Create live and offline streamers
        $liveStreamer = StreamerProfile::factory()->create([
            'channel_name' => 'LiveStreamer',
            'is_live' => true,
            'is_approved' => true,
        ]);
        
        $offlineStreamer = StreamerProfile::factory()->create([
            'channel_name' => 'OfflineStreamer',
            'is_live' => false,
            'is_approved' => true,
        ]);

        // Filter by live status
        $response = $this->get(route('streamer.profiles.index', ['live_status' => 'live']));
        $response->assertStatus(200);
        $response->assertSee('LiveStreamer');
        $response->assertDontSee('OfflineStreamer');

        // Filter by offline status
        $response = $this->get(route('streamer.profiles.index', ['live_status' => 'offline']));
        $response->assertStatus(200);
        $response->assertSee('OfflineStreamer');
        $response->assertDontSee('LiveStreamer');
    }

    /** @test */
    public function it_searches_streamers_by_name_and_bio()
    {
        $response = $this->get(route('streamer.profiles.index', ['search' => 'TestStreamer']));
        
        $response->assertStatus(200);
        $response->assertSee($this->streamer->channel_name);

        // Search by bio content
        $response = $this->get(route('streamer.profiles.index', ['search' => 'gaming content']));
        $response->assertStatus(200);
        $response->assertSee($this->streamer->channel_name);
    }

    /** @test */
    public function it_sorts_streamers_correctly()
    {
        $streamerA = StreamerProfile::factory()->create([
            'channel_name' => 'AStreamer',
            'is_approved' => true,
        ]);
        
        $streamerZ = StreamerProfile::factory()->create([
            'channel_name' => 'ZStreamer',
            'is_approved' => true,
        ]);

        // Sort by name ascending
        $response = $this->get(route('streamer.profiles.index', ['sort' => 'name', 'direction' => 'asc']));
        $response->assertStatus(200);
        
        // Check that AStreamer appears before ZStreamer in the response
        $content = $response->getContent();
        $posA = strpos($content, 'AStreamer');
        $posZ = strpos($content, 'ZStreamer');
        $this->assertLessThan($posZ, $posA);
    }

    /** @test */
    public function it_provides_streamer_recommendations_for_authenticated_users()
    {
        $this->actingAs($this->user);
        
        $response = $this->get(route('streamer.profiles.recommendations'));
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'recommendations' => [
                '*' => [
                    'id',
                    'channel_name',
                    'platform',
                    'bio',
                    'profile_photo_url',
                    'is_live',
                    'is_verified',
                    'followers_count',
                    'url'
                ]
            ]
        ]);
    }

    /** @test */
    public function it_displays_main_search_page()
    {
        $response = $this->get(route('search.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Search');
        $response->assertSee('Search games, streamers, reviews, and more...');
    }

    /** @test */
    public function it_searches_across_all_categories()
    {
        $response = $this->get(route('search.index', ['q' => 'test']));
        
        $response->assertStatus(200);
        $response->assertSee('Search Results for "test"');
        
        // Should find the game
        $response->assertSee($this->game->name);
        
        // Should find the streamer
        $response->assertSee($this->streamer->channel_name);
        
        // Should find the review
        $response->assertSee($this->review->title);
        
        // Should find the list
        $response->assertSee($this->list->name);
    }

    /** @test */
    public function it_filters_search_by_category()
    {
        // Search only in streamers category
        $response = $this->get(route('search.index', ['q' => 'test', 'category' => 'streamers']));
        
        $response->assertStatus(200);
        $response->assertSee($this->streamer->channel_name);
        $response->assertDontSee($this->game->name);
        
        // Search only in games category
        $response = $this->get(route('search.index', ['q' => 'test', 'category' => 'games']));
        
        $response->assertStatus(200);
        $response->assertSee($this->game->name);
        $response->assertDontSee($this->streamer->channel_name);
    }

    /** @test */
    public function it_provides_search_suggestions()
    {
        $response = $this->get(route('api.search.suggestions', ['q' => 'test']));
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'title',
                'category',
                'url',
                'type'
            ]
        ]);
        
        $suggestions = $response->json();
        $this->assertNotEmpty($suggestions);
        
        // Check that we get suggestions from different categories
        $categories = array_column($suggestions, 'category');
        $this->assertContains('Games', $categories);
        $this->assertContains('Streamers', $categories);
    }

    /** @test */
    public function it_shows_streamer_reviews_in_search_results()
    {
        $response = $this->get(route('search.index', ['q' => 'great', 'category' => 'reviews']));
        
        $response->assertStatus(200);
        $response->assertSee($this->review->title);
        $response->assertSee($this->streamer->channel_name);
        $response->assertSee($this->game->name);
    }

    /** @test */
    public function it_displays_live_status_in_search_results()
    {
        // Set streamer as live
        $this->streamer->update(['is_live' => true]);
        
        $response = $this->get(route('search.index', ['q' => 'test', 'category' => 'streamers']));
        
        $response->assertStatus(200);
        $response->assertSee('LIVE');
    }

    /** @test */
    public function it_displays_verification_status_in_search_results()
    {
        $response = $this->get(route('search.index', ['q' => 'test', 'category' => 'streamers']));
        
        $response->assertStatus(200);
        $response->assertSee('Verified');
    }

    /** @test */
    public function it_handles_empty_search_queries()
    {
        $response = $this->get(route('search.index', ['q' => '']));
        
        $response->assertStatus(200);
        $response->assertSee('Search Everything');
        $response->assertSee('Find games, tech products, streamers, reviews, and lists');
    }

    /** @test */
    public function it_handles_no_search_results()
    {
        $response = $this->get(route('search.index', ['q' => 'nonexistentquery123']));
        
        $response->assertStatus(200);
        $response->assertSee('No results found');
        $response->assertSee('find anything matching');
    }

    /** @test */
    public function it_only_shows_approved_streamers_in_search()
    {
        // Create an unapproved streamer
        $unapprovedStreamer = StreamerProfile::factory()->create([
            'channel_name' => 'UnapprovedStreamer',
            'is_approved' => false,
        ]);
        
        $response = $this->get(route('search.index', ['q' => 'unapproved']));
        
        $response->assertStatus(200);
        $response->assertDontSee('UnapprovedStreamer');
    }

    /** @test */
    public function it_only_shows_published_reviews_in_search()
    {
        // Create an unpublished review
        $unpublishedReview = Review::factory()->create([
            'user_id' => $this->streamer->user_id,
            'streamer_profile_id' => $this->streamer->id,
            'product_id' => $this->game->id,
            'title' => 'Unpublished Review',
            'is_published' => false,
        ]);
        
        $response = $this->get(route('search.index', ['q' => 'unpublished']));
        
        $response->assertStatus(200);
        $response->assertDontSee('Unpublished Review');
    }

    /** @test */
    public function it_only_shows_public_lists_in_search()
    {
        // Create a private list
        $privateList = ListModel::factory()->create([
            'name' => 'Private Gaming List',
            'is_public' => false,
        ]);
        
        $response = $this->get(route('search.index', ['q' => 'private']));
        
        $response->assertStatus(200);
        $response->assertDontSee('Private Gaming List');
    }

    /** @test */
    public function search_suggestions_include_live_status()
    {
        $this->streamer->update(['is_live' => true]);
        
        $response = $this->get(route('api.search.suggestions', ['q' => 'test']));
        
        $response->assertStatus(200);
        $suggestions = $response->json();
        
        $streamerSuggestion = collect($suggestions)->firstWhere('type', 'streamer');
        $this->assertNotNull($streamerSuggestion);
        $this->assertTrue($streamerSuggestion['is_live']);
    }

    /** @test */
    public function search_suggestions_include_platform_info()
    {
        $response = $this->get(route('api.search.suggestions', ['q' => 'test']));
        
        $response->assertStatus(200);
        $suggestions = $response->json();
        
        $streamerSuggestion = collect($suggestions)->firstWhere('type', 'streamer');
        $this->assertNotNull($streamerSuggestion);
        $this->assertEquals('Twitch', $streamerSuggestion['platform']);
    }

    /** @test */
    public function search_suggestions_limit_results()
    {
        // Create many streamers and games
        StreamerProfile::factory()->count(10)->create(['is_approved' => true]);
        Product::factory()->count(10)->create(['type' => 'game']);
        
        $response = $this->get(route('api.search.suggestions', ['q' => 'test']));
        
        $response->assertStatus(200);
        $suggestions = $response->json();
        
        // Should limit to 6 suggestions total
        $this->assertLessThanOrEqual(6, count($suggestions));
    }
}