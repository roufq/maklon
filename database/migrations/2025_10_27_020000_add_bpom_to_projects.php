<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'bpom_registration_id')) {
                $table->foreignId('bpom_registration_id')->nullable()->after('customer_id')->constrained('bpom_registrations')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'bpom_registration_id')) {
                $table->dropConstrainedForeignId('bpom_registration_id');
            }
        });
    }
};

