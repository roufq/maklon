<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->after('team_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('projects', 'order_quantity')) {
                $table->integer('order_quantity')->nullable()->after('customer_id');
            }
            if (!Schema::hasColumn('projects', 'due_date')) {
                $table->date('due_date')->nullable()->after('end_date');
            }
            if (!Schema::hasColumn('projects', 'production_status')) {
                $table->enum('production_status', ['draft','scheduled','in_progress','blocked','completed'])->default('draft')->after('status')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'customer_id')) {
                $table->dropConstrainedForeignId('customer_id');
            }
            if (Schema::hasColumn('projects', 'order_quantity')) {
                $table->dropColumn('order_quantity');
            }
            if (Schema::hasColumn('projects', 'due_date')) {
                $table->dropColumn('due_date');
            }
            if (Schema::hasColumn('projects', 'production_status')) {
                $table->dropColumn('production_status');
            }
        });
    }
};

