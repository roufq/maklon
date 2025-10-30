<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tickets')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->unsignedInteger('project_sequence')->nullable()->after('project_id');
                try { $table->unique(['tenant_id','project_id','project_sequence'], 'tickets_tenant_project_seq_unique'); } catch (Throwable $e) {}
                try { $table->index(['project_id','project_sequence'], 'tickets_project_seq_idx'); } catch (Throwable $e) {}
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tickets')) {
            Schema::table('tickets', function (Blueprint $table) {
                try { $table->dropUnique('tickets_tenant_project_seq_unique'); } catch (Throwable $e) {}
                try { $table->dropIndex('tickets_project_seq_idx'); } catch (Throwable $e) {}
                $table->dropColumn('project_sequence');
            });
        }
    }
};

