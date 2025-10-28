<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\PermissionsSeeder;

class SyncPermissionsCommand extends Command
{
    protected $signature = 'app:sync-permissions {--assign-admin-to-first : Ensure first user is Admin}';
    protected $description = 'Sync Spatie roles and permissions for the application';

    public function handle(): int
    {
        $this->callSilent('cache:forget', ['key' => 'spatie.permission.cache']);
        $this->info('Syncing permissions and roles...');
        (new PermissionsSeeder())->run();
        $this->info('Done.');
        return self::SUCCESS;
    }
}

