<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;

class CreateTenantCommand extends Command
{
    protected $signature = 'tenant:create {name} {--domain=}';
    protected $description = 'Create a new tenant (optionally bind a domain)';

    public function handle(): int
    {
        $name = $this->argument('name');
        $domain = $this->option('domain');
        $tenant = Tenant::create(['name' => $name, 'domain' => $domain]);
        $this->info('Tenant created: #'.$tenant->id.' '.$tenant->name.' '.($tenant->domain ?? '')); 
        return self::SUCCESS;
    }
}

