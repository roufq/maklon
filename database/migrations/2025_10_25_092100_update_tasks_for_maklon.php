<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'stage_type')) {
                $table->enum('stage_type', ['cutting','mixing','filling','packaging','qc'])->default('cutting')->after('status')->index();
            }
            if (!Schema::hasColumn('tasks', 'estimated_hours')) {
                $table->decimal('estimated_hours', 8, 2)->nullable()->after('due_date');
            }
            if (!Schema::hasColumn('tasks', 'qc_required')) {
                $table->boolean('qc_required')->default(false)->after('estimated_hours');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'stage_type')) {
                $table->dropColumn('stage_type');
            }
            if (Schema::hasColumn('tasks', 'estimated_hours')) {
                $table->dropColumn('estimated_hours');
            }
            if (Schema::hasColumn('tasks', 'qc_required')) {
                $table->dropColumn('qc_required');
            }
        });
    }
};

