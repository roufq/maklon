<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('calendar_events', function (Blueprint $table) {
            $table->string('external_provider')->nullable()->after('project_id');
            $table->string('external_id')->nullable()->after('external_provider');
            $table->unsignedBigInteger('sync_version')->default(0)->after('external_id');
            $table->index(['external_provider','external_id']);
        });
    }

    public function down(): void
    {
        Schema::table('calendar_events', function (Blueprint $table) {
            $table->dropIndex(['external_provider','external_id']);
            $table->dropColumn(['external_provider','external_id','sync_version']);
        });
    }
};

