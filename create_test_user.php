<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Create test user
$user = App\Models\User::firstOrCreate(
    ['email' => 'test@example.com'],
    [
        'password' => bcrypt('password123'),
    ]
);

echo "User created/found: " . $user->email . "\n";
echo "Password: password123\n";
