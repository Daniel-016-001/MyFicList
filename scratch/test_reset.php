<?php

use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Ensure database is in memory or local test db
// Let's create two users
$email1 = 'user1_' . time() . '@example.com';
$email2 = 'user2_' . time() . '@example.com';

$user1 = User::create([
    'name' => 'User One',
    'username' => 'user1_' . time(),
    'email' => $email1,
    'password' => Hash::make('oldpassword'),
]);

$user2 = User::create([
    'name' => 'User Two',
    'username' => 'user2_' . time(),
    'email' => $email2,
    'password' => Hash::make('oldpassword'),
]);

// Create password reset token for User 2
$token = Password::broker()->createToken($user2);
echo "Token created for user 2: " . $token . "\n";

// Attempt to reset User 1's password using User 2's token
$status = Password::broker()->reset(
    [
        'email' => $email1,
        'token' => $token,
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ],
    function ($user, $password) {
        $user->password = Hash::make($password);
        $user->save();
    }
);

echo "Reset status for user 1 with user 2 token: " . $status . "\n";
if ($status === Password::PASSWORD_RESET) {
    echo "SUCCESS: Reset went through!\n";
} else {
    echo "FAILED: Reset blocked (this is correct/expected Laravel behavior)\n";
}

// Clean up
$user1->delete();
$user2->delete();
