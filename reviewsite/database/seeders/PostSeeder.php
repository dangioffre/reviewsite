<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 featured posts
        Post::factory()->count(5)->featured()->create();

        // Create 15 regular posts
        Post::factory()->count(15)->create();
    }
} 