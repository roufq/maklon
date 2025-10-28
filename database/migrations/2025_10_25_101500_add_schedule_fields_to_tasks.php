<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks','work_station_id')) {
                $table->foreignId('work_station_id')->nullable()->after('project_id')->constrained('work_stations')->nullOnDelete();
            }
            if (!Schema::hasColumn('tasks','scheduled_start')) {
                $table->dateTime('scheduled_start')->nullable()->after('due_date');
            }
            if (!Schema::hasColumn('tasks','scheduled_end')) {
                $table->dateTime('scheduled_end')->nullable()->after('scheduled_start');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks','work_station_id')) { $table->dropConstrainedForeignId('work_station_id'); }
            if (Schema::hasColumn('tasks','scheduled_start')) { $table->dropColumn('scheduled_start'); }
            if (Schema::hasColumn('tasks','scheduled_end')) { $table->dropColumn('scheduled_end'); }
        });
    }
};

