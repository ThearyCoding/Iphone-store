<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin user
        User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin', // add if your users table has "role"
        ]);

        // Normal user
        User::create([
            'name'     => 'John Doe',
            'email'    => 'user@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'user',
        ]);

        // Optional: create multiple fake users
        User::factory()->count(10)->create();

        $this->command->info('✅ Users seeded successfully!');
    }
}
