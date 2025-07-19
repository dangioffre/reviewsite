<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "Testing regular user login...\n";

$user = User::where('email', 'user@test.com')->first();

if ($user) {
    echo "User found: " . $user->name . "\n";
    echo "Admin status: " . ($user->is_admin ? 'Yes' : 'No') . "\n";
    echo "Password check: " . (Hash::check('password', $user->password) ? 'Valid' : 'Invalid') . "\n";
    
    // Test with uppercase password
    echo "Password check (uppercase): " . (Hash::check('Password', $user->password) ? 'Valid' : 'Invalid') . "\n";
    
    // Test with different variations
    echo "Password check (with spaces): " . (Hash::check(' password ', $user->password) ? 'Valid' : 'Invalid') . "\n";
} else {
    echo "User not found\n";
}

echo "\nAll regular users (non-admin):\n";
$regularUsers = User::where('is_admin', false)->get();
foreach ($regularUsers as $regularUser) {
    echo "- " . $regularUser->name . " (" . $regularUser->email . ")\n";
} 