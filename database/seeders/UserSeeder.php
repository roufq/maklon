<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@manajemen.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('Admin');

        // Create Manager User
        $manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@manajemen.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $manager->assignRole('Manager');

        // Create Developer User
        $developer = User::create([
            'name' => 'Developer User',
            'email' => 'developer@manajemen.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $developer->assignRole('Developer');

        // Create Client User
        $client = User::create([
            'name' => 'Client User',
            'email' => 'client@manajemen.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $client->assignRole('Client');
    }
}
