<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;

class CommunityReviewSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get all regular users and products
        $regularUsers = User::where('is_admin', false)->get();
        $products = Product::all();

        if ($regularUsers->isEmpty()) {
            $this->command->error('No regular users found. Please run UserSeeder first.');
            return;
        }

        if ($products->isEmpty()) {
            $this->command->error('No products found. Please seed products first.');
            return;
        }

        // Casual community review templates
        $reviewTemplates = [
            // Excellent Reviews (9-10 stars)
            [
                'rating' => 10,
                'templates' => [
                    "OMG this is AMAZING! I've been playing for hours and can't put it down. Everything about this is perfect - the graphics, the gameplay, the sound, everything! This is definitely going to be one of my all-time favorites. 10/10 would recommend to everyone!",
                    "Absolutely blown away by this! The quality is incredible and it's so much fun. I wasn't sure what to expect but this exceeded all my expectations. Worth every penny and then some. This is what gaming should be like!",
                    "Perfect! Just perfect! I've been waiting for something like this for so long and it delivers on every level. The attention to detail is insane and you can tell the developers really cared about making something special. A masterpiece!",
                    "This is why I love gaming! Everything clicks perfectly and it's just pure joy to experience. I've already told all my friends about it and they're all getting it too. This is going to be huge!",
                    "Mind = blown! This is exactly what I wanted and more. The polish is incredible and it runs so smoothly. I'm already planning my second playthrough. This is going straight to my favorites list!"
                ]
            ],
            [
                'rating' => 9,
                'templates' => [
                    "Really really good! Had a blast with this and would definitely recommend it. There are maybe one or two tiny things that could be better but overall this is fantastic. Great job by the developers!",
                    "Excellent stuff! This is what I was hoping for and it delivers. Super fun and well made. A few minor issues here and there but nothing that ruins the experience. Solid 9/10!",
                    "Love it! So much fun and really well done. I've been having a great time with this. There's maybe room for a small improvement or two but honestly this is great as is.",
                    "Amazing work! This is really impressive and I'm enjoying every minute of it. A couple of small things could be tweaked but overall this is fantastic. Highly recommended!",
                    "Outstanding! This exceeded my expectations in almost every way. Just a few tiny rough edges but the core experience is incredible. This is definitely worth your time and money."
                ]
            ],
            // Good Reviews (7-8 stars)
            [
                'rating' => 8,
                'templates' => [
                    "Pretty good! I'm enjoying this quite a bit. There are some things that could be better but overall it's a solid experience. Worth picking up if you're interested in this type of thing.",
                    "Good stuff! Not perfect but definitely enjoyable. Had some fun moments and the quality is decent. A few issues here and there but nothing major. I'd say it's worth it.",
                    "Solid entry! Does what it sets out to do pretty well. There are some areas that could use work but the core is good. If you like this genre you'll probably enjoy it.",
                    "Nice work! This is a good time and well put together. Some minor complaints but overall I'm happy with it. Good value for what you get.",
                    "Enjoyable experience! Has its moments and is generally well made. Not groundbreaking but definitely fun. Would recommend to fans of the genre."
                ]
            ],
            [
                'rating' => 7,
                'templates' => [
                    "It's alright. Has some good points but also some issues. Not bad but not amazing either. Might be worth it if you're really into this sort of thing.",
                    "Decent enough. Does some things well but struggles in other areas. It's okay but I was hoping for a bit more. Still playable though.",
                    "Mixed feelings on this one. Some parts are really good but others feel lacking. It's not bad but it could have been better with more polish.",
                    "It's fine I guess. Nothing special but not terrible either. Has its moments but also some frustrating parts. Average overall.",
                    "Okay experience. Some good ideas but the execution is hit or miss. Worth trying if you're curious but don't expect too much."
                ]
            ],
            // Average Reviews (5-6 stars)
            [
                'rating' => 6,
                'templates' => [
                    "Meh. It's not great but not terrible either. Has some issues that hold it back from being good. Maybe wait for a sale or updates.",
                    "Disappointing. Was expecting more based on the hype. It's functional but feels lacking in several areas. Hard to recommend at full price.",
                    "Below average. Has potential but too many problems to really enjoy. Feels like it needed more development time before release.",
                    "Not impressed. Some decent ideas but poor execution. Technical issues and design problems make it frustrating to play.",
                    "Underwhelming. Expected better quality. It works but feels unfinished and unpolished. Only for dedicated fans maybe."
                ]
            ],
            [
                'rating' => 5,
                'templates' => [
                    "Pretty bad honestly. Lots of issues and poor design choices. Hard to recommend unless you're desperate for this type of content.",
                    "Not good. Multiple problems that make it hard to enjoy. Feels rushed and unfinished. Wait for major updates or skip entirely.",
                    "Disappointing quality. Basic functionality is there but everything feels half-baked. This needed more work before release.",
                    "Poor execution. The concept might be okay but the implementation is severely lacking. Too many problems to overlook.",
                    "Subpar effort. Multiple issues across the board make this a frustrating experience. Only for the most patient players."
                ]
            ],
            // Poor Reviews (1-4 stars)
            [
                'rating' => 4,
                'templates' => [
                    "Pretty broken. Lots of bugs and issues that make it hard to play. Feels like a beta that was released too early. Avoid for now.",
                    "Bad. Multiple serious problems that ruin the experience. This needed a lot more work before being released to the public.",
                    "Broken mess. Too many technical issues and poor design choices. Hard to believe this passed quality control.",
                    "Terrible state. Crashes, bugs, and poor performance throughout. This is not ready for release in its current form."
                ]
            ],
            [
                'rating' => 3,
                'templates' => [
                    "Awful. Barely functional and full of problems. Complete waste of time and money. Avoid at all costs.",
                    "Broken garbage. Nothing works properly and it's completely unenjoyable. How did this get released?",
                    "Complete disaster. Fundamentally broken in multiple ways. This is not a finished product."
                ]
            ],
            [
                'rating' => 2,
                'templates' => [
                    "Completely broken. Doesn't work at all. Total waste of money. Should be removed from sale until fixed.",
                    "Absolute trash. Nothing functions correctly. This is insulting to customers."
                ]
            ],
            [
                'rating' => 1,
                'templates' => [
                    "Worst thing I've ever experienced. Completely unusable. Demanding a refund immediately.",
                    "Total garbage. Doesn't even work. This is a scam."
                ]
            ]
        ];

        $createdReviews = 0;
        $targetReviews = 150; // More community reviews than staff reviews
        $maxAttempts = 500; // Prevent infinite loop
        $attempts = 0;

        while ($createdReviews < $targetReviews && $attempts < $maxAttempts) {
            $attempts++;
            
            // Select random regular user and product
            $regularUser = $regularUsers->random();
            $product = $products->random();

            // Check if this user already reviewed this product
            $existingReview = Review::where('user_id', $regularUser->id)
                                   ->where('product_id', $product->id)
                                   ->exists();

            if ($existingReview) {
                continue; // Skip if already reviewed
            }

            // Community reviews tend to be more varied and emotional
            // Slightly more positive than negative but with more extreme ratings
            $ratingWeights = [
                10 => 20, // 20% chance of 10/10 (users love to give perfect scores)
                9 => 18,  // 18% chance of 9/10
                8 => 22,  // 22% chance of 8/10
                7 => 15,  // 15% chance of 7/10
                6 => 10,  // 10% chance of 6/10
                5 => 8,   // 8% chance of 5/10
                4 => 4,   // 4% chance of 4/10
                3 => 2,   // 2% chance of 3/10
                2 => 0.5, // 0.5% chance of 2/10
                1 => 0.5  // 0.5% chance of 1/10
            ];

            $rand = mt_rand(1, 10000) / 100; // Random percentage
            $cumulativeWeight = 0;
            $selectedRating = 8; // Default fallback

            foreach ($ratingWeights as $rating => $weight) {
                $cumulativeWeight += $weight;
                if ($rand <= $cumulativeWeight) {
                    $selectedRating = $rating;
                    break;
                }
            }

            // Find the appropriate template group
            $selectedTemplate = null;
            foreach ($reviewTemplates as $templateGroup) {
                if ($templateGroup['rating'] == $selectedRating) {
                    $selectedTemplate = $templateGroup['templates'][array_rand($templateGroup['templates'])];
                    break;
                }
            }

            // Create the review with random timestamps over the past 60 days
            Review::create([
                'product_id' => $product->id,
                'user_id' => $regularUser->id,
                'content' => $selectedTemplate,
                'rating' => $selectedRating,
                'is_staff_review' => false,
                'created_at' => now()->subDays(rand(1, 60))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
            ]);

            $createdReviews++;
        }

        $this->command->info("Created {$createdReviews} community reviews from {$regularUsers->count()} regular users across {$products->count()} products");
    }
}
