<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure Admin Role exists
        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            $adminRole = Role::create([
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full access to all system features',
                'permissions' => [
                    'user_management',
                    'role_management',
                    'product_management',
                    'category_management',
                    'blog_management',
                    'page_management',
                    'file_upload',
                    'system_settings'
                ],
                'page_access' => [
                    'dashboard',
                    'users',
                    'roles',
                    'categories',
                    'products',
                    'blogs',
                    'pages',
                    'settings'
                ],
                'status' => true
            ]);
            $this->command->info('Admin Role created.');
        }

        // Create Admin User
        $user = User::where('email', 'admin@thecodeforge.in')->first();

        if (!$user) {
            User::create([
                'name' => 'Admin CodeForge',
                'email' => 'admin@thecodeforge.in',
                'password' => 'Codeforge@110092', // Model mutator will hash this
                'role_id' => $adminRole->_id,
                'status' => true,
                'phone' => '',
                'address' => ''
            ]);
            $this->command->info('User admin@thecodeforge.in created successfully.');
        } else {
            $user->update([
                'password' => 'Codeforge@110092', // Model mutator will hash this
                'role_id' => $adminRole->_id,
                'status' => true,
            ]);
            $this->command->info('User admin@thecodeforge.in updated successfully.');
        }
    }
}
