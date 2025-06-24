<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['game', 'hardware']);
        $name = $type === 'game'
            ? $this->faker->randomElement(['Halo Infinite', 'The Legend of Zelda: Tears of the Kingdom', 'Super Mario 64', 'Elden Ring', 'Final Fantasy VII', 'Cyberpunk 2077', 'The Witcher 3', 'God of War', 'Spider-Man 2', 'Baldur\'s Gate 3'])
            : $this->faker->randomElement(['Xbox Series X', 'PlayStation 5', 'Nintendo Switch', 'NVIDIA RTX 4090', 'Steam Deck']);
        return [
            'name' => $name,
            'type' => $type,
            'description' => $this->faker->paragraph(3),
            'story' => $this->faker->paragraphs(3, true),
            'image' => 'https://via.placeholder.com/600x400/27272A/FFFFFF?text=' . urlencode($name),
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'release_date' => $this->faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'genre_id' => null, // Will be set by seeder
            'platform_id' => null, // Will be set by seeder
        ];
    }
} 