<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->index('created_by');
            $table->index('team_id');
        });

        Schema::table('team_user', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('team_id');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->index('assigned_to');
            $table->index('project_id');
        });

        Schema::table('stakeholders', function (Blueprint $table) {
            $table->index('email');
            $table->index('project_id');
        });

        Schema::table('time_entries', function (Blueprint $table) {
            if (Schema::hasColumn('time_entries','user_id')) $table->index('user_id');
            if (Schema::hasColumn('time_entries','project_id')) $table->index('project_id');
            if (Schema::hasColumn('time_entries','start_time')) $table->index('start_time');
        });

        Schema::table('resource_allocations', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('project_id');
            $table->index(['start_date','end_date']);
        });

        Schema::table('project_budgets', function (Blueprint $table) {
            $table->index('project_id');
        });

        Schema::table('project_expenses', function (Blueprint $table) {
            $table->index('project_id');
            $table->index('spent_at');
        });
    }

    public function down(): void
    {
        // For simplicity, skip dropping indexes since names may vary per driver.
    }
};

