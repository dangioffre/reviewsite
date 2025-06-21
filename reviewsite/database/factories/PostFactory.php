<?php

namespace Database\Factories;

use App\Models\Post;
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
        $type = $this->faker->randomElement(['Article', 'Review']);
        
        return [
            'title' => $this->faker->sentence(4),
            'content' => $this->faker->paragraphs(3, true),
            'excerpt' => $this->faker->paragraph(2),
            'author' => 'Dan & Brian',
            'featured_image' => 'https://via.placeholder.com/1200x600/27272A/FFFFFF?text=' . $type,
            'is_featured' => false,
            'type' => $type,
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
} 