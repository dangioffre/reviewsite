<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Core data first
            RolesAndPermissionsSeeder::class,
            GenrePlatformSeeder::class,
            HardwareSeeder::class,
            
            // Users (both admin and regular)
            AdminSeeder::class,
            UserSeeder::class,
            
            // Content
            PostSeeder::class,
            
            // Reviews (after users and products exist)
            StaffReviewSeeder::class,
            CommunityReviewSeeder::class,
        ]);
    }
}
