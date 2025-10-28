<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // bpom_registrations: make registration_number unique per-tenant (instead of global)
        if (Schema::hasTable('bpom_registrations')) {
            Schema::table('bpom_registrations', function (Blueprint $table) {
                // Drop global unique if exists, then add composite unique
                try { $table->dropUnique('bpom_registrations_registration_number_unique'); } catch (\Throwable $e) { /* ignore */ }
                try { $table->unique(['tenant_id','registration_number'], 'bpom_registrations_tenant_regnum_unique'); } catch (\Throwable $e) { /* ignore */ }
            });
        }

        // production_batches: add index for expiry_date to support reporting/alerts
        if (Schema::hasTable('production_batches')) {
            Schema::table('production_batches', function (Blueprint $table) {
                try { $table->index('expiry_date', 'production_batches_expiry_date_index'); } catch (\Throwable $e) { /* ignore */ }
            });
        }

        // suppliers: add lookup index per tenant for name
        if (Schema::hasTable('suppliers')) {
            Schema::table('suppliers', function (Blueprint $table) {
                try { $table->index(['tenant_id','name'], 'suppliers_tenant_name_index'); } catch (\Throwable $e) { /* ignore */ }
            });
        }

        // inventory_items: add lookup and low-stock related indexes
        if (Schema::hasTable('inventory_items')) {
            Schema::table('inventory_items', function (Blueprint $table) {
                try { $table->index(['tenant_id','name'], 'inventory_items_tenant_name_index'); } catch (\Throwable $e) { /* ignore */ }
                try { $table->index('min_stock', 'inventory_items_min_stock_index'); } catch (\Throwable $e) { /* ignore */ }
            });
        }

        // stock_movements: add polymorphic reference and created_at indexes
        if (Schema::hasTable('stock_movements')) {
            Schema::table('stock_movements', function (Blueprint $table) {
                try { $table->index(['reference_type','reference_id'], 'stock_movements_reference_index'); } catch (\Throwable $e) { /* ignore */ }
                try { $table->index('created_at', 'stock_movements_created_at_index'); } catch (\Throwable $e) { /* ignore */ }
            });
        }

        // work_stations: add name lookup per tenant
        if (Schema::hasTable('work_stations')) {
            Schema::table('work_stations', function (Blueprint $table) {
                try { $table->index(['tenant_id','name'], 'work_stations_tenant_name_index'); } catch (\Throwable $e) { /* ignore */ }
            });
        }

        // qc_results: add status and created_at indexes for filtering/history
        if (Schema::hasTable('qc_results')) {
            Schema::table('qc_results', function (Blueprint $table) {
                try { $table->index('status', 'qc_results_status_index'); } catch (\Throwable $e) { /* ignore */ }
                try { $table->index('created_at', 'qc_results_created_at_index'); } catch (\Throwable $e) { /* ignore */ }
            });
        }
    }

    public function down(): void
    {
        // Non-destructive: do not attempt to revert indexes/uniques in production
        // This migration is safe-forward only to avoid dropping critical constraints.
    }
};

