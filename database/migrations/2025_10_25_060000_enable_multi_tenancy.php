<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('domain')->nullable()->unique();
            $table->timestamps();
        });

        $addTenant = function (string $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                if (!Schema::hasColumn($table, 'tenant_id')) {
                    $t->unsignedBigInteger('tenant_id')->nullable()->index()->after('id');
                }
            });
        };

        foreach ([
            'users','projects','tasks','teams','time_entries','attachments','notifications','resource_allocations',
            'project_budgets','budget_categories','project_expenses','risk_register','stakeholders','calendar_events',
            'invoices','invoice_items','invoice_audits'
        ] as $tbl) { $addTenant($tbl); }
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
        // keep tenant_id columns to avoid destructive down in production
    }
};

