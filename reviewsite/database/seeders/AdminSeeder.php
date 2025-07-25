<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Review;
use App\Models\Product;
use App\Models\Podcast;
use App\Models\Episode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Password'),
                'is_admin' => true,
            ]
        );

        // Create or update admin's podcast
        $podcast = Podcast::updateOrCreate(
            ['slug' => 'admin-gaming-podcast'],
            [
                'owner_id' => $admin->id,
                'name' => 'Admin Gaming Podcast',
                'description' => 'The official gaming podcast from the site admins. We discuss the latest games, industry news, and share our thoughts on everything gaming related.',
                'rss_url' => 'https://example.com/feed.xml',
                'logo_url' => 'https://picsum.photos/300/300?random=1',
                'website_url' => 'https://example.com/podcast',
                'hosts' => ['Admin', 'Co-Host'],
                'links' => [
                    'website' => 'https://example.com/podcast',
                    'twitter' => 'https://twitter.com/admingamingpod',
                    'youtube' => 'https://youtube.com/@admingamingpod',
                    'discord' => 'https://discord.gg/admingaming',
                ],
                'status' => 'approved',
                'verification_token' => Str::random(32),
                'verification_status' => true,
                'approved_at' => now(),
                'approved_by' => $admin->id,
                'is_featured' => true,
            ]
        );

        // Create 5 episodes for admin's podcast (only if they don't exist)
        if ($podcast->episodes()->count() === 0) {
            $episodeTitles = [
                'Episode 1: Welcome to Our Gaming Podcast',
                'Episode 2: Top Games of 2024',
                'Episode 3: Indie Game Spotlight',
                'Episode 4: Console Wars Discussion',
                'Episode 5: Gaming Industry News Roundup',
            ];

            $episodeDescriptions = [
                'In our first episode, we introduce ourselves and discuss what listeners can expect from this podcast. We also share our gaming backgrounds and current favorites.',
                'We dive deep into the best games released in 2024 so far, discussing what makes them special and why they deserve your attention.',
                'This week we shine a spotlight on some amazing indie games that might have flown under your radar. These hidden gems are worth checking out!',
                'We tackle the age-old debate: PlayStation vs Xbox vs Nintendo. Which console ecosystem is winning in 2024?',
                'Catch up on all the latest gaming industry news, from major acquisitions to upcoming releases and everything in between.',
            ];

            foreach (range(1, 5) as $episodeNumber) {
                Episode::create([
                    'podcast_id' => $podcast->id,
                    'title' => $episodeTitles[$episodeNumber - 1],
                    'slug' => 'episode-' . $episodeNumber,
                    'description' => $episodeDescriptions[$episodeNumber - 1],
                    'show_notes' => 'Show notes for episode ' . $episodeNumber . '. Links and timestamps will be added here.',
                    'published_at' => now()->subDays(30 - ($episodeNumber * 5)),
                    'audio_url' => 'https://example.com/audio/episode-' . $episodeNumber . '.mp3',
                    'artwork_url' => 'https://picsum.photos/400/400?random=' . ($episodeNumber + 10),
                    'duration' => rand(1800, 3600), // 30-60 minutes in seconds
                    'episode_number' => $episodeNumber,
                    'season_number' => 1,
                    'episode_type' => 'full',
                    'is_explicit' => false,
                    'tags' => ['gaming', 'podcast', 'episode-' . $episodeNumber],
                ]);
            }
        }

        // Create additional reviews for admin (only if they don't already have many)
        $existingReviewCount = $admin->reviews()->count();
        if ($existingReviewCount < 10) {
            $products = Product::inRandomOrder()->take(15 - $existingReviewCount)->get();
            
            if ($products->count() < 15 - $existingReviewCount) {
                $this->command->warn('Not enough products found. Creating some sample reviews anyway.');
                $products = Product::take(5)->get();
            }

            $reviewTemplates = [
                [
                    'rating' => 10,
                    'content' => "Absolutely outstanding! This is exactly what I was looking for. The quality is exceptional and the experience is unmatched. This sets a new standard for the genre.",
                ],
                [
                    'rating' => 9,
                    'content' => "Excellent work! This delivers on every promise and then some. Minor issues are easily overlooked given the overall quality. Highly recommended!",
                ],
                [
                    'rating' => 8,
                    'content' => "Very solid product. Does what it's supposed to do very well. Some room for improvement but overall a great experience worth your time and money.",
                ],
                [
                    'rating' => 7,
                    'content' => "Good quality with some notable strengths. A few areas could use refinement but the core experience is enjoyable. Worth checking out.",
                ],
                [
                    'rating' => 6,
                    'content' => "Decent enough but has some issues that hold it back. Not bad but could be better with more polish and attention to detail.",
                ],
            ];

            foreach ($products as $product) {
                // Skip if admin already reviewed this product
                if ($admin->reviews()->where('product_id', $product->id)->exists()) {
                    continue;
                }

                $template = $reviewTemplates[array_rand($reviewTemplates)];
                
                Review::create([
                    'user_id' => $admin->id,
                    'product_id' => $product->id,
                    'rating' => $template['rating'],
                    'title' => 'Admin Review: ' . $product->name,
                    'content' => $template['content'] . ' As an admin, I\'ve seen many products in this category, and this one stands out for its quality and execution.',
                    'is_approved' => true,
                    'approved_at' => now(),
                    'approved_by' => $admin->id,
                    'is_featured' => rand(0, 1),
                    'created_at' => now()->subDays(rand(1, 90)),
                    'updated_at' => now()->subDays(rand(1, 90)),
                ]);
            }
        }

        $this->command->info('Admin user updated with podcast (5 episodes) and additional reviews!');
    }
} 