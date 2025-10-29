<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_items','description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('inventory_items','min_stock_level')) {
                $table->integer('min_stock_level')->default(0)->after('current_stock');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_items','min_stock_level')) {
                $table->dropColumn('min_stock_level');
            }
            if (Schema::hasColumn('inventory_items','description')) {
                $table->dropColumn('description');
            }
        });
    }
};

