<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles and permissions are seeded for tests
        if (Role::count() === 0) {
            $this->seed(\Database\Seeders\RoleSeeder::class);
        }

        // Ensure tenant exists for multi-tenant tests
        if (\App\Models\Tenant::count() === 0) {
            $this->seed(\Database\Seeders\TenantSeeder::class);
        }
    }
}
