<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            if (!Schema::hasColumn('deliveries','quantity')) {
                $table->integer('quantity')->default(0)->after('customer_id');
            }
            // Keep column type as-is (json) for compatibility; we will store plain string
        });
    }

    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            if (Schema::hasColumn('deliveries','quantity')) {
                $table->dropColumn('quantity');
            }
        });
    }
};

