<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;

class StaffReviewSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get all staff users and products
        $staffUsers = User::where('is_admin', true)->get();
        $products = Product::all();

        if ($staffUsers->isEmpty()) {
            $this->command->error('No staff users found. Please run UserSeeder first.');
            return;
        }

        if ($products->isEmpty()) {
            $this->command->error('No products found. Please seed products first.');
            return;
        }

        // Professional gaming review templates
        $reviewTemplates = [
            // Positive Reviews (8-10 stars)
            [
                'rating' => 10,
                'templates' => [
                    "This is absolutely phenomenal - a masterpiece that sets new standards in the industry. The attention to detail is incredible, and every aspect feels meticulously crafted. From the moment you start, you're drawn into an experience that's both technically impressive and emotionally engaging. The gameplay mechanics are intuitive yet deep, offering layers of complexity that reward both casual and hardcore players. This is a must-have that will be remembered as a classic.",
                    "Outstanding achievement in game design and execution. The developers have created something truly special here - combining innovative mechanics with polished presentation. The user experience is seamless, with excellent performance optimization and thoughtful design choices throughout. Every element works in harmony to create an engaging and memorable experience. This sets a new benchmark for quality in the genre.",
                    "Exceptional quality across all aspects. The technical implementation is flawless, with smooth performance and stunning presentation. The design philosophy is evident in every detail, creating a cohesive and immersive experience. The learning curve is perfectly balanced, making it accessible to newcomers while offering depth for experienced users. This is what happens when talented developers are given the time and resources to create something special."
                ]
            ],
            [
                'rating' => 9,
                'templates' => [
                    "Excellent product that delivers on its promises. The core experience is solid and engaging, with high production values evident throughout. While there are minor areas that could be refined, the overall package is impressive. The attention to detail in key areas really shows, and the performance is consistently smooth. This is definitely worth your time and money - a strong recommendation from our team.",
                    "Very impressive work that showcases great design and technical skill. The gameplay feels refined and purposeful, with each element contributing to the overall experience. There are a few small rough edges, but nothing that significantly impacts the enjoyment. The developers clearly understand their audience and have delivered something that hits the mark. A solid addition to any collection.",
                    "Strong execution with clear vision and quality implementation. The experience feels cohesive and well-thought-out, with good pacing and progression. Minor issues don't detract from what is otherwise a very enjoyable and well-crafted product. The technical performance is reliable, and the design choices generally work well together. Definitely recommended for fans of the genre."
                ]
            ],
            [
                'rating' => 8,
                'templates' => [
                    "Good solid experience with several standout moments. The core mechanics work well and there's clear effort put into the presentation. Some aspects could use more polish, but the foundation is strong. Performance is generally good with only occasional minor issues. This offers good value and will appeal to both newcomers and veterans of the genre.",
                    "Well-executed product that delivers a satisfying experience. The design is competent and the technical implementation is mostly solid. While it doesn't break new ground, it does what it sets out to do effectively. There are some areas where improvements could be made, but overall this is a worthwhile addition that fans will appreciate.",
                    "Solid effort with good production values and thoughtful design. The experience is engaging and well-paced, though not without some minor flaws. The developers have created something enjoyable that, while not perfect, offers good entertainment value. Performance is stable and the overall package feels complete and polished."
                ]
            ],
            // Mixed Reviews (5-7 stars)
            [
                'rating' => 7,
                'templates' => [
                    "Decent product with both strengths and weaknesses. The core concept is sound and there are some genuinely good moments, but execution is inconsistent. Some areas feel well-developed while others seem rushed or underdeveloped. Performance is acceptable but could be better optimized. Worth considering if you're interested in the genre, but temper your expectations.",
                    "Mixed bag that shows potential but doesn't quite reach it. There are good ideas here and moments of quality, but also some frustrating design choices and technical issues. The overall experience is adequate but feels like it could have been much better with more development time. Might appeal to dedicated fans but others should wait for updates.",
                    "Average effort that does some things well but struggles in other areas. The foundation is there but the execution feels uneven. Some aspects are polished while others feel incomplete or poorly implemented. Performance varies and there are occasional issues that impact the experience. Has potential but needs more work to reach its goals."
                ]
            ],
            [
                'rating' => 6,
                'templates' => [
                    "Mediocre experience that fails to live up to its potential. While there are some redeeming qualities, the overall package feels underwhelming. Technical issues and design problems hold it back from being something special. The core mechanics work but lack the polish and refinement needed for a truly engaging experience. Only for the most dedicated fans.",
                    "Disappointing result that shows glimpses of what could have been. The concept has merit but the execution is flawed in several key areas. Performance issues and questionable design choices detract from any positive aspects. While not completely broken, it feels like a missed opportunity that needed more development time.",
                    "Below average product that struggles with basic execution. There are occasional bright spots, but they're overshadowed by numerous problems and poor design decisions. Technical performance is inconsistent and the overall experience feels unpolished. Hard to recommend unless you're specifically looking for this type of content."
                ]
            ],
            [
                'rating' => 5,
                'templates' => [
                    "Barely acceptable product with significant issues. While it technically functions, the experience is marred by poor design choices and technical problems. The core concept might have merit, but the execution is severely lacking. Performance is problematic and the overall quality feels rushed. Only consider if you have very specific needs or interests.",
                    "Subpar effort that feels incomplete and poorly executed. Multiple aspects of the design are flawed, and technical issues are frequent. While some elements work as intended, the overall experience is frustrating and unsatisfying. This needed much more development time and attention to detail before release.",
                    "Poor execution that fails to meet basic standards. The experience is plagued by design problems and technical issues that make it difficult to recommend. While there might be some underlying potential, it's buried under layers of poor implementation and lack of polish. Wait for significant updates or look elsewhere."
                ]
            ],
            // Negative Reviews (1-4 stars)
            [
                'rating' => 4,
                'templates' => [
                    "Problematic product with serious flaws that impact the core experience. While it has some functional elements, the numerous issues make it difficult to enjoy. Poor optimization, questionable design decisions, and lack of polish are evident throughout. The potential is there, but the execution falls far short of acceptable standards.",
                    "Deeply flawed experience that struggles with fundamental design and technical issues. Multiple aspects feel broken or poorly implemented, creating frustration rather than enjoyment. The core mechanics are unsound and performance is consistently poor. This needed extensive additional development before release.",
                    "Seriously compromised product that fails to deliver on basic expectations. The experience is marred by poor design choices, technical problems, and lack of quality control. While some elements might work, they're overshadowed by the numerous significant issues. Very difficult to recommend in its current state."
                ]
            ],
            [
                'rating' => 3,
                'templates' => [
                    "Poor quality product with extensive problems across multiple areas. The design is fundamentally flawed and the technical implementation is severely lacking. Basic functionality is compromised and the overall experience is frustrating and unsatisfying. This feels like an unfinished product that was released prematurely.",
                    "Badly executed product that fails to meet minimum standards. The core experience is broken in several key ways, with poor performance and numerous design flaws. While there might be some redeeming elements buried within, they're not enough to salvage the overall package. Avoid unless absolutely necessary.",
                    "Severely flawed product that demonstrates poor development practices. Multiple systems are broken or poorly implemented, creating a frustrating and unreliable experience. The lack of quality control is evident throughout, and basic functionality is compromised. This needed extensive additional work before release."
                ]
            ],
            [
                'rating' => 2,
                'templates' => [
                    "Fundamentally broken product that fails to function properly in most areas. The design is deeply flawed and the technical execution is abysmal. Basic features don't work as intended and the overall experience is more frustrating than enjoyable. This feels like a prototype that was mistakenly released as a finished product.",
                    "Terrible execution with widespread failures across all aspects. The core functionality is compromised, performance is unacceptable, and the design shows complete lack of understanding of user needs. This is an example of how not to develop a product. Avoid at all costs.",
                    "Catastrophically poor product that demonstrates complete failure in development and quality control. Nothing works as it should, and the entire experience is plagued by serious issues. This is not ready for release and should have been scrapped or completely rebuilt. A complete waste of time and resources."
                ]
            ],
            [
                'rating' => 1,
                'templates' => [
                    "Completely broken and unusable product that represents a total failure in development. Nothing functions correctly, and the entire experience is fundamentally compromised. This is not just poor quality - it's actively harmful to the user experience. A complete disaster that should never have been released.",
                    "Absolute disaster of a product that fails on every conceivable level. The design is nonsensical, the technical implementation is catastrophic, and the overall experience is completely unusable. This represents everything wrong with rushed development and lack of quality control. Avoid at all costs - this is genuinely harmful to the medium.",
                    "Total catastrophe that defies explanation. Every aspect is broken, poorly conceived, or completely non-functional. This is not just a bad product - it's an insult to users and a complete waste of everyone's time. The fact that this was released in this state is deeply concerning and represents a complete failure of the development process."
                ]
            ]
        ];

        $createdReviews = 0;
        $maxAttempts = 200; // Prevent infinite loop
        $attempts = 0;

        while ($createdReviews < 50 && $attempts < $maxAttempts) {
            $attempts++;
            
            // Select random staff user and product
            $staffUser = $staffUsers->random();
            $product = $products->random();

            // Check if this staff user already reviewed this product
            $existingReview = Review::where('user_id', $staffUser->id)
                                   ->where('product_id', $product->id)
                                   ->exists();

            if ($existingReview) {
                continue; // Skip if already reviewed
            }

            // Select random rating category with weighted distribution
            // More positive reviews than negative (realistic for curated products)
            $ratingWeights = [
                10 => 15, // 15% chance of 10/10
                9 => 25,  // 25% chance of 9/10
                8 => 30,  // 30% chance of 8/10
                7 => 15,  // 15% chance of 7/10
                6 => 8,   // 8% chance of 6/10
                5 => 4,   // 4% chance of 5/10
                4 => 2,   // 2% chance of 4/10
                3 => 0.5, // 0.5% chance of 3/10
                2 => 0.25, // 0.25% chance of 2/10
                1 => 0.25  // 0.25% chance of 1/10
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

            // Create the review
            Review::create([
                'product_id' => $product->id,
                'user_id' => $staffUser->id,
                'review' => $selectedTemplate,
                'rating' => $selectedRating,
                'is_staff_review' => true,
                'created_at' => now()->subDays(rand(1, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
            ]);

            $createdReviews++;
        }

        $this->command->info("Created {$createdReviews} staff reviews from {$staffUsers->count()} staff members across {$products->count()} products");
    }
}
