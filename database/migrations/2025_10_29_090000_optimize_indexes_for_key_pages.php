<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Projects: common filters
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                try { $table->index(['tenant_id','status'], 'projects_tenant_status_idx'); } catch (Throwable $e) {}
                try { $table->index(['tenant_id','team_id'], 'projects_tenant_team_idx'); } catch (Throwable $e) {}
                try { $table->index(['tenant_id','created_at'], 'projects_tenant_created_idx'); } catch (Throwable $e) {}
            });
        }

        // Production batches
        if (Schema::hasTable('production_batches')) {
            Schema::table('production_batches', function (Blueprint $table) {
                try { $table->index(['tenant_id','qc_status'], 'batches_tenant_qc_idx'); } catch (Throwable $e) {}
                try { $table->index(['tenant_id','project_id'], 'batches_tenant_project_idx'); } catch (Throwable $e) {}
            });
        }

        // Deliveries
        if (Schema::hasTable('deliveries')) {
            Schema::table('deliveries', function (Blueprint $table) {
                try { $table->index(['tenant_id','status'], 'deliveries_tenant_status_idx'); } catch (Throwable $e) {}
                try { $table->index(['tenant_id','project_id'], 'deliveries_tenant_project_idx'); } catch (Throwable $e) {}
                try { $table->index(['tenant_id','production_batch_id'], 'deliveries_tenant_batch_idx'); } catch (Throwable $e) {}
            });
        }

        // Inventory items
        if (Schema::hasTable('inventory_items')) {
            Schema::table('inventory_items', function (Blueprint $table) {
                try { $table->index(['tenant_id','name'], 'inventory_tenant_name_idx'); } catch (Throwable $e) {}
                try { $table->index(['tenant_id','supplier_id'], 'inventory_tenant_supplier_idx'); } catch (Throwable $e) {}
            });
        }

        // Stock movements
        if (Schema::hasTable('stock_movements')) {
            Schema::table('stock_movements', function (Blueprint $table) {
                try { $table->index(['inventory_item_id','created_at','type'], 'movements_item_created_type_idx'); } catch (Throwable $e) {}
            });
        }

        // QC results
        if (Schema::hasTable('qc_results')) {
            Schema::table('qc_results', function (Blueprint $table) {
                try { $table->index(['tenant_id','status'], 'qc_tenant_status_idx'); } catch (Throwable $e) {}
                try { $table->index(['tenant_id','production_batch_id'], 'qc_tenant_batch_idx'); } catch (Throwable $e) {}
            });
        }

        // BPOM registrations
        if (Schema::hasTable('bpom_registrations')) {
            Schema::table('bpom_registrations', function (Blueprint $table) {
                try { $table->index(['tenant_id','status'], 'bpom_tenant_status_idx'); } catch (Throwable $e) {}
                try { $table->index(['tenant_id','expiry_date'], 'bpom_tenant_expiry_idx'); } catch (Throwable $e) {}
            });
        }
    }

    public function down(): void
    {
        // intentionally keep indexes (safe no-op on rollback)
    }
};

