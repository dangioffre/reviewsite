<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(3, true),
            'rating' => $this->faker->numberBetween(1, 10),
            'positive_points' => $this->faker->optional()->sentences(2),
            'negative_points' => $this->faker->optional()->sentences(2),
            'platform_played_on' => $this->faker->optional()->randomElement(['pc', 'ps5', 'xbox-series-x', 'nintendo-switch']),
            'is_staff_review' => false,
            'is_published' => true,
        ];
    }

    /**
     * Indicate that the review is a staff review.
     */
    public function staff(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_staff_review' => true,
        ]);
    }

    /**
     * Indicate that the review is unpublished.
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }
}