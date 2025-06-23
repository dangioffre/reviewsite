<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['article', 'news', 'announcement']);
        
        return [
            'title' => $this->faker->sentence(4),
            'slug' => $this->faker->slug(),
            'excerpt' => $this->faker->paragraph(2),
            'content' => $this->faker->paragraphs(3, true),
            'image' => 'https://via.placeholder.com/1200x600/27272A/FFFFFF?text=' . ucfirst($type),
            'is_published' => $this->faker->boolean(80), // 80% chance of being published
            'is_featured' => false,
            'type' => $type,
            'published_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'user_id' => User::factory(), // Create a user for each post
        ];
    }

    /**
     * Indicate that the post is featured.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function featured()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_featured' => true,
            ];
        });
    }

    /**
     * Indicate that the post is published.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function published()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_published' => true,
                'published_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            ];
        });
    }
} 