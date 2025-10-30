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
        // Align with TenantSeeder ('manajemen.com') so web tenant scope matches seeded data
        $tenant = Tenant::firstOrCreate(
            ['domain' => 'manajemen.com'],
            ['name' => 'Default Tenant']
        );
        $tenantId = $tenant->id;
        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@maklon.com'],
            ['name' => 'Admin User','password' => Hash::make('password'),'email_verified_at' => now(),'tenant_id' => $tenantId]
        );
        if ($admin->tenant_id !== $tenantId) { $admin->tenant_id = $tenantId; $admin->save(); }
        $admin->assignRole('Admin');

        // Create Finance User (renamed from Manager)
        $manager = User::firstOrCreate(
            ['email' => 'manager@maklon.com'],
            ['name' => 'Finance User','password' => Hash::make('password'),'email_verified_at' => now(),'tenant_id' => $tenantId]
        );
        if ($manager->tenant_id !== $tenantId) { $manager->tenant_id = $tenantId; $manager->save(); }
        $manager->syncRoles(['Finance']);

        // Create Produksi User (renamed from Developer)
        $developer = User::firstOrCreate(
            ['email' => 'developer@maklon.com'],
            ['name' => 'Produksi User','password' => Hash::make('password'),'email_verified_at' => now(),'tenant_id' => $tenantId]
        );
        if ($developer->tenant_id !== $tenantId) { $developer->tenant_id = $tenantId; $developer->save(); }
        $developer->syncRoles(['Produksi']);

        // Create CS User (renamed from Client)
        $client = User::firstOrCreate(
            ['email' => 'client@maklon.com'],
            ['name' => 'CS User','password' => Hash::make('password'),'email_verified_at' => now(),'tenant_id' => $tenantId]
        );
        if ($client->tenant_id !== $tenantId) { $client->tenant_id = $tenantId; $client->save(); }
        $client->syncRoles(['CS']);
    }
}
