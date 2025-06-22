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
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Run existing seeders
        $this->call([
            AdminSeeder::class,
            RolesAndPermissionsSeeder::class,
            GenrePlatformSeeder::class,
            HardwareSeeder::class,
            
            // New relationship seeders
            GameModeSeeder::class,
            DeveloperSeeder::class,
            PublisherSeeder::class,
            ThemeSeeder::class,
            
            // New seeders for testing
            RegularUserSeeder::class,
            GameSeeder::class,
            TechProductSeeder::class,
            
            // Existing content seeders
            PostSeeder::class,
            StaffReviewSeeder::class,
            CommunityReviewSeeder::class,
            UserSeeder::class,
        ]);
    }
}
