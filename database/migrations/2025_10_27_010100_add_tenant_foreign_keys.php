<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            // Core and Maklon-critical tables
            'projects', 'tasks', 'teams', 'time_entries', 'attachments', 'notifications', 'resource_allocations',
            'project_budgets', 'budget_categories', 'project_expenses', 'risk_register', 'stakeholders', 'calendar_events',
            'invoices', 'invoice_items', 'invoice_audits',
            'project_baselines', 'evm_points', 'webhooks',
            'bpom_registrations', 'production_batches', 'boms',
            'suppliers', 'inventory_items', 'stock_movements', 'work_stations', 'quality_checkpoints', 'qc_results',
            'deliveries'
        ];

        // Cleanup invalid tenant references before adding FK constraints
        foreach ($tables as $tbl) {
            if (!Schema::hasTable($tbl) || !Schema::hasColumn($tbl, 'tenant_id')) {
                continue;
            }
            try {
                DB::statement("UPDATE `$tbl` SET `tenant_id` = NULL WHERE `tenant_id` IS NOT NULL AND `tenant_id` NOT IN (SELECT `id` FROM `tenants`)");
            } catch (\Throwable $e) {
                // Fallback for non-MySQL drivers (e.g., sqlite)
                try {
                    DB::table($tbl)->whereNotIn('tenant_id', function ($query) {
                        $query->select('id')->from('tenants');
                    })->update(['tenant_id' => null]);
                } catch (\Throwable $e2) {
                    // ignore; leave as-is if driver doesn't support
                }
            }
        }

        // Add FK constraints with nullOnDelete for safety
        foreach ($tables as $tbl) {
            if (!Schema::hasTable($tbl) || !Schema::hasColumn($tbl, 'tenant_id')) {
                continue;
            }
            try {
                Schema::table($tbl, function (Blueprint $table) use ($tbl) {
                    // avoid duplicate constraint by checking index existence not available here; rely on try/catch
                    $table->foreign('tenant_id', $tbl.'_tenant_id_foreign')->references('id')->on('tenants')->nullOnDelete();
                });
            } catch (\Throwable $e) {
                // ignore if already exists
            }
        }
    }

    public function down(): void
    {
        // Non-destructive: keep FKs; dropping may be risky in production.
    }
};

