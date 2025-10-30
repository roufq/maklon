<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Roles
        $adminRole = Role::findOrCreate('Admin');
        // Renamed roles per new terminology
        $financeRole = Role::findOrCreate('Finance');
        $productionRole = Role::findOrCreate('Produksi');
        $csRole = Role::findOrCreate('CS');
        // Maklon-specific roles (optional)
        $productionManagerRole = Role::findOrCreate('ProductionManager');
        $warehouseRole = Role::findOrCreate('Warehouse');
        $qcRole = Role::findOrCreate('QC');

        // Create Permissions
        $permissions = [
            // Project permissions
            'projects.view',
            'projects.create',
            'projects.edit',
            'projects.delete',

            // Task permissions
            'tasks.view',
            'tasks.create',
            'tasks.edit',
            'tasks.delete',

            // Team permissions
            'team.view',
            'team.create',
            'team.edit',
            'team.delete',

            // Report permissions
            'reports.view',
            'reports.create',
            'reports.edit',
            'reports.delete',

            // Time entries
            'time.view', 'time.create', 'time.edit', 'time.delete',

            // Attachments
            'attachments.view', 'attachments.create', 'attachments.delete',

            // Risks
            'risks.view', 'risks.create', 'risks.edit', 'risks.delete',

            // Resources (capacity planning)
            'resources.view', 'resources.create', 'resources.edit', 'resources.delete',

            // Budgets/Financials
            'budgets.view', 'budgets.create', 'budgets.edit', 'budgets.delete',
            // Invoices
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete', 'invoices.approve',

            // Calendar
            'calendar.view', 'calendar.create', 'calendar.edit', 'calendar.delete',

            // Notifications
            'notifications.view', 'notifications.update',

            // Maklon granular permissions
            'production.view','production.create','production.edit','production.delete',
            'bpom.view','bpom.create','bpom.edit','bpom.delete',
            'inventory.view','inventory.create','inventory.edit','inventory.delete',
            'qc.view','qc.create','qc.edit','qc.delete',
            'delivery.view','delivery.create','delivery.edit','delivery.delete',
            'supplier.view','supplier.create','supplier.edit','supplier.delete',
            // Tickets/chat
            'tickets.view','tickets.create','tickets.reply','tickets.close',
            // Boxes management
            'boxes.view','boxes.create','boxes.edit','boxes.delete',
            // Messages (generic messaging in context of ticket/project)
            'messages.view','messages.create',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Assign permissions to roles
        // Admin gets all permissions
        $adminRole->givePermissionTo(Permission::all());

        // Finance gets most permissions except delete (mirrors previous Manager)
        $financeRole->givePermissionTo([
            'projects.view', 'projects.create', 'projects.edit',
            'tasks.view', 'tasks.create', 'tasks.edit',
            'team.view', 'team.create', 'team.edit',
            'reports.view', 'reports.create', 'reports.edit',
            'time.view', 'time.create', 'time.edit',
            'attachments.view', 'attachments.create',
            'risks.view', 'risks.create', 'risks.edit',
            'resources.view', 'resources.create', 'resources.edit',
            'budgets.view', 'budgets.create', 'budgets.edit',
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.approve',
            'calendar.view', 'calendar.create', 'calendar.edit',
            'notifications.view', 'notifications.update',
            // Maklon baseline for Manager
            'production.view','production.create','production.edit',
            'bpom.view','bpom.create','bpom.edit',
            'inventory.view','inventory.create','inventory.edit',
            'qc.view','qc.create','qc.edit',
            'delivery.view','delivery.create','delivery.edit',
            'supplier.view','supplier.create','supplier.edit',
            // Tickets/chat
            'tickets.view','tickets.create','tickets.reply','tickets.close',
            // Boxes: typically view only for Finance
            'boxes.view',
            // Messages: view only for Finance
            'messages.view',
        ]);

        // Produksi (replacing Developer): limited project/task access
        $productionRole->syncPermissions([
            'projects.view',
            'tasks.view', 'tasks.create', 'tasks.edit',
            // Tickets/chat for collaboration
            'tickets.view','tickets.reply',
            // Boxes full management for Produksi
            'boxes.view','boxes.create','boxes.edit','boxes.delete',
            // Messages create/view for Produksi
            'messages.view','messages.create',
        ]);

        // CS (replacing Client): view and initiate tickets
        $csRole->syncPermissions([
            'projects.view',
            'tickets.view','tickets.create','tickets.reply',
            // Boxes: view only (optional)
            'boxes.view',
            // Messages create/view for CS
            'messages.view','messages.create',
        ]);

        // ProductionManager role
        $productionManagerRole->givePermissionTo([
            'production.view','production.create','production.edit','production.delete',
            'qc.view','qc.create','qc.edit',
            'bpom.view','bpom.edit',
            'inventory.view',
            'delivery.view','delivery.edit',
        ]);

        // Warehouse role
        $warehouseRole->givePermissionTo([
            'inventory.view','inventory.create','inventory.edit','inventory.delete',
            'delivery.view','delivery.create','delivery.edit',
        ]);

        // QC role
        $qcRole->givePermissionTo([
            'qc.view','qc.create','qc.edit',
            'production.view'
        ]);
    }
}
