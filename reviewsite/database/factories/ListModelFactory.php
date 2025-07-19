<?php

namespace Database\Factories;

use App\Models\ListModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ListModelFactory extends Factory
{
    protected $model = ListModel::class;

    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        
        return [
            'user_id' => User::factory(),
            'name' => $name,
            'description' => $this->faker->sentence(),
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->randomNumber(4),
            'is_public' => $this->faker->boolean(70), // 70% chance of being public
            'category' => $this->faker->randomElement(array_keys(ListModel::$categories)),
            'sort_by' => $this->faker->randomElement(['date_added', 'name', 'rating']),
            'sort_direction' => $this->faker->randomElement(['asc', 'desc']),
            'allow_collaboration' => $this->faker->boolean(30), // 30% chance
            'allow_comments' => $this->faker->boolean(80), // 80% chance
            'followers_count' => $this->faker->numberBetween(0, 100),
            'comments_count' => $this->faker->numberBetween(0, 50),
        ];
    }

    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => true,
        ]);
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => false,
        ]);
    }
}