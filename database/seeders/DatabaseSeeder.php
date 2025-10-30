<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed tenant first to satisfy FK
        $this->call(TenantSeeder::class);
        // Seed roles and permissions first
        $this->call(RoleSeeder::class);

        // Then seed users
        $this->call(UserSeeder::class);

        // Seed maklon-specific data
        $this->call(MaklonSeeder::class);

        // Seed demo for tickets/messages/boxes
        $this->call(MessagesTicketsBoxesSeeder::class);

        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
