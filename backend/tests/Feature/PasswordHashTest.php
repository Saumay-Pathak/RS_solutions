<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PasswordHashTest extends TestCase
{
    // We can't use RefreshDatabase easily with MongoDB if it's not set up for it, 
    // so we'll just create a user and delete it.

    public function test_password_is_hashed_correctly_on_creation()
    {
        $password = 'secret123';
        
        // Simulate User Creation as in UserController
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test_hash@example.com',
            'password' => $password, // Passing plain text
            'status' => true,
        ]);

        // Check if password matches
        $this->assertTrue(Hash::check($password, $user->password), 'Password should match the hash.');
        
        // Clean up
        $user->delete();
    }

    public function test_password_is_hashed_correctly_on_update()
    {
        $password = 'secret123';
        $newPassword = 'newsecret123';
        
        $user = User::create([
            'name' => 'Test User 2',
            'email' => 'test_hash_2@example.com',
            'password' => $password,
            'status' => true,
        ]);

        // Simulate Update as in UserController
        $user->update([
            'password' => $newPassword // Passing plain text
        ]);

        // Check if new password matches
        $this->assertTrue(Hash::check($newPassword, $user->password), 'New password should match the hash.');

        // Clean up
        $user->delete();
    }
}
