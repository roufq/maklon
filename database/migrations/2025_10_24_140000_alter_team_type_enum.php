<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Align the `teams.type` column with the domain used in app logic
        // Change enum to only allow 'project_team' and 'department'
        // Use SQLite-compatible syntax
        if (DB::getDriverName() === 'sqlite') {
            // For SQLite, drop and recreate the column since MODIFY is not supported
            DB::statement("ALTER TABLE teams ADD COLUMN type_temp VARCHAR(255) NOT NULL DEFAULT 'project_team'");
            DB::statement("UPDATE teams SET type_temp = CASE WHEN type IN ('project_team','department') THEN type ELSE 'project_team' END");
            DB::statement("ALTER TABLE teams DROP COLUMN type");
            DB::statement("ALTER TABLE teams RENAME COLUMN type_temp TO type");
        } else {
            DB::statement("ALTER TABLE `teams` MODIFY `type` ENUM('project_team','department') NOT NULL DEFAULT 'project_team'");
        }
    }

    public function down(): void
    {
        // Revert to the original enum if needed
        // Use SQLite-compatible syntax
        if (DB::getDriverName() === 'sqlite') {
            // For SQLite, drop and recreate the column since MODIFY is not supported
            DB::statement("ALTER TABLE teams ADD COLUMN type_temp VARCHAR(255) NOT NULL DEFAULT 'development'");
            DB::statement("UPDATE teams SET type_temp = CASE WHEN type IN ('development','design','marketing','sales','support','management') THEN type ELSE 'development' END");
            DB::statement("ALTER TABLE teams DROP COLUMN type");
            DB::statement("ALTER TABLE teams RENAME COLUMN type_temp TO type");
        } else {
            DB::statement("ALTER TABLE `teams` MODIFY `type` ENUM('development','design','marketing','sales','support','management') NOT NULL DEFAULT 'development'");
        }
    }
};

