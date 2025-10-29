<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::firstOrCreate(
            ['domain' => 'manajemen.com'],
            ['name' => 'Default Tenant']
        );
        $tenantId = $tenant->id;
        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@manajemen.com'],
            ['name' => 'Admin User','password' => Hash::make('password'),'email_verified_at' => now(),'tenant_id' => $tenantId]
        );
        if (!$admin->tenant_id) { $admin->tenant_id = $tenantId; $admin->save(); }
        $admin->assignRole('Admin');

        // Create Manager User
        $manager = User::firstOrCreate(
            ['email' => 'manager@manajemen.com'],
            ['name' => 'Manager User','password' => Hash::make('password'),'email_verified_at' => now(),'tenant_id' => $tenantId]
        );
        if (!$manager->tenant_id) { $manager->tenant_id = $tenantId; $manager->save(); }
        $manager->assignRole('Manager');

        // Create Developer User
        $developer = User::firstOrCreate(
            ['email' => 'developer@manajemen.com'],
            ['name' => 'Developer User','password' => Hash::make('password'),'email_verified_at' => now(),'tenant_id' => $tenantId]
        );
        if (!$developer->tenant_id) { $developer->tenant_id = $tenantId; $developer->save(); }
        $developer->assignRole('Developer');

        // Create Client User
        $client = User::firstOrCreate(
            ['email' => 'client@manajemen.com'],
            ['name' => 'Client User','password' => Hash::make('password'),'email_verified_at' => now(),'tenant_id' => $tenantId]
        );
        if (!$client->tenant_id) { $client->tenant_id = $tenantId; $client->save(); }
        $client->assignRole('Client');
    }
}
