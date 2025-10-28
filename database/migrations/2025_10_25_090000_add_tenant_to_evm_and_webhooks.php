<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $addTenant = function (string $table) {
            if (!Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    $t->unsignedBigInteger('tenant_id')->nullable()->index()->after('id');
                });
            }
        };

        foreach (['project_baselines','evm_points','webhooks'] as $tbl) {
            if (Schema::hasTable($tbl)) {
                $addTenant($tbl);
            }
        }
    }

    public function down(): void
    {
        // Do not drop tenant columns to avoid destructive rollback in production
    }
};

