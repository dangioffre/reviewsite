<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegularUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test user for regular user testing
        $testUser = User::create([
            'name' => 'John Doe',
            'email' => 'user@test.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        // Create a few more regular users for variety
        $users = [
            [
                'name' => 'Sarah Wilson',
                'email' => 'sarah@test.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike@test.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
            ],
            [
                'name' => 'Emily Chen',
                'email' => 'emily@test.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
            ],
            [
                'name' => 'David Rodriguez',
                'email' => 'david@test.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
            ],
        ];

        foreach ($users as $userData) {
            User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'email_verified_at' => now(),
                'password' => $userData['password'],
                'is_admin' => $userData['is_admin'],
            ]);
        }

        $this->command->info('Created regular user accounts:');
        $this->command->info('- Primary test user: user@test.com / password');
        $this->command->info('- Additional users: sarah@test.com, mike@test.com, emily@test.com, david@test.com');
        $this->command->info('- All passwords: password');
    }
}
