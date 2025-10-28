<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'client_email')) {
                $table->string('client_email')->nullable()->after('client_name');
            }
            if (!Schema::hasColumn('invoices', 'public_token')) {
                $table->string('public_token')->nullable()->unique()->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'public_token')) {
                $table->dropUnique(['public_token']);
                $table->dropColumn('public_token');
            }
            if (Schema::hasColumn('invoices', 'client_email')) {
                $table->dropColumn('client_email');
            }
        });
    }
};

