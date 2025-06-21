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
            ? $this->faker->randomElement(['Halo Infinite', 'The Legend of Zelda: Ocarina of Time', 'Super Mario 64', 'Elden Ring', 'Final Fantasy VII'])
            : $this->faker->randomElement(['Xbox One', 'PlayStation 5', 'Nintendo Switch', 'NVIDIA RTX 4090', 'Steam Deck']);
        return [
            'name' => $name,
            'type' => $type,
            'description' => $this->faker->paragraph(3),
            'image' => 'https://via.placeholder.com/600x400/27272A/FFFFFF?text=' . urlencode($name),
            'video' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'staff_review' => $this->faker->paragraph(2),
            'staff_rating' => $this->faker->numberBetween(7, 10),
        ];
    }
} 