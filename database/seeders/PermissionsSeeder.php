<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Tenant;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Projects
            'projects.view','projects.create','projects.edit','projects.delete',
            // Tasks
            'tasks.view','tasks.create','tasks.edit','tasks.delete',
            // Teams
            'team.view','team.create','team.edit','team.delete',
            // Time
            'time.view','time.create','time.edit','time.delete',
            // Attachments
            'attachments.view','attachments.create','attachments.delete',
            // Notifications
            'notifications.view','notifications.update',
            // Risks
            'risks.view','risks.create','risks.edit','risks.delete',
            // Resources
            'resources.view','resources.create','resources.edit','resources.delete',
            // Reports & calendar
            'reports.view','calendar.view',
            // Budgets & invoices
            'budgets.view','budgets.create','budgets.edit',
            'invoices.approve',
        ];

        foreach ($permissions as $name) {
            Permission::findOrCreate($name);
        }

        // Roles
        $admin = Role::findOrCreate('Admin');
        // Align with new role naming
        $manager = Role::findOrCreate('Finance');
        $member = Role::findOrCreate('Member');
        $client = Role::findOrCreate('CS');

        // Assign permissions
        $admin->syncPermissions(Permission::all());

        $managerPerms = [
            'projects.view','projects.create','projects.edit',
            'tasks.view','tasks.create','tasks.edit',
            'team.view','team.create','team.edit',
            'time.view','time.create','time.edit',
            'attachments.view','attachments.create',
            'notifications.view','notifications.update',
            'risks.view','risks.create','risks.edit',
            'resources.view','resources.create','resources.edit',
            'reports.view','calendar.view',
            'budgets.view','budgets.create','budgets.edit',
            'invoices.approve',
        ];
        $manager->syncPermissions($managerPerms);

        $memberPerms = [
            'projects.view',
            'tasks.view','tasks.create','tasks.edit',
            'time.view','time.create','time.edit',
            'attachments.view','attachments.create',
            'notifications.view',
            'resources.view',
            'reports.view','calendar.view',
        ];
        $member->syncPermissions($memberPerms);

        $clientPerms = [
            'projects.view','tasks.view','reports.view','calendar.view'
        ];
        $client->syncPermissions($clientPerms);

        // Ensure a demo tenant exists and first user is Admin in that tenant
        $tenant = Tenant::firstOrCreate(['name' => 'Demo Org'], ['domain' => null]);
        $first = User::orderBy('id')->first();
        if ($first) {
            if (!$first->hasRole('Admin')) {
                $first->syncRoles(['Admin']);
            }
            if (!$first->tenant_id) {
                $first->tenant_id = $tenant->id;
                $first->save();
            }
        }
    }
}
